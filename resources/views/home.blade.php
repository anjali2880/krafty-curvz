@extends('layouts.app')

@section('title', 'Home')
@section('meta_description', 'Discover handmade resin art, scented candles & personalised gifts by Krafty Curvz. Shop coasters, keychains, photo frames & custom pieces crafted with love.')
@section('canonical', route('home'))
@php
    $homeOgImage = $siteSettings->banner_background
        ? asset('storage/' . $siteSettings->banner_background)
        : ($siteSettings->logo ? asset('storage/' . $siteSettings->logo) : '');
@endphp
@section('og_image', $homeOgImage)

@section('content')
<!-- Hero Section -->
<section
    class="relative flex items-center py-12 md:py-24 min-h-[300px] sm:min-h-[360px] md:min-h-[540px] {{ $siteSettings->banner_background ? 'bg-cover bg-center bg-no-repeat' : 'bg-gray-100' }}"
    @if($siteSettings->banner_background)
        style="background-image: linear-gradient(120deg, rgba(12, 16, 24, 0.55), rgba(20, 24, 32, 0.40)), url('{{ asset('storage/' . $siteSettings->banner_background) }}');"
    @endif
>
    @if($siteSettings->banner_background)
        <div class="absolute inset-0 bg-gradient-to-b from-black/25 to-black/45"></div>
    @endif
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center kc-hero-fade relative">
            <p class="inline-flex items-center px-4 py-1 rounded-full border border-white/25 bg-white/5 text-xs sm:text-sm uppercase tracking-[0.22em] {{ $siteSettings->banner_background ? 'text-white/90' : 'text-gray-700' }} font-semibold">
                Handmade. Personal. Timeless.
            </p>
            <h1 class="mt-4 text-3xl sm:text-4xl md:text-6xl font-bold leading-tight {{ $siteSettings->banner_background ? 'text-white' : 'text-gray-900' }}" style="font-family: 'Cormorant Garamond', serif;">
                Handcrafted Resin Art That Preserves Your Precious Memories
            </h1>
            <p class="mt-4 text-base md:text-xl {{ $siteSettings->banner_background ? 'text-white/90' : 'text-gray-600' }} max-w-3xl mx-auto" style="font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, 'Apple Color Emoji', 'Segoe UI Emoji';">
                Personalized keepsakes, gifts, and decor crafted with care. From custom name pieces to resin preservation, we turn moments into art.
            </p>
            <div class="mt-6 md:mt-8 flex flex-col sm:flex-row gap-3 md:gap-4 justify-center">
                <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center bg-amber-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-amber-700 transition-colors">
                    Shop Collection
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
@if($categories->count() > 0)
<section class="py-10 md:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-gray-900 mb-7 md:mb-12">Shop by Category</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 md:gap-8">
            @foreach($categories->where('parent_id', null) as $mainCategory)
                <a href="{{ route('category.show', $mainCategory->slug) }}" class="group">
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden h-full flex flex-col">
                        <div class="bg-gray-100 w-full aspect-square overflow-hidden flex items-center justify-center">
                            @if($mainCategory->image)
                                <img
                                    src="{{ asset('storage/' . $mainCategory->image) }}"
                                    alt="{{ $mainCategory->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                >
                            @else
                                <svg class="w-16 h-16 text-gray-600 group-hover:text-amber-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            @endif
                        </div>
                            <div class="p-5 md:p-6 flex-1 flex flex-col">
                            <h3 class="font-semibold text-gray-800 group-hover:text-amber-600 transition-colors text-lg mb-2">{{ $mainCategory->name }}</h3>
                            <p class="text-sm text-gray-600 mb-3">{{ $mainCategory->description }}</p>
                            @if($mainCategory->children->count() > 0)
                                <div class="flex flex-wrap items-center gap-2 mt-auto">
                                    @foreach($mainCategory->children->take(3) as $subcategory)
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ $subcategory->name }}</span>
                                    @endforeach
                                    @if($mainCategory->children->count() > 3)
                                        <span class="text-xs text-amber-600">+{{ $mainCategory->children->count() - 3 }} more</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- New Arrivals -->
@if($newArrivals->count() > 0)
<section class="py-10 md:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-7 md:mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3 md:mb-4">New Arrivals</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Fresh pieces just added to our collection
            </p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 md:gap-8">
            @foreach($newArrivals as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Featured Products -->
@if($featuredProducts->count() > 0)
<section class="py-10 md:py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-7 md:mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3 md:mb-4">Featured Products</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Check out our handpicked favorites from the collection
            </p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 md:gap-8">
            @foreach($featuredProducts as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
        <div class="text-center mt-8 md:mt-12">
            <a href="{{ route('products.index') }}" class="inline-block bg-amber-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-amber-700 transition-colors">
                View All Products
            </a>
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section
    class="relative py-10 md:py-20 bg-cover bg-center bg-no-repeat"
    style="background-image: url('{{ asset('images/ourstory.png') }}');"
>
    <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/55 to-black/65"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="relative">
        <h2 class="text-2xl md:text-3xl font-bold text-white mb-3 md:mb-4">
            Looking for something custom?
        </h2>
        <p class="text-base md:text-xl text-white/90 mb-6 md:mb-8 max-w-2xl mx-auto">
            We create personalized resin pieces tailored to your preferences. Get in touch with us to discuss your custom order.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 md:gap-4 justify-center">
            <a href="mailto:{{ $siteSettings->contact_email ?? 'contact@kraftycurvz.com' }}" class="inline-block bg-white text-amber-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                Email Us
            </a>
            @if($siteSettings->whatsapp_number)
                <a href="https://wa.me/{{ $siteSettings->whatsapp_number }}" target="_blank" class="inline-block bg-green-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-600 transition-colors">
                    Chat on WhatsApp
                </a>
            @endif
        </div>
        </div>
    </div>
</section>
@endsection
