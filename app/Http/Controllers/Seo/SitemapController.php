<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [
            [
                'loc' => route('home'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => route('about'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
            [
                'loc' => route('products.index'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
        ];

        $categories = Category::query()
            ->where('is_active', true)
            ->select(['slug', 'updated_at'])
            ->get();

        foreach ($categories as $category) {
            $urls[] = [
                'loc' => route('category.show', $category->slug),
                'lastmod' => optional($category->updated_at)->toDateString() ?? now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        $products = Product::query()
            ->where('is_active', true)
            ->select(['slug', 'updated_at'])
            ->latest('updated_at')
            ->get();

        foreach ($products as $product) {
            $urls[] = [
                'loc' => route('products.show', $product->slug),
                'lastmod' => optional($product->updated_at)->toDateString() ?? now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        return response()
            ->view('sitemap.index', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
