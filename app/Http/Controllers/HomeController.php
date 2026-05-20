<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
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

        return view('home', compact('featuredProducts', 'categories', 'newArrivals'));
    }
}
