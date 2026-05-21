<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        $products = $request->user()
            ->wishlistProducts()
            ->with(['category', 'images', 'sizes'])
            ->latest('wishlists.created_at')
            ->paginate(12);

        return view('wishlist', [
            'products' => $products,
        ]);
    }

    public function toggle(Request $request, Product $product): RedirectResponse
    {
        $userId = (int) $request->user()->id;

        $existing = Wishlist::query()
            ->where('user_id', $userId)
            ->where('product_id', (int) $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Removed from wishlist.');
        }

        Wishlist::query()->create([
            'user_id' => $userId,
            'product_id' => (int) $product->id,
        ]);

        return back()->with('success', 'Added to wishlist.');
    }
}

