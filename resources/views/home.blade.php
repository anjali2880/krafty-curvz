@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section
    class="py-20 {{ $siteSettings->banner_background ? 'bg-cover bg-center bg-no-repeat relative' : 'bg-gray-100' }}"
    @if($siteSettings->banner_background)
        style="background-image: linear-gradient(120deg, rgba(12, 16, 24, 0.65), rgba(20, 24, 32, 0.45)), url('{{ asset('storage/' . $siteSettings->banner_background) }}');"
    @endif
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold {{ $siteSettings->banner_background ? 'text-white' : 'text-gray-900' }} mb-4">
                Welcome to {{ $siteSettings->site_name ?? 'Krafty Curvz' }}
            </h1>
            <p class="text-lg {{ $siteSettings->banner_background ? 'text-white/90' : 'text-gray-600' }} max-w-2xl mx-auto mb-8">
                Discover our beautiful collection of handmade resin art pieces, crafted with love and attention to detail.
            </p>
            <a href="{{ route('products.index') }}" class="inline-block bg-amber-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-amber-700 transition-colors">
                Shop Now
            </a>
        </div>
    </div>
</section>

<!-- Categories Section -->
@if($categories->count() > 0)
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Shop by Category</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
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
                        <div class="p-6 flex-1 flex flex-col">
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
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">New Arrivals</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Fresh pieces just added to our collection
            </p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($newArrivals as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Featured Products -->
@if($featuredProducts->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Featured Products</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Check out our handpicked favorites from the collection
            </p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($featuredProducts as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
        <div class="text-center mt-12">
            <a href="{{ route('products.index') }}" class="inline-block bg-amber-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-amber-700 transition-colors">
                View All Products
            </a>
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="py-20 bg-amber-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">
            Looking for something custom?
        </h2>
        <p class="text-xl text-amber-100 mb-8 max-w-2xl mx-auto">
            We create personalized resin pieces tailored to your preferences. Get in touch with us to discuss your custom order.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
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
</section>
@endsection
