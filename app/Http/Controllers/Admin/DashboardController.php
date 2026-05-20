<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $pendingOrders = Order::where('status', 'pending')->count();
        $recentOrders = Order::with('items')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalOrders', 'totalProducts', 'totalCategories',
            'totalRevenue', 'pendingOrders', 'recentOrders'
        ));
    }
}
