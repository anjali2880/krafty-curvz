<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    private const FALLBACK_MAX_ORDER_QUANTITY = 10;

    private function resolveMaxOrderQuantity(Product $product): int
    {
        $max = (int) ($product->max_order_quantity ?? self::FALLBACK_MAX_ORDER_QUANTITY);
        return $max > 0 ? $max : self::FALLBACK_MAX_ORDER_QUANTITY;
    }

    private function resolveAvailableStock(Product $product): ?int
    {
        if (!$product->manage_stock) {
            return null;
        }

        return max(0, (int) ($product->stock_quantity ?? 0));
    }

    public function index(): View
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'order_type' => 'nullable|in:normal,send_item',
            'item_description' => 'nullable|string|max:1000',
            'custom_note' => 'nullable|string|max:1000',
            'size_id' => 'nullable|exists:product_sizes,id',
            'size_name' => 'nullable|string',
            'size_price' => 'nullable|numeric|min:0',
            'shape_option' => 'nullable|string|max:50',
            'customization_data' => 'nullable|string',
            'customization_image' => 'nullable|string',
        ]);

        $product = Product::with('images')->findOrFail($request->product_id);
        $canUseResinOrderType = $product->canUseResinOrderType();
        $maxOrderQuantity = $this->resolveMaxOrderQuantity($product);
        $availableStock = $this->resolveAvailableStock($product);
        $quantity = $request->quantity ?? 1;

        if ($availableStock !== null && $availableStock <= 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This product is currently out of stock.');
        }
        
        // Use size price if provided, otherwise use product price
        $price = $request->size_price ?? $product->effective_price;
        $sizeName = $request->size_name ?? null;
        $sizeId = $request->size_id ?? null;
        $shapeOption = $request->shape_option ?? null;
        $allowedShapeOptions = collect($product->shape_options ?? [])
            ->filter(fn ($shape) => is_string($shape) && trim($shape) !== '')
            ->map(fn ($shape) => trim($shape))
            ->values()
            ->all();

        if ($product->has_shape_options && empty($shapeOption)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please select a shape.');
        }

        if ($product->has_shape_options && !in_array($shapeOption, $allowedShapeOptions, true)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'The selected shape is invalid for this product.');
        }

        $cart = session()->get('cart', []);
        $cartKey = $product->id . '-' . ($sizeId ?? 'standard') . '-' . ($shapeOption ?? 'standard') . '-' . ($request->customization_data ? md5($request->customization_data) : 'standard');
        $maxAllowedQuantity = $availableStock !== null
            ? min($maxOrderQuantity, $availableStock)
            : $maxOrderQuantity;

        if ($quantity > $maxAllowedQuantity) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Only ' . $maxAllowedQuantity . ' item(s) can be added for this product.');
        }

        if (isset($cart[$cartKey])) {
            $nextQuantity = $cart[$cartKey]['quantity'] + $quantity;
            if ($nextQuantity > $maxAllowedQuantity) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'You can only keep up to ' . $maxAllowedQuantity . ' item(s) for this product in your cart.');
            }
            $cart[$cartKey]['quantity'] = $nextQuantity;
            $cart[$cartKey]['max_order_quantity'] = $maxOrderQuantity;
            $cart[$cartKey]['available_stock'] = $availableStock;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $price,
                'quantity' => $quantity,
                'max_order_quantity' => $maxOrderQuantity,
                'available_stock' => $availableStock,
                'image' => $product->primary_image_url,
                'size_id' => $sizeId,
                'size_name' => $sizeName,
                'shape_option' => $shapeOption,
                'is_customized' => !empty($request->customization_data),
                'customization_data' => $request->customization_data ? json_decode($request->customization_data, true) : null,
                'customization_image' => $request->customization_image,
            ];
        }

        session()->put('cart', $cart);
        session()->put('checkout_order_type', $canUseResinOrderType ? $request->input('order_type', 'normal') : 'normal');
        session()->put('checkout_item_description', $canUseResinOrderType ? $request->input('item_description') : null);
        session()->put('checkout_custom_note', $canUseResinOrderType ? $request->input('custom_note') : null);

        return redirect()->route('cart.index')
            ->with('success', 'Product added to cart!');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'cart_key' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->cart_key])) {
            $item = $cart[$request->cart_key];
            $product = Product::query()->find($item['product_id'] ?? null);
            $maxOrderQuantity = $product
                ? $this->resolveMaxOrderQuantity($product)
                : (int) ($item['max_order_quantity'] ?? self::FALLBACK_MAX_ORDER_QUANTITY);
            $availableStock = $product
                ? $this->resolveAvailableStock($product)
                : ($item['available_stock'] ?? null);

            $maxOrderQuantity = $maxOrderQuantity > 0 ? $maxOrderQuantity : self::FALLBACK_MAX_ORDER_QUANTITY;
            $maxAllowedQuantity = $availableStock !== null
                ? min($maxOrderQuantity, max(0, (int) $availableStock))
                : $maxOrderQuantity;

            if ($maxAllowedQuantity <= 0) {
                return redirect()->route('cart.index')
                    ->with('error', 'This product is out of stock and cannot be updated.');
            }

            if ((int) $request->quantity > $maxAllowedQuantity) {
                return redirect()->route('cart.index')
                    ->with('error', 'Maximum available quantity for this product is ' . $maxAllowedQuantity . '.');
            }

            $cart[$request->cart_key]['quantity'] = (int) $request->quantity;
            $cart[$request->cart_key]['max_order_quantity'] = $maxOrderQuantity;
            $cart[$request->cart_key]['available_stock'] = $availableStock;
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')
            ->with('success', 'Cart updated!');
    }

    public function remove(Request $request): RedirectResponse
    {
        $request->validate([
            'cart_key' => 'required|string',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->cart_key])) {
            unset($cart[$request->cart_key]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')
            ->with('success', 'Item removed from cart!');
    }

    public function clear(): RedirectResponse
    {
        session()->forget('cart');
        return redirect()->route('cart.index')
            ->with('success', 'Cart cleared!');
    }
}
