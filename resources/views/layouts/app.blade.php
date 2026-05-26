<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @php
        $_seoPage  = $__env->yieldContent('title', '');
        $_seoDesc  = $__env->yieldContent('meta_description', 'Beautiful handmade resin products by ' . $siteSettings->site_name . ' – Coasters, Trays, Keychains, Wall Art & Custom Gifts');
        $_seoCanon = ltrim($__env->yieldContent('canonical', '')) ?: url()->current();
        $_seoOgImg = ltrim($__env->yieldContent('og_image', '')) ?: ($siteSettings->logo ? asset('storage/' . $siteSettings->logo) : '');
        $_seoOgTyp = $__env->yieldContent('og_type', 'website');
        $_seoRobots= $__env->yieldContent('robots', 'index, follow');
        $_seoTitle = (empty($_seoPage) || $_seoPage === 'Home')
            ? $siteSettings->site_name . ' | Handmade Resin Art & Custom Gifts'
            : $_seoPage . ' | ' . $siteSettings->site_name;

        /* --- Global JSON-LD payloads --- */
        $_orgSchema = array_filter([
            '@context'     => 'https://schema.org',
            '@type'        => 'Organization',
            'name'         => $siteSettings->site_name,
            'url'          => url('/'),
            'logo'         => $siteSettings->logo ? ['@type' => 'ImageObject', 'url' => asset('storage/' . $siteSettings->logo)] : null,
            'contactPoint' => !empty($siteSettings->contact_email)
                ? ['@type' => 'ContactPoint', 'contactType' => 'customer support', 'email' => $siteSettings->contact_email]
                : null,
            'sameAs'       => array_values(array_filter([
                !empty($siteSettings->instagram_url) ? $siteSettings->instagram_url : null,
                !empty($siteSettings->whatsapp_number)
                    ? 'https://wa.me/' . preg_replace('/\D+/', '', $siteSettings->whatsapp_number)
                    : null,
            ])) ?: null,
        ]);
        $_siteSchema = [
            '@context' => 'https://schema.org',
            '@type'    => 'WebSite',
            'name'     => $siteSettings->site_name,
            'url'      => url('/'),
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => ['@type' => 'EntryPoint', 'urlTemplate' => route('products.index') . '?search={search_term_string}'],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    @endphp

    {{-- Favicon --}}
    @if($siteSettings->favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $siteSettings->favicon) }}">
    @endif

    {{-- Core meta --}}
    <title>{{ $_seoTitle }}</title>
    <meta name="description" content="{{ $_seoDesc }}">
    <link rel="canonical" href="{{ $_seoCanon }}">
    <meta name="robots" content="{{ $_seoRobots }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Open Graph --}}
    <meta property="og:type"        content="{{ $_seoOgTyp }}">
    <meta property="og:site_name"   content="{{ $siteSettings->site_name }}">
    <meta property="og:locale"      content="en_IN">
    <meta property="og:title"       content="{{ $_seoTitle }}">
    <meta property="og:description" content="{{ $_seoDesc }}">
    <meta property="og:url"         content="{{ $_seoCanon }}">
    @if($_seoOgImg)
    <meta property="og:image"       content="{{ $_seoOgImg }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height"content="630">
    <meta property="og:image:alt"   content="{{ $_seoTitle }}">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $_seoTitle }}">
    <meta name="twitter:description" content="{{ $_seoDesc }}">
    @if($_seoOgImg)
    <meta name="twitter:image"       content="{{ $_seoOgImg }}">
    @endif

    {{-- Global Structured Data --}}
    <script type="application/ld+json">{!! json_encode($_siteSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
    <script type="application/ld+json">{!! json_encode($_orgSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>

	    @vite(['resources/css/app.css', 'resources/js/app.js'])
	    @stack('head')
	    @stack('styles')

        <!-- Slick (for premium UI transitions) -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css">

        <style>
            /* Premium fullscreen search UI */
            .kc-search-overlay { position: fixed; inset: 0; z-index: 2147483647; display: none; }
            .kc-search-overlay[aria-hidden="false"] { display: block; }

            .kc-search-backdrop {
                position: absolute;
                inset: 0;
                background:
                    radial-gradient(1200px 700px at 50% 35%, rgba(255, 216, 160, 0.12), rgba(0, 0, 0, 0.78)),
                    linear-gradient(120deg, rgba(12, 16, 24, 0.70), rgba(10, 12, 18, 0.82));
                -webkit-backdrop-filter: blur(14px);
                backdrop-filter: blur(14px);
                opacity: 0;
                transition: opacity 260ms ease;
            }

            .kc-search-stage {
                position: relative;
                width: 100%;
                height: 100%;
                display: grid;
                place-items: center;
                padding: 20px;
                opacity: 0;
                transform: translateY(10px) scale(1.02);
                transition: opacity 260ms ease, transform 260ms ease;
            }

            body.kc-search-open { overflow: hidden; }
            body.kc-search-open .kc-search-backdrop { opacity: 1; }
            body.kc-search-open .kc-search-stage { opacity: 1; transform: translateY(0) scale(1); }

            .kc-search-panel {
                width: min(920px, 100%);
                border-radius: 28px;
                border: 1px solid rgba(255, 255, 255, 0.12);
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.10), rgba(255, 255, 255, 0.05));
                box-shadow: 0 30px 80px rgba(0, 0, 0, 0.45), 0 10px 28px rgba(0, 0, 0, 0.28);
                -webkit-backdrop-filter: blur(18px);
                backdrop-filter: blur(18px);
                overflow: hidden;
            }

            .kc-search-panel-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 18px 22px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.10);
            }
            .kc-search-title {
                font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                font-size: 12px;
                color: rgba(255, 255, 255, 0.80);
            }

            .kc-search-close {
                width: 44px;
                height: 44px;
                border-radius: 999px;
                border: 1px solid rgba(255, 255, 255, 0.18);
                background: rgba(255, 255, 255, 0.10);
                color: rgba(255, 255, 255, 0.90);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: transform 200ms ease, background 200ms ease, border-color 200ms ease, color 200ms ease, box-shadow 200ms ease;
            }
            .kc-search-close:hover {
                transform: rotate(10deg) translateY(-1px);
                background: rgba(239, 68, 68, 0.18);
                border-color: rgba(239, 68, 68, 0.35);
                color: rgba(255, 255, 255, 0.98);
                box-shadow: 0 10px 25px rgba(239, 68, 68, 0.18);
            }

            .kc-search-body { padding: 26px 22px 24px; }
            .kc-search-lead {
                font-family: "Cormorant Garamond", ui-serif, Georgia, Cambria, "Times New Roman", Times, serif;
                font-size: clamp(24px, 3.2vw, 36px);
                font-weight: 700;
                color: rgba(255, 255, 255, 0.95);
                line-height: 1.1;
                text-align: center;
                margin: 6px 0 18px;
            }
            .kc-search-sub {
                font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
                text-align: center;
                color: rgba(255, 255, 255, 0.70);
                max-width: 54ch;
                margin: 0 auto 22px;
                font-size: 14px;
            }

            .kc-search-form { width: min(760px, 100%); margin: 0 auto; }

            .kc-search-input-wrap {
                position: relative;
                border-radius: 999px;
                border: 1px solid rgba(255, 255, 255, 0.18);
                background: rgba(255, 255, 255, 0.10);
                -webkit-backdrop-filter: blur(18px);
                backdrop-filter: blur(18px);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 199, 125, 0.08), 0 0 36px rgba(255, 168, 88, 0.10);
                transition: transform 220ms ease, box-shadow 220ms ease, border-color 220ms ease;
            }
            .kc-search-input-wrap:focus-within {
                transform: scale(1.01);
                border-color: rgba(251, 191, 36, 0.55);
                box-shadow: 0 14px 38px rgba(0, 0, 0, 0.34), 0 0 0 4px rgba(245, 158, 11, 0.18), 0 0 55px rgba(245, 158, 11, 0.16);
            }

            .kc-search-input {
                width: 100%;
                border: 0;
                outline: none;
                background: transparent;
                padding: 18px 58px 18px 22px;
                color: rgba(255, 255, 255, 0.96);
                font-size: 16px;
                font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
            }
            .kc-search-input::placeholder { color: rgba(255, 255, 255, 0.55); }

            .kc-search-submit {
                position: absolute;
                top: 50%;
                right: 10px;
                transform: translateY(-50%);
                width: 44px;
                height: 44px;
                border-radius: 999px;
                border: 1px solid rgba(255, 255, 255, 0.16);
                background: linear-gradient(135deg, rgba(245, 158, 11, 0.95), rgba(180, 83, 9, 0.95));
                color: white;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 12px 30px rgba(245, 158, 11, 0.22);
                transition: transform 200ms ease, box-shadow 200ms ease, filter 200ms ease;
            }
            .kc-search-submit:hover {
                transform: translateY(-50%) translateY(-1px);
                box-shadow: 0 16px 34px rgba(245, 158, 11, 0.28);
                filter: saturate(1.05);
            }

            .kc-search-tags { width: min(760px, 100%); margin: 18px auto 0; }
            .kc-tag {
                display: inline-flex !important;
                align-items: center;
                gap: 8px;
                padding: 10px 14px;
                border-radius: 999px;
                border: 1px solid rgba(255, 255, 255, 0.16);
                background: rgba(255, 255, 255, 0.08);
                color: rgba(255, 255, 255, 0.85);
                font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
                font-size: 13px;
                transition: transform 180ms ease, background 180ms ease, border-color 180ms ease, box-shadow 180ms ease;
                cursor: pointer;
                user-select: none;
                white-space: nowrap;
            }
            .kc-tag:hover {
                transform: translateY(-2px);
                background: rgba(255, 255, 255, 0.12);
                border-color: rgba(251, 191, 36, 0.30);
                box-shadow: 0 12px 22px rgba(0, 0, 0, 0.22);
            }
            .kc-tag-dot {
                width: 8px;
                height: 8px;
                border-radius: 99px;
                background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.9), rgba(245, 158, 11, 0.9));
                box-shadow: 0 0 16px rgba(245, 158, 11, 0.32);
            }

            /* Slick spacing tweaks */
            .kc-search-tags .slick-slide { margin: 0 8px; }
            .kc-search-tags .slick-list { margin: 0 -8px; }
            /* Center suggestions when there are only a few tags */
            .kc-search-tags .slick-list { display: flex; justify-content: center; }
            .kc-search-tags .slick-track { display: flex; align-items: center; justify-content: center; margin-left: auto !important; margin-right: auto !important; }
            .kc-search-tags .slick-slide { height: auto; }
            .kc-search-tags .slick-list { padding: 0 22px; }

            @media (max-width: 640px) {
                .kc-search-panel { border-radius: 22px; }
                .kc-search-body { padding: 22px 16px 18px; }
                .kc-search-panel-head { padding: 16px 16px; }
                .kc-search-input { padding: 16px 54px 16px 18px; }
            }
        </style>
	</head>
<body class="bg-neutral-50 text-neutral-900 min-h-screen antialiased">
    <!-- Modern Header -->
    <header class="bg-[#fff7eb]/95 border-b border-amber-100/80 backdrop-blur-md shadow-soft sticky top-0 left-0 right-0 z-50 transition-all duration-300">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 md:h-20 items-center">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                        @if($siteSettings->logo)
                            <div class="relative">
                                <img src="{{ asset('storage/' . $siteSettings->logo) }}" alt="{{ $siteSettings->site_name }}" class="h-11 md:h-14 object-contain transition-transform duration-300 group-hover:scale-110">
                                <div class="absolute inset-0 bg-primary-400/20 rounded-lg blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>
                        @endif
                        @if($siteSettings->show_site_name)
                            <span class="hidden sm:inline text-2xl tracking-wide font-bold text-[#5f3c2a]" style="font-family: 'Cormorant Garamond', serif;">{{ $siteSettings->site_name }}</span>
                        @endif
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center gap-10">
                    <div class="flex items-center gap-8" style="font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, 'Apple Color Emoji', 'Segoe UI Emoji';">
                    <a href="{{ route('home') }}" class="relative pb-2 text-neutral-800 hover:text-amber-700 font-medium transition-all duration-300 group">
                        <span>Home</span>
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-amber-700 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('products.index') }}" class="relative pb-2 text-neutral-800 hover:text-amber-700 font-medium transition-all duration-300 group">
                        <span>Shop</span>
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-amber-700 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('about') }}" class="relative pb-2 text-neutral-800 hover:text-amber-700 font-medium transition-all duration-300 group">
                        <span>About</span>
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-amber-700 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('contact') }}" class="relative pb-2 text-neutral-800 hover:text-amber-700 font-medium transition-all duration-300 group">
                        <span>Contact</span>
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-amber-700 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    </div>
                    
                    <!-- Icons -->
                    <div class="flex items-center gap-3">
                        @php
                            $cartCount = count(session()->get('cart', []));
                            $wishlistCount = auth()->check()
                                ? \App\Models\Wishlist::query()->where('user_id', auth()->id())->count()
                                : 0;
                        @endphp
                        <button type="button" onclick="openSearch()"
                                class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/70 hover:bg-white border border-amber-100 hover:border-amber-200 text-neutral-800 hover:text-amber-700 transition-all duration-300"
                                aria-label="Search">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                        <a href="{{ auth()->check() ? route('wishlist') : route('login') }}" class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/70 hover:bg-white border border-amber-100 hover:border-amber-200 text-neutral-800 hover:text-amber-700 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364 4.318 12.682a4.5 4.5 0 010-6.364z"/>
                            </svg>
                            @if($wishlistCount > 0)
                                <span class="absolute -top-2 -right-2 bg-amber-700 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold">
                                    {{ $wishlistCount > 99 ? '99+' : $wishlistCount }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('cart.index') }}" class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/70 hover:bg-white border border-amber-100 hover:border-amber-200 text-neutral-800 hover:text-amber-700 transition-all duration-300 group">
                            <svg class="w-6 h-6 transition-transform duration-300 group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                            </svg>
                            @if($cartCount > 0)
                                <span class="absolute -top-2 -right-2 bg-amber-700 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold">{{ $cartCount }}</span>
                            @endif
                        </a>
                    </div>
                    
                    @if(auth()->check())
                        <a href="{{ route('orders.my') }}" class="relative pb-2 text-neutral-800 hover:text-amber-700 font-medium transition-all duration-300 group" style="font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, 'Apple Color Emoji', 'Segoe UI Emoji';">
                            <span>My Orders</span>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-amber-700 transition-all duration-300 group-hover:w-full"></span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="relative pb-2 text-neutral-800 hover:text-amber-700 font-medium transition-all duration-300 group" style="font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, 'Apple Color Emoji', 'Segoe UI Emoji';">
                                <span>Logout</span>
                                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-amber-700 transition-all duration-300 group-hover:w-full"></span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="relative pb-2 text-neutral-800 hover:text-amber-700 font-medium transition-all duration-300 group" style="font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, 'Apple Color Emoji', 'Segoe UI Emoji';">
                            <span>Login</span>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-amber-700 transition-all duration-300 group-hover:w-full"></span>
                        </a>
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 hover:shadow-medium">
                            Register
                        </a>
                    @endif
                </div>

	                <!-- Mobile Menu Button -->
		                <div class="lg:hidden flex items-center gap-2">
                        <button type="button" onclick="openSearch()" class="relative inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/70 hover:bg-white border border-amber-100 hover:border-amber-200 text-neutral-800 hover:text-amber-700 transition-all duration-300" aria-label="Search">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
	                        <a href="{{ auth()->check() ? route('wishlist') : route('login') }}" class="relative inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/70 hover:bg-white border border-amber-100 hover:border-amber-200 text-neutral-800 hover:text-amber-700 transition-all duration-300" aria-label="Wishlist">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364 4.318 12.682a4.5 4.5 0 010-6.364z"/>
                            </svg>
                            @if($wishlistCount > 0)
                                <span class="absolute -top-2 -right-2 bg-amber-700 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold">
                                    {{ $wishlistCount > 99 ? '99+' : $wishlistCount }}
                                </span>
                            @endif
                        </a>
		                    <a href="{{ route('cart.index') }}" class="relative inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/70 hover:bg-white border border-amber-100 hover:border-amber-200 text-neutral-800 hover:text-amber-700 transition-all duration-300" aria-label="Cart">
	                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
	                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
	                        </svg>
	                        @if($cartCount > 0)
	                            <span class="absolute -top-2 -right-2 bg-amber-700 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold">{{ $cartCount }}</span>
	                        @endif
	                    </a>
                    <button onclick="toggleMobileMenu()" class="text-neutral-700 hover:text-primary-600 transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </nav>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden lg:hidden bg-white/95 backdrop-blur-md border-t border-neutral-200">
	            <div class="px-4 sm:px-6 lg:px-8 py-3 space-y-1">
                <a href="{{ route('home') }}" class="block py-2.5 text-neutral-700 hover:text-primary-600 font-medium transition-colors duration-300 border-l-4 border-transparent hover:border-primary-500 pl-4">Home</a>
                <a href="{{ route('products.index') }}" class="block py-2.5 text-neutral-700 hover:text-primary-600 font-medium transition-colors duration-300 border-l-4 border-transparent hover:border-primary-500 pl-4">Shop</a>
                <a href="{{ route('about') }}" class="block py-2.5 text-neutral-700 hover:text-primary-600 font-medium transition-colors duration-300 border-l-4 border-transparent hover:border-primary-500 pl-4">About</a>
                <a href="{{ route('contact') }}" class="block py-2.5 text-neutral-700 hover:text-primary-600 font-medium transition-colors duration-300 border-l-4 border-transparent hover:border-primary-500 pl-4">Contact</a>
                @if(auth()->check())
                    <a href="{{ route('orders.my') }}" class="block py-2.5 text-neutral-700 hover:text-primary-600 font-medium transition-colors duration-300 border-l-4 border-transparent hover:border-primary-500 pl-4">My Orders</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left py-2.5 text-neutral-700 hover:text-primary-600 font-medium transition-colors duration-300 border-l-4 border-transparent hover:border-primary-500 pl-4">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block py-2.5 text-neutral-700 hover:text-primary-600 font-medium transition-colors duration-300 border-l-4 border-transparent hover:border-primary-500 pl-4">Login</a>
                    <a href="{{ route('register') }}" class="block py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-medium rounded-lg text-center transition-all duration-300">Register</a>
                @endif
            </div>
        </div>
    </header>

    <!-- Main -->
    <main>
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 animate-slide-down">
                <div class="flash-message bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl shadow-soft">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 animate-slide-down">
                <div class="flash-message bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl shadow-soft">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </main>

    <!-- Premium Footer -->
    <footer class="bg-[#fff7eb] text-[#2f241c] border-t border-amber-100/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 text-left">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 md:gap-10 items-start">
                <!-- Brand -->
                <div class="lg:col-span-4 space-y-3 md:space-y-4">
                    <div class="flex items-center gap-4">
                        @if($siteSettings->footer_logo)
                            <img src="{{ asset('storage/' . $siteSettings->footer_logo) }}" alt="{{ $siteSettings->site_name }} logo" class="h-12 object-contain">
                        @elseif($siteSettings->logo)
                            <img src="{{ asset('storage/' . $siteSettings->logo) }}" alt="{{ $siteSettings->site_name }} logo" class="h-12 object-contain">
                        @endif
                        <div class="leading-tight">
                            <div class="text-2xl font-bold text-[#5f3c2a]" style="font-family: 'Cormorant Garamond', serif;">Krafty Curvz</div>
                            <div class="text-xs uppercase tracking-[0.22em] text-amber-700">Handmade Luxury</div>
                        </div>
                    </div>
                    <p class="text-sm text-[#4a3a2f] leading-7 max-w-md whitespace-pre-line">
                        {{ trim((string) ($siteSettings->footer_text ?: 'Handmade resin art, scented candles & creative gifts crafted with love.')) }}
                    </p>
                </div>

                <div class="lg:col-span-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7 md:gap-10">
                        <!-- Quick Links -->
                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-[0.22em] text-[#5f3c2a]">Quick Links</h4>
                            <ul class="mt-3 md:mt-4 space-y-2.5 md:space-y-3 text-sm">
                                <li><a href="{{ route('home') }}" class="text-[#4a3a2f] hover:text-amber-800 transition-colors">Home</a></li>
                                <li><a href="{{ route('products.index') }}" class="text-[#4a3a2f] hover:text-amber-800 transition-colors">Shop</a></li>
                                <li><a href="{{ route('about') }}" class="text-[#4a3a2f] hover:text-amber-800 transition-colors">About</a></li>
                                <li><a href="{{ route('contact') }}" class="text-[#4a3a2f] hover:text-amber-800 transition-colors">Contact</a></li>
                            </ul>
                        </div>

                        <!-- Categories -->
                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-[0.22em] text-[#5f3c2a]">Product Categories</h4>
                            <ul class="mt-3 md:mt-4 space-y-2.5 md:space-y-3 text-sm">
                                <li><a href="{{ route('category.show', 'resin-products') }}" class="text-[#4a3a2f] hover:text-amber-800 transition-colors">Resin Art</a></li>
                                <li><a href="{{ route('category.show', 'candles') }}" class="text-[#4a3a2f] hover:text-amber-800 transition-colors">Scented Candles</a></li>
                                <li><a href="{{ route('category.show', 'pipe-cleaner-crafts') }}" class="text-[#4a3a2f] hover:text-amber-800 transition-colors">Pipe Cleaner Craft</a></li>
                            </ul>
                        </div>

                        <!-- Contact -->
                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-[0.22em] text-[#5f3c2a]">Contact</h4>
                            <div class="mt-4 flex items-center gap-3">
                                @php
                                    $igUrl = !empty($siteSettings->instagram_url) ? $siteSettings->instagram_url : 'https://www.instagram.com/krafty_curvz/';
                                    $waNumber = !empty($siteSettings->whatsapp_number) ? preg_replace('/\D+/', '', $siteSettings->whatsapp_number) : null;
                                @endphp
                                @if($waNumber)
                                    <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full border border-amber-200 bg-white/60 hover:bg-white flex items-center justify-center transition-colors" title="WhatsApp">
                                        <svg class="w-5 h-5 text-emerald-700" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24L6.305 22.346a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                    </a>
                                @endif
                                @if(!empty($siteSettings->contact_email))
                                    <a href="mailto:{{ $siteSettings->contact_email }}" class="w-10 h-10 rounded-full border border-amber-200 bg-white/60 hover:bg-white flex items-center justify-center transition-colors" title="Email">
                                        <svg class="w-5 h-5 text-amber-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l9 6 9-6M4 6h16a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                                        </svg>
                                    </a>
                                @endif
                                <a href="{{ $igUrl }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full border border-amber-200 bg-white/60 hover:bg-white flex items-center justify-center transition-colors" title="Instagram">
                                    <svg class="w-5 h-5 text-[#5f3c2a]" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1112.324 0 6.162 6.162 0 01-12.324 0zM12 16a4 4 0 110-8 4 4 0 010 8zm4.965-10.405a1.44 1.44 0 112.881.001 1.44 1.44 0 01-2.881-.001z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 md:mt-12 pt-6 md:pt-8 border-t border-amber-100/70 flex flex-col md:flex-row items-center justify-between gap-3 text-sm text-[#4a3a2f]">
                <p>&copy; {{ date('Y') }} {{ $siteSettings->site_name ?? 'Krafty Curvz' }}. All rights reserved.</p>
                @if(!empty(trim((string) $siteSettings->footer_text)))
                    <p class="text-xs text-amber-800/90 tracking-wide whitespace-pre-line">{{ trim((string) $siteSettings->footer_text) }}</p>
                @else
                    <p class="text-xs text-amber-800/90 tracking-wide">Handmade with love • Secure packaging • Custom orders available</p>
                @endif
            </div>
        </div>
    </footer>

    <!-- Premium Fullscreen Search Overlay -->
    <div id="kc-search" class="kc-search-overlay" aria-hidden="true">
        <div class="kc-search-backdrop" aria-hidden="true"></div>
        <div class="kc-search-stage" role="dialog" aria-modal="true" aria-label="Search">
            <div class="kc-search-panel">
                <div class="kc-search-panel-head">
                    <div class="kc-search-title">Search</div>
                    <button type="button" class="kc-search-close" onclick="closeSearch()" aria-label="Close search">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="kc-search-body">
                    <div class="kc-search-lead">Find your next handmade favorite</div>
                    <div class="kc-search-sub">Search resin art, candles, bouquets, and custom gifts. Start typing, or pick a suggestion below.</div>

                    <form class="kc-search-form" method="GET" action="{{ route('products.index') }}">
                        <div class="kc-search-input-wrap">
                            <input id="kc-search-input" class="kc-search-input" name="search" type="text" autocomplete="off" placeholder="Search products…">
                            <button class="kc-search-submit" type="submit" aria-label="Search">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </div>
                    </form>

                    <div class="kc-search-tags" aria-label="Search suggestions">
                        <div class="kc-tag" data-kc-value="keychain"><span class="kc-tag-dot"></span>Keychain</div>
                        <div class="kc-tag" data-kc-value="candle"><span class="kc-tag-dot"></span>Candle</div>
                        <div class="kc-tag" data-kc-value="photo frame"><span class="kc-tag-dot"></span>Photo Frame</div>
                        <div class="kc-tag" data-kc-value="bouquet"><span class="kc-tag-dot"></span>Bouquet</div>
                        <div class="kc-tag" data-kc-value="tray"><span class="kc-tag-dot"></span>Tray</div>
                        <div class="kc-tag" data-kc-value="gift"><span class="kc-tag-dot"></span>Gift</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script>
        function openSearch() {
            const overlay = document.getElementById('kc-search');
            if (!overlay) return;

            overlay.setAttribute('aria-hidden', 'false');
            document.body.classList.add('kc-search-open');

            const input = document.getElementById('kc-search-input');
            setTimeout(function() { if (input) input.focus(); }, 80);
        }

        function closeSearch() {
            const overlay = document.getElementById('kc-search');
            if (!overlay) return;

            document.body.classList.remove('kc-search-open');
            overlay.setAttribute('aria-hidden', 'true');
        }

        // Premium fullscreen search UI (jQuery + Slick). Use jQuery instead of "$" everywhere.
        jQuery(function() {
            // Slick tags (smooth premium transitions)
                if (jQuery('.kc-search-tags').length && typeof jQuery('.kc-search-tags').slick === 'function') {
                    jQuery('.kc-search-tags').slick({
                        arrows: false,
                        dots: false,
                        infinite: false,
                        speed: 420,
                        cssEase: 'cubic-bezier(0.22, 0.61, 0.36, 1)',
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        swipeToSlide: true,
                        variableWidth: true,
                        responsive: [
                            { breakpoint: 1024, settings: { slidesToShow: 3 } },
                            { breakpoint: 640, settings: { slidesToShow: 2 } }
                        ]
                    });
                }

            // Click tag -> fill input and focus
            jQuery(document).on('click', '.kc-tag', function() {
                const val = jQuery(this).attr('data-kc-value') || '';
                jQuery('#kc-search-input').val(val).trigger('focus');
            });

            // Backdrop click closes
            jQuery(document).on('click', '.kc-search-backdrop', function() {
                closeSearch();
            });

            // ESC closes
            jQuery(document).on('keydown', function(e) {
                if (e.key === 'Escape') closeSearch();
            });
        });

        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
            
            // Animate menu items
            if (!menu.classList.contains('hidden')) {
                const items = menu.querySelectorAll('a');
                items.forEach((item, index) => {
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(-20px)';
                    setTimeout(() => {
                        item.style.transition = 'all 0.3s ease-out';
                        item.style.opacity = '1';
                        item.style.transform = 'translateX(0)';
                    }, index * 100);
                });
            }
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('mobile-menu');
            const menuButton = event.target.closest('button[onclick="toggleMobileMenu()"]');
            
            if (!menu.contains(event.target) && !menuButton && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
            }
        });

        // Header scroll effect
        let lastScroll = 0;
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > 100) {
                header.classList.add('shadow-medium');
            } else {
                header.classList.remove('shadow-medium');
            }
            
            lastScroll = currentScroll;
        });
    </script>

    @stack('scripts')
</body>
</html>
