@extends('layouts.app')

@section('title', 'Contact Us')
@section('meta_description', 'Get in touch with ' . ($siteSettings->site_name ?? 'Krafty Curvz') . ' for custom resin products, candles, and handmade craft orders. We reply fast!')
@section('canonical', route('contact'))
@php
    $contactOgImage = $siteSettings->banner_background
        ? asset('storage/' . $siteSettings->banner_background)
        : ($siteSettings->logo ? asset('storage/' . $siteSettings->logo) : '');
@endphp
@section('og_image', $contactOgImage)

@push('head')
@php
    $localBizSchema = array_filter([
        '@context'    => 'https://schema.org',
        '@type'       => 'LocalBusiness',
        'name'        => $siteSettings->site_name ?? 'Krafty Curvz',
        'url'         => url('/'),
        'image'       => $siteSettings->logo ? asset('storage/' . $siteSettings->logo) : null,
        'description' => 'Handmade resin art, scented candles & creative gifts crafted with love.',
        'email'       => !empty($siteSettings->contact_email) ? $siteSettings->contact_email : null,
        'telephone'   => !empty($siteSettings->whatsapp_number) ? '+' . preg_replace('/\D+/', '', $siteSettings->whatsapp_number) : null,
        'sameAs'      => array_values(array_filter([
            !empty($siteSettings->instagram_url) ? $siteSettings->instagram_url : null,
        ])) ?: null,
    ]);
@endphp
<script type="application/ld+json">{!! json_encode($localBizSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
@endpush

@section('content')
<section
    class="relative flex items-center py-20 md:py-24 min-h-[420px] md:min-h-[540px] bg-cover bg-center bg-no-repeat"
    style="background-image: linear-gradient(120deg, rgba(12, 16, 24, 0.55), rgba(20, 24, 32, 0.40)), url('{{ !empty($siteSettings->banner_background) ? asset('storage/' . $siteSettings->banner_background) : 'https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=2000&q=80' }}');"
>
    <div class="absolute inset-0 bg-gradient-to-b from-black/25 to-black/45"></div>
    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold text-white mb-4">Contact {{ $siteSettings->site_name ?? 'Krafty Curvz' }}</h1>
        <p class="text-lg text-white max-w-2xl mx-auto mb-8">Share your custom idea, gifting plan, or resin preservation request and our team will help you quickly.</p>
    </div>
</section>

<section class="py-14 md:py-20 pb-20 md:pb-24 bg-[#f5f2ed]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 md:gap-6">
            <div class="bg-white border border-amber-100 rounded-2xl shadow-soft p-6 md:p-7">
                <p class="text-xs uppercase tracking-[0.2em] text-amber-700 font-semibold">Email</p>
                @if(!empty($siteSettings->contact_email))
                    <a href="mailto:{{ $siteSettings->contact_email }}" class="mt-4 inline-block text-lg md:text-xl font-semibold text-neutral-900 hover:text-amber-700 transition-colors break-all">{{ $siteSettings->contact_email }}</a>
                @else
                    <p class="mt-4 text-neutral-600">Add contact email from Admin Settings.</p>
                @endif
            </div>

            <div class="bg-white border border-amber-100 rounded-2xl shadow-soft p-6 md:p-7">
                <p class="text-xs uppercase tracking-[0.2em] text-amber-700 font-semibold">Phone</p>
                @if(!empty($siteSettings->contact_phone))
                    <a href="tel:{{ preg_replace('/\s+/', '', $siteSettings->contact_phone) }}" class="mt-4 inline-block text-lg md:text-xl font-semibold text-neutral-900 hover:text-amber-700 transition-colors">{{ $siteSettings->contact_phone }}</a>
                @else
                    <p class="mt-4 text-neutral-600">Add phone number from Admin Settings.</p>
                @endif
            </div>

            <div class="bg-white border border-amber-100 rounded-2xl shadow-soft p-6 md:p-7">
                <p class="text-xs uppercase tracking-[0.2em] text-amber-700 font-semibold">WhatsApp</p>
                @if(!empty($siteSettings->whatsapp_number))
                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $siteSettings->whatsapp_number) }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex items-center text-lg md:text-xl font-semibold text-green-700 hover:text-green-800 transition-colors">
                        <span>Chat Instantly</span>
                    </a>
                @else
                    <p class="mt-4 text-neutral-600">Add WhatsApp number from Admin Settings.</p>
                @endif
            </div>
        </div>

        <div class="mt-8 md:mt-12 space-y-6 md:space-y-8">
            <div class="bg-white border border-amber-100 rounded-2xl shadow-soft p-6 md:p-8">
                <h2 class="text-3xl font-bold text-[#5f3c2a]">Send Us a Message</h2>
                <p class="mt-3 text-gray-600 leading-7">Tell us what you are looking for. For faster response, include product name, quantity, and preferred timeline.</p>

                <form class="mt-7 md:mt-8 grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5" action="#" method="POST">
                    <div>
                        <label class="block text-sm font-semibold text-neutral-700 mb-2">Full Name</label>
                        <input type="text" class="w-full border border-amber-200 rounded-xl px-4 py-3 text-neutral-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all" placeholder="Enter your name">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-neutral-700 mb-2">Phone Number</label>
                        <input type="text" class="w-full border border-amber-200 rounded-xl px-4 py-3 text-neutral-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all" placeholder="Enter phone number">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-neutral-700 mb-2">Email Address</label>
                        <input type="email" class="w-full border border-amber-200 rounded-xl px-4 py-3 text-neutral-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all" placeholder="Enter email address">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-neutral-700 mb-2">Message</label>
                        <textarea rows="5" class="w-full border border-amber-200 rounded-xl px-4 py-3 text-neutral-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all" placeholder="Describe your custom request..."></textarea>
                    </div>
                    <div class="md:col-span-2 pt-1">
                        <button type="button" class="inline-flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors duration-300 shadow-medium">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white border border-amber-100 rounded-2xl shadow-soft p-6 md:p-7">
                    <h3 class="text-2xl font-bold text-[#5f3c2a]">Visit / Pickup Details</h3>
                    @if(!empty($siteSettings->contact_address))
                        <p class="mt-4 text-gray-700 leading-7">{{ $siteSettings->contact_address }}</p>
                    @else
                        <p class="mt-4 text-gray-600">Add your address in Admin Settings to display it here.</p>
                    @endif
                </div>

                <div class="bg-white border border-amber-100 rounded-2xl shadow-soft p-6 md:p-7">
                    <h3 class="text-2xl font-bold text-[#5f3c2a]">Need Quick Help?</h3>
                    <p class="mt-3 text-gray-600">For urgent support related to custom orders, connect on WhatsApp and share your order number or design idea.</p>
                    @if(!empty($siteSettings->whatsapp_number))
                        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $siteSettings->whatsapp_number) }}" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex items-center justify-center w-full bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-full transition-colors">
                            Open WhatsApp Chat
                        </a>
                    @else
                        <p class="mt-4 text-gray-600">Add WhatsApp number from Admin Settings.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
