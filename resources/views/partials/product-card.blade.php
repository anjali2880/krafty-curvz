@props(['product'])
<div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
    @php $isOutOfStock = $product->manage_stock && (int) ($product->stock_quantity ?? 0) <= 0; @endphp
    @php
        $wishlistIds = $wishlistProductIds ?? [];
        $isWishlisted = auth()->check() && in_array((int) $product->id, $wishlistIds, true);
    @endphp
    <!-- Badge for customizable products -->
    @if($product->customizable_product)
    @endif
    
    <!-- Product Image -->
    <a href="{{ route('products.show', $product->slug) }}" class="block relative overflow-hidden">
        <div class="aspect-square bg-gray-100">
            <img src="{{ $product->primary_image_url }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
        </div>
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
        @if($isOutOfStock)
            <span class="absolute top-2 left-2 bg-red-600 text-white text-xs font-semibold px-2 py-1 rounded">Out of Stock</span>
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
