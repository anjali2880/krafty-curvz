@extends('layouts.app')

@section('title', 'About Us')
@section('meta_description', 'Learn about ' . ($siteSettings->site_name ?? 'Krafty Curvz') . ', our story, values, and handmade resin creations.')
@section('canonical', route('about'))

@section('content')
<section
    class="relative py-20 bg-cover bg-center bg-no-repeat"
    style="background-image: linear-gradient(120deg, rgba(15, 24, 35, 0.52), rgba(96, 57, 26, 0.48)), url('{{ !empty($siteSettings->banner_background) ? asset('storage/' . $siteSettings->banner_background) : 'https://images.unsplash.com/photo-1579547945413-497e1b99dac0?auto=format&fit=crop&w=2000&q=80' }}');"
>
    <div class="absolute inset-0 bg-gradient-to-b from-black/20 to-black/35"></div>
    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-6xl font-bold text-white mb-5">Our Creative Journey</h1>
        <p class="text-lg md:text-2xl text-white/90 max-w-3xl mx-auto">Where passion meets creativity in every handcrafted piece.</p>
    </div>
</section>

<section class="py-16 bg-[#f5f2ed]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
        <div>
            <h2 class="text-4xl font-bold text-[#5f3c2a] mb-6">Our Story</h2>
            <div class="space-y-4 text-[#4a4a4a] leading-8">
                <p>{{ $siteSettings->site_name ?? 'Krafty Curvz' }} started as a small passion project and quickly became a full creative studio for handmade resin products, scented candles, and pipe cleaner crafts.</p>
                <p>From custom gifts to statement decor pieces, we focus on details, quality, and designs that feel personal and memorable.</p>
                <p>Every product is crafted with care to bring warmth, beauty, and joy to your homes and special occasions.</p>
            </div>
        </div>

        <div class="rounded-2xl overflow-hidden shadow-large border border-white/60 bg-white">
            <img
                src="https://images.unsplash.com/photo-1610701596061-2ecf227e85b2?auto=format&fit=crop&w=1400&q=80"
                alt="Resin artwork"
                class="w-full h-[420px] object-cover"
            >
        </div>
    </div>
</section>

<section class="py-16 bg-[#f5f2ed]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-soft px-6 py-12 md:px-12">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-[#5f3c2a]">Our Core Values</h2>
                <div class="w-20 h-0.5 bg-amber-500 mx-auto mt-4"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-6 rounded-xl hover:bg-amber-50/40 transition-colors duration-300">
                    <div class="text-3xl mb-3">✨</div>
                    <h3 class="text-2xl font-semibold text-[#5f3c2a] mb-3">Creativity First</h3>
                    <p class="text-gray-600">We experiment with styles and techniques to craft truly unique pieces.</p>
                </div>

                <div class="text-center p-6 rounded-xl hover:bg-amber-50/40 transition-colors duration-300">
                    <div class="text-3xl mb-3">🌿</div>
                    <h3 class="text-2xl font-semibold text-[#5f3c2a] mb-3">Sustainable Care</h3>
                    <p class="text-gray-600">We focus on responsible materials and thoughtful production choices.</p>
                </div>

                <div class="text-center p-6 rounded-xl border border-amber-100 shadow-soft bg-white">
                    <div class="text-3xl mb-3">🤲</div>
                    <h3 class="text-2xl font-semibold text-[#5f3c2a] mb-3">Handcrafted Quality</h3>
                    <p class="text-gray-600">Every order gets personal attention from our skilled craft team.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-[#f5f2ed]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-[#5f3c2a]">Meet Our Artists</h2>
            <div class="w-20 h-0.5 bg-amber-500 mx-auto mt-4"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-xl shadow-soft overflow-hidden text-center">
                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=900&q=80" alt="Artist 1" class="w-full h-72 object-cover">
                <div class="p-6">
                    <h3 class="text-3xl font-semibold text-[#5f3c2a]">Anjali Mehta</h3>
                    <p class="text-amber-600 font-semibold mt-1">Founder</p>
                    <p class="text-gray-600 mt-4">Specializes in resin trays and ocean-inspired handcrafted designs.</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-soft overflow-hidden text-center">
                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=900&q=80" alt="Artist 2" class="w-full h-72 object-cover">
                <div class="p-6">
                    <h3 class="text-3xl font-semibold text-[#5f3c2a]">Shubham Mehta</h3>
                    <p class="text-amber-600 font-semibold mt-1">Resin Specialist</p>
                    <p class="text-gray-600 mt-4">Creates layered geode styles and signature finish effects.</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-soft overflow-hidden text-center">
                <img src="https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=900&q=80" alt="Artist 3" class="w-full h-72 object-cover">
                <div class="p-6">
                    <h3 class="text-3xl font-semibold text-[#5f3c2a]">Pallavi Mehta</h3>
                    <p class="text-amber-600 font-semibold mt-1">Design Innovator</p>
                    <p class="text-gray-600 mt-4">Brings modern concepts into candles and floral craft collections.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
