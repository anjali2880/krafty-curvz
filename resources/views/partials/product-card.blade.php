@props(['product'])
<div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
    @php $isOutOfStock = $product->manage_stock && (int) ($product->stock_quantity ?? 0) <= 0; @endphp
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
