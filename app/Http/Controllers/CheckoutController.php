<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SiteSettings;
use App\Services\Payment\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    private const RESIN_KEY_PHRASES = [
        'preserve my memory',
        'use my own flowers',
        'resin preservation',
        'preserve gift',
        'preserve jewellery',
        'preserve ashes',
        'use my own item',
        'sentimental item',
        'memory keepsake',
        'special personal item',
    ];

    private function cartSupportsSendItem(array $cart): bool
    {
        $productIds = collect($cart)
            ->pluck('product_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($productIds->isEmpty()) {
            return false;
        }

        $products = Product::query()
            ->with('category')
            ->whereIn('id', $productIds->all())
            ->get();

        foreach ($products as $product) {
            if ($product->canUseResinOrderType()) {
                return true;
            }
        }

        return false;
    }

    public function index(): View
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty!');
        }

        $cartSupportsSendItem = $this->cartSupportsSendItem($cart);

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $prefillOrderType = session('checkout_order_type', 'normal');
        $prefillItemDescription = session('checkout_item_description');
        $prefillCustomNote = session('checkout_custom_note');
        $prefillCustomerName = '';
        $prefillCustomerEmail = '';
        $prefillCustomerPhone = '';
        $prefillShippingAddress = '';

        if (auth()->check()) {
            $user = auth()->user();
            $latestOrder = Order::query()
                ->where('user_id', $user->id)
                ->latest()
                ->first();

            $prefillCustomerName = (string) ($user->name ?? '');
            $prefillCustomerEmail = (string) ($user->email ?? '');
            $prefillCustomerPhone = (string) ($latestOrder->customer_phone ?? '');
            $prefillShippingAddress = (string) ($latestOrder->shipping_address ?? '');
        }

        return view('checkout.index', compact(
            'cart',
            'subtotal',
            'prefillOrderType',
            'prefillItemDescription',
            'prefillCustomNote',
            'prefillCustomerName',
            'prefillCustomerEmail',
            'prefillCustomerPhone',
            'prefillShippingAddress'
        ));
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:500',
            'order_type' => 'nullable|in:normal,send_item',
            'item_description' => 'nullable|string|max:1000',
            'custom_note' => 'nullable|string|max:1000',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty!');
        }

        $cartSupportsSendItem = $this->cartSupportsSendItem($cart);

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $combinedText = Str::lower(implode(' ', array_filter([
            $validated['notes'] ?? null,
            $validated['item_description'] ?? null,
            $validated['custom_note'] ?? null,
        ])));

        $detectedSendItem = false;
        foreach (self::RESIN_KEY_PHRASES as $phrase) {
            if (str_contains($combinedText, Str::lower($phrase))) {
                $detectedSendItem = true;
                break;
            }
        }

        $effectiveOrderType = $validated['order_type']
            ?? session('checkout_order_type')
            ?? 'normal';
        $effectiveItemDescription = $validated['item_description']
            ?? session('checkout_item_description');
        $effectiveCustomNote = $validated['custom_note']
            ?? session('checkout_custom_note');

        $customerWillSendItem = $cartSupportsSendItem
            && ($effectiveOrderType === 'send_item' || $detectedSendItem);

        if ($customerWillSendItem && empty(trim((string) ($effectiveItemDescription ?? '')))) {
            return back()
                ->withInput()
                ->withErrors(['item_description' => 'Item description is required for resin preservation orders.']);
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'shipping_address' => $validated['shipping_address'],
            'notes' => $validated['notes'] ?? null,
            'customer_will_send_item' => $customerWillSendItem,
            'item_description' => $effectiveItemDescription,
            'custom_note' => $effectiveCustomNote,
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'status' => $customerWillSendItem ? 'waiting_for_customer_parcel' : 'pending',
            'payment_method' => 'whatsapp',
            'payment_status' => 'unpaid',
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'total' => $item['price'] * $item['quantity'],
                'is_customized' => $item['is_customized'] ?? false,
                'customization_data' => $item['customization_data'] ?? null,
                'customization_image' => $item['customization_image'] ?? null,
                'size_id' => $item['size_id'] ?? null,
                'size_name' => $item['size_name'] ?? null,
                'shape_option' => $item['shape_option'] ?? null,
            ]);
        }

        // Process payment via payment manager (currently WhatsApp/null gateway)
        $paymentManager = app(PaymentManager::class);
        $paymentManager->initiatePayment([
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'amount' => $order->total,
        ]);

        session()->forget('cart');
        session()->forget('checkout_order_type');
        session()->forget('checkout_item_description');
        session()->forget('checkout_custom_note');

        return redirect()->route('order.confirmation', $order->order_number)
            ->with('success', 'Order placed successfully!');
    }

    public function confirmation(string $orderNumber): View
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();
        $settings = SiteSettings::getSettings();
        $whatsappNumber = $settings->whatsapp_number ?: config('payment.whatsapp_number', '');

        return view('checkout.confirmation', compact('order', 'whatsappNumber'));
    }

    public function myOrders(): View
    {
        $orders = Order::query()
            ->where('user_id', auth()->id())
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('orders.my-orders', compact('orders'));
    }

    public function showMyOrder(Order $order): View
    {
        if ((int) $order->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $order->load(['items.product.images']);

        return view('orders.show', compact('order'));
    }

    public function uploadParcelDetails(Request $request, Order $order): RedirectResponse
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->customer_will_send_item) {
            return back()->with('error', 'Parcel details are not required for this order.');
        }

        $validated = $request->validate([
            'courier_agency_name' => 'required|string|max:255',
            'tracking_number' => 'required|string|max:255',
            'parcel_slip' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'parcel_additional_notes' => 'nullable|string|max:1000',
        ]);

        $slipPath = $request->file('parcel_slip')->store('parcel-slips', 'public');

        $order->update([
            'courier_agency_name' => $validated['courier_agency_name'],
            'tracking_number' => $validated['tracking_number'],
            'parcel_slip_path' => $slipPath,
            'parcel_additional_notes' => $validated['parcel_additional_notes'] ?? null,
            'parcel_details_submitted_at' => now(),
            'status' => 'parcel_shipped_by_customer',
        ]);

        return back()->with('success', 'Parcel details uploaded successfully.');
    }
}
