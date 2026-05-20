<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    public function index(Request $request, ?string $categorySlug = null): View
    {
        $query = Product::with(['category', 'images'])->where('is_active', true);
        $activeCategorySlug = $categorySlug ?? $request->category;

        if (!empty($activeCategorySlug)) {
            $category = Category::where('slug', $activeCategorySlug)->first();
            
            if ($category) {
                if ($category->parent_id === null) {
                    // Main category - include products from main category and all subcategories
                    $categoryIds = [$category->id];
                    if ($category->children->count() > 0) {
                        $categoryIds = array_merge($categoryIds, $category->children->pluck('id')->toArray());
                    }
                    $query->whereIn('category_id', $categoryIds);
                } else {
                    // Subcategory - only include products from this subcategory
                    $query->where('category_id', $category->id);
                }
            }
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->latest();
                    break;
                case 'name':
                    $query->orderBy('name', 'asc');
                    break;
                default:
                    $query->latest();
            }
        } else {
                $query->latest();
        }

        $products = $query->paginate(12)->appends($request->query());
        $categories = Category::where('is_active', true)->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show(string $slug): View|RedirectResponse
    {
        $product = Product::with(['category', 'images', 'sizes'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            $legacyProduct = Product::query()
                ->where('legacy_slug', $slug)
                ->where('is_active', true)
                ->first();

            if ($legacyProduct) {
                return redirect()->route('products.show', ['slug' => $legacyProduct->slug], 301);
            }

            // Fallback for previously clean URLs when unique slug suffixes were added later.
            $prefixMatch = Product::query()
                ->where('slug', 'like', $slug . '-%')
                ->where('is_active', true)
                ->orderByDesc('id')
                ->first();

            if ($prefixMatch) {
                return redirect()->route('products.show', ['slug' => $prefixMatch->slug], 301);
            }

            abort(404);
        }

        $relatedProducts = Product::with(['images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        $existingCustomizationData = null;
        $existingCustomizationImage = null;

        $savedCustomizations = session()->get('saved_customizations', []);
        $savedCustomization = $savedCustomizations[(int) $product->id] ?? null;
        if (is_array($savedCustomization) && !empty($savedCustomization['design_data'])) {
            $existingCustomizationData = $savedCustomization['design_data'];
            $existingCustomizationImage = $savedCustomization['design_image'] ?? null;
        }

        $cart = session()->get('cart', []);

        if (empty($existingCustomizationData)) {
            foreach (array_reverse($cart, true) as $item) {
                if ((int) ($item['product_id'] ?? 0) !== (int) $product->id) {
                    continue;
                }

                if (!empty($item['customization_data'])) {
                    $existingCustomizationData = $item['customization_data'];
                    $existingCustomizationImage = $item['customization_image'] ?? null;
                    break;
                }
            }
        }

        return view('products.show', compact(
            'product',
            'relatedProducts',
            'existingCustomizationData',
            'existingCustomizationImage'
        ));
    }
}
