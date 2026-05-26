@extends('layouts.app')

@section('title', 'Wishlist')
@section('robots', 'noindex, follow')

@section('content')
<section class="py-14 md:py-20 bg-[#f5f2ed]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-6 mb-8">
            <div>
                <p class="text-xs uppercase tracking-[0.25em] text-amber-700 font-semibold">Account</p>
                <h1 class="text-3xl md:text-4xl font-bold text-[#5f3c2a] mt-2">Wishlist</h1>
                <p class="mt-2 text-gray-600">Save your favorite items and come back anytime.</p>
            </div>
            <a href="{{ route('products.index') }}" class="hidden sm:inline-flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white font-semibold px-7 py-3 rounded-full transition-colors">
                Continue Shopping
            </a>
        </div>

        @if(!empty($products) && $products->count() > 0)
            @php $wishlistProductIds = $products->pluck('id')->map(fn ($id) => (int) $id)->all(); @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                    @include('partials.product-card', ['product' => $product, 'wishlistProductIds' => $wishlistProductIds])
                @endforeach
            </div>
            <div class="mt-10">
                {{ $products->links() }}
            </div>
        @else
            <div class="bg-white border border-amber-100 rounded-2xl shadow-soft p-8 md:p-12 text-center">
                <h2 class="text-2xl font-bold text-[#5f3c2a]">Your wishlist is empty</h2>
                <p class="mt-3 text-gray-600 max-w-2xl mx-auto">Tap the heart icon on any product to save it here.</p>
                <div class="mt-8">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white font-semibold px-8 py-3 rounded-full transition-colors">
                        Browse Products
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
