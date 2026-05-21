<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Wishlist;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProducts = Product::with(['category', 'images'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::where('is_active', true)
            ->where('parent_id', null)
            ->whereIn('slug', ['resin-products', 'candles', 'pipe-cleaner-crafts'])
            ->with(['children'])
            ->get();

        $newArrivals = Product::with(['category', 'images'])
            ->where('is_active', true)
            ->latest()
            ->take(4)
            ->get();

        $wishlistProductIds = [];
        if (auth()->check()) {
            $wishlistProductIds = Wishlist::query()
                ->where('user_id', auth()->id())
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return view('home', compact('featuredProducts', 'categories', 'newArrivals', 'wishlistProductIds'));
    }
}
