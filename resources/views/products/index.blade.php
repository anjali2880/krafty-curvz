@extends('layouts.app')

@section('title', 'Shop')
@section('meta_description', 'Browse our full collection of handmade resin art, scented candles, keychains, photo frames & custom gifts. Free custom orders available at Krafty Curvz.')
@section('canonical', route('products.index'))
@php
    $shopOgImage = $siteSettings->banner_background
        ? asset('storage/' . $siteSettings->banner_background)
        : ($siteSettings->logo ? asset('storage/' . $siteSettings->logo) : '');
@endphp
@section('og_image', $shopOgImage)
@section('og_type', 'website')

@push('head')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": "Shop | {{ $siteSettings->site_name }}",
  "description": "Browse our full collection of handmade resin art, scented candles, keychains, photo frames & custom gifts.",
  "url": "{{ route('products.index') }}",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "Home", "item": "{{ route('home') }}"},
      {"@type": "ListItem", "position": 2, "name": "Shop", "item": "{{ route('products.index') }}"}
    ]
  }
}
</script>
@endpush

@section('content')
<!-- Shop Header -->
<section class="relative flex items-center py-10 md:py-24 min-h-[420px] md:min-h-[540px] overflow-hidden {{ !empty($siteSettings->banner_background) ? 'bg-cover bg-center bg-no-repeat' : 'bg-gray-100' }}"
         @if(!empty($siteSettings->banner_background))
             style="background-image: linear-gradient(120deg, rgba(12, 16, 24, 0.55), rgba(20, 24, 32, 0.40)), url('{{ asset('storage/' . $siteSettings->banner_background) }}');"
         @endif>
    <div class="absolute inset-0 bg-gradient-to-b from-black/25 to-black/45"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative text-center">
            <h1 class="text-4xl font-bold {{ !empty($siteSettings->banner_background) ? 'text-white' : 'text-gray-900' }} mb-4">
                Shop Collection
            </h1>
            <p class="text-lg {{ !empty($siteSettings->banner_background) ? 'text-white' : 'text-gray-600' }} max-w-2xl mx-auto">
                Discover our beautiful collection of handmade resin art pieces
            </p>
        </div>
    </div>
</section>

<!-- Shop Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Filters Sidebar -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Filters</h2>
                
                <form id="filters-form" method="GET" action="{{ route('products.index') }}" class="space-y-6">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search products..." 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} class="mr-2">
                                <span>All Categories</span>
                            </label>
                            @foreach($categories->where('parent_id', null) as $mainCategory)
                                <div class="border-t pt-2 mt-2">
                                    <label class="flex items-center font-semibold text-gray-800">
                                        <input type="radio" name="category" value="{{ $mainCategory->slug }}" {{ request('category') == $mainCategory->slug ? 'checked' : '' }} class="mr-2">
                                        <span>{{ $mainCategory->name }}</span>
                                    </label>
                                    @if($mainCategory->children->count() > 0)
                                        <div class="ml-4 mt-1 space-y-1">
                                            @foreach($mainCategory->children as $subcategory)
                                                <label class="flex items-center text-sm text-gray-600">
                                                    <input type="radio" name="category" value="{{ $subcategory->slug }}" {{ request('category') == $subcategory->slug ? 'checked' : '' }} class="mr-2">
                                                    <span>{{ $subcategory->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                        <div class="flex items-center space-x-2">
                            <input type="number" 
                                   name="min_price" 
                                   value="{{ request('min_price') }}" 
                                   placeholder="Min" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <span class="text-gray-500">-</span>
                            <input type="number" 
                                   name="max_price" 
                                   value="{{ request('max_price') }}" 
                                   placeholder="Max" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>

                    <!-- Sort -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select name="sort" class="filter-auto-submit w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Default</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price (Low to High)</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price (High to Low)</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        </select>
                    </div>
                    
                    @if(request()->hasAny(['search', 'category', 'min_price', 'max_price', 'sort']))
                        <a href="{{ route('products.index') }}" class="block w-full text-center text-gray-600 hover:text-gray-800 text-sm">
                            Clear Filters
                        </a>
                    @endif
                </form>
            </div>
        </aside>

        <!-- Products Grid -->
        <div class="flex-1">
            @if($products->count() > 0)
                <!-- Results Header -->
                <div class="flex justify-between items-center mb-6">
                    <p class="text-gray-600">{{ $products->total() }} products found</p>
                    
                    <!-- Sort Dropdown (Mobile) -->
                    <div class="lg:hidden">
                        <select name="sort_mobile" class="border border-gray-300 rounded-md px-3 py-2">
                            <option value="">Sort</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price (Low to High)</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price (High to Low)</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        @php $isOutOfStock = $product->manage_stock && (int) ($product->stock_quantity ?? 0) <= 0; @endphp
                        @php
                            $wishlistIds = $wishlistProductIds ?? [];
                            $isWishlisted = auth()->check() && in_array((int) $product->id, $wishlistIds, true);
                        @endphp
                        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                            <!-- Badge for customizable products -->
                            @if($product->customizable_product)
                            @endif
                            
                            <!-- Product Image -->
                            <a href="{{ route('products.show', $product->slug) }}" class="block relative overflow-hidden">
                                <div class="aspect-square bg-gray-100">
                                    @if($product->images->count() > 0)
                                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                             alt="{{ $product->name }}" 
                                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                @if($isOutOfStock)
                                    <span class="absolute top-2 left-2 bg-red-600 text-white text-xs font-semibold px-2 py-1 rounded">Out of Stock</span>
                                @endif

                                @if(auth()->check())
                                    <form method="POST" action="{{ route('wishlist.toggle', $product->id) }}" class="absolute top-2 right-2 z-10">
                                        @csrf
                                        <button type="submit" class="w-10 h-10 rounded-full bg-white/90 hover:bg-white border border-amber-100 hover:border-amber-200 flex items-center justify-center transition-all">
                                            @if($isWishlisted)
                                                <svg class="w-5 h-5 text-amber-700" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 21s-7.2-4.35-9.6-8.55C.9 9.75 2.1 6.9 4.65 5.85c1.8-.75 3.9-.3 5.25 1.05L12 8.1l2.1-1.2c1.35-1.35 3.45-1.8 5.25-1.05 2.55 1.05 3.75 3.9 2.25 6.6C19.2 16.65 12 21 12 21z"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-neutral-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364 4.318 12.682a4.5 4.5 0 010-6.364z"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="absolute top-2 right-2 z-10 w-10 h-10 rounded-full bg-white/90 hover:bg-white border border-amber-100 hover:border-amber-200 flex items-center justify-center transition-all" title="Login to wishlist">
                                        <svg class="w-5 h-5 text-neutral-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364 4.318 12.682a4.5 4.5 0 010-6.364z"/>
                                        </svg>
                                    </a>
                                @endif
                            </a>
                            
                            <!-- Product Info -->
                            <div class="p-4">
                                @if($product->category)
                                    <p class="text-sm text-amber-600 font-medium mb-1">{{ $product->category->name }}</p>
                                @endif
                                
                                <a href="{{ route('products.show', $product->slug) }}" class="block">
                                    <h3 class="font-semibold text-gray-800 hover:text-amber-600 transition-colors mb-2">
                                        {{ $product->name }}
                                    </h3>
                                </a>
                                
                                <!-- Price -->
                                <div class="flex items-center justify-between">
                                    <div>
                                        @if($product->sale_price)
                                            <span class="text-lg font-bold text-amber-600">&#8377;{{ number_format($product->sale_price, 0) }}</span>
                                            <span class="text-sm text-gray-500 line-through ml-2">&#8377;{{ number_format($product->price, 0) }}</span>
                                        @else
                                            <span class="text-lg font-bold text-gray-800">&#8377;{{ number_format($product->effective_price, 0) }}</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Quick view button -->
                                    <button class="text-gray-400 hover:text-amber-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                
                                @if($product->sizes->count() > 0)
                                    <p class="text-xs text-gray-500 mt-2">{{ $product->sizes->count() }} sizes available</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12">
                    {{ $products->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No products found</h3>
                    <p class="text-gray-600 mb-6">Try adjusting your filters or search terms</p>
                    <a href="{{ route('products.index') }}" class="inline-block bg-amber-600 text-white px-6 py-2 rounded-md hover:bg-amber-700 transition-colors">
                        Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const form = document.getElementById('filters-form');
        if (!form) return;

        // Auto-submit for radio/select filters
        form.querySelectorAll('input[name="category"], select[name="sort"], input[name="min_price"], input[name="max_price"]').forEach((el) => {
            el.addEventListener('change', () => form.submit());
        });

        // Search submit on Enter/change for reliable behavior
        const searchInput = form.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('change', () => form.submit());
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    form.submit();
                }
            });
        }

        // Mobile sort dropdown should preserve existing query params
        const mobileSort = document.querySelector('select[name="sort_mobile"]');
        if (mobileSort) {
            mobileSort.addEventListener('change', function () {
                const url = new URL(window.location.href);
                if (this.value) {
                    url.searchParams.set('sort', this.value);
                } else {
                    url.searchParams.delete('sort');
                }
                window.location.href = url.toString();
            });
        }
    })();
</script>
@endpush
