<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if($siteSettings->favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $siteSettings->favicon) }}">
    @endif
    <title>@yield('title', $siteSettings->site_name) - {{ $siteSettings->site_name }} | Handmade Resin Products</title>
    <meta name="description" content="@yield('meta_description', 'Beautiful handmade resin products by ' . $siteSettings->site_name . ' - Coasters, Trays, Keychains, Wall Art & Custom Gifts')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-neutral-50 text-neutral-900 min-h-screen antialiased">
    <!-- Modern Header -->
    <header class="bg-[#fdfaf6]/90 backdrop-blur-md shadow-soft fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                        @if($siteSettings->logo)
                            <div class="relative">
                                <img src="{{ asset('storage/' . $siteSettings->logo) }}" alt="{{ $siteSettings->site_name }}" class="h-14 object-contain transition-transform duration-300 group-hover:scale-110">
                                <div class="absolute inset-0 bg-primary-400/20 rounded-lg blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>
                        @endif
                        @if($siteSettings->show_site_name)
                            <span class="text-2xl font-display font-bold bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent">{{ $siteSettings->site_name }}</span>
                        @endif
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="relative text-neutral-700 hover:text-primary-600 font-medium transition-all duration-300 group">
                        <span>Home</span>
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary-600 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('products.index') }}" class="relative text-neutral-700 hover:text-primary-600 font-medium transition-all duration-300 group">
                        <span>Shop</span>
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary-600 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('about') }}" class="relative text-neutral-700 hover:text-primary-600 font-medium transition-all duration-300 group">
                        <span>About</span>
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary-600 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    
                    <!-- Cart Icon -->
                    <a href="{{ route('cart.index') }}" class="relative text-neutral-700 hover:text-primary-600 transition-all duration-300 group">
                        <div class="relative">
                            <svg class="w-6 h-6 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                            </svg>
                            @php $cartCount = count(session()->get('cart', [])); @endphp
                            @if($cartCount > 0)
                                <span class="absolute -top-2 -right-2 bg-gradient-to-r from-primary-500 to-accent-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold animate-bounce-soft">{{ $cartCount }}</span>
                            @endif
                        </div>
                    </a>
                    
                    @if(auth()->check())
                        <a href="{{ route('orders.my') }}" class="relative text-neutral-700 hover:text-primary-600 font-medium transition-all duration-300 group">
                            <span>My Orders</span>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary-600 transition-all duration-300 group-hover:w-full"></span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="relative text-neutral-700 hover:text-primary-600 font-medium transition-all duration-300 group">
                                <span>Logout</span>
                                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary-600 transition-all duration-300 group-hover:w-full"></span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="relative text-neutral-700 hover:text-primary-600 font-medium transition-all duration-300 group">
                            <span>Login</span>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary-600 transition-all duration-300 group-hover:w-full"></span>
                        </a>
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white px-6 py-2.5 rounded-full font-medium transition-all duration-300 transform hover:scale-105 hover:shadow-medium">
                            Register
                        </a>
                    @endif
                </div>

                <!-- Mobile Menu Button -->
                <div class="lg:hidden flex items-center space-x-4">
                    <a href="{{ route('cart.index') }}" class="relative text-neutral-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                        </svg>
                        @if($cartCount > 0)
                            <span class="absolute -top-2 -right-2 bg-gradient-to-r from-primary-500 to-accent-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold">{{ $cartCount }}</span>
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
            <div class="px-4 sm:px-6 lg:px-8 py-4 space-y-3">
                <a href="{{ route('home') }}" class="block py-3 text-neutral-700 hover:text-primary-600 font-medium transition-colors duration-300 border-l-4 border-transparent hover:border-primary-500 pl-4">Home</a>
                <a href="{{ route('products.index') }}" class="block py-3 text-neutral-700 hover:text-primary-600 font-medium transition-colors duration-300 border-l-4 border-transparent hover:border-primary-500 pl-4">Shop</a>
                <a href="{{ route('about') }}" class="block py-3 text-neutral-700 hover:text-primary-600 font-medium transition-colors duration-300 border-l-4 border-transparent hover:border-primary-500 pl-4">About</a>
                @if(auth()->check())
                    <a href="{{ route('orders.my') }}" class="block py-3 text-neutral-700 hover:text-primary-600 font-medium transition-colors duration-300 border-l-4 border-transparent hover:border-primary-500 pl-4">My Orders</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left py-3 text-neutral-700 hover:text-primary-600 font-medium transition-colors duration-300 border-l-4 border-transparent hover:border-primary-500 pl-4">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block py-3 text-neutral-700 hover:text-primary-600 font-medium transition-colors duration-300 border-l-4 border-transparent hover:border-primary-500 pl-4">Login</a>
                    <a href="{{ route('register') }}" class="block py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-medium rounded-lg text-center transition-all duration-300">Register</a>
                @endif
            </div>
        </div>
    </header>

    <!-- Main -->
    <main class="pt-20 min-h-screen">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 animate-slide-down">
                <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl shadow-soft">
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
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl shadow-soft">
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

    <!-- Modern Footer -->
    <footer class="bg-gradient-to-br from-neutral-900 via-neutral-800 to-neutral-900 text-neutral-300 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.4"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <!-- Brand Section -->
                <div class="space-y-4">
                    <h3 class="text-2xl font-display font-bold text-white">{{ $siteSettings->site_name }}</h3>
                    <p class="text-neutral-400 leading-relaxed">{{ $siteSettings->footer_text ?? 'Handcrafted resin products made with love. Each piece is unique and crafted with care.' }}</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-primary-500/20 hover:bg-primary-500/30 rounded-lg flex items-center justify-center transition-all duration-300 group">
                            <svg class="w-5 h-5 text-primary-400 group-hover:text-primary-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-primary-500/20 hover:bg-primary-500/30 rounded-lg flex items-center justify-center transition-all duration-300 group">
                            <svg class="w-5 h-5 text-primary-400 group-hover:text-primary-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-primary-500/20 hover:bg-primary-500/30 rounded-lg flex items-center justify-center transition-all duration-300 group">
                            <svg class="w-5 h-5 text-primary-400 group-hover:text-primary-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1112.324 0 6.162 6.162 0 01-12.324 0zM12 16a4 4 0 110-8 4 4 0 010 8zm4.965-10.405a1.44 1.44 0 112.881.001 1.44 1.44 0 01-2.881-.001z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="space-y-4">
                    <h4 class="font-semibold text-white text-lg">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('home') }}" class="text-neutral-400 hover:text-primary-400 transition-all duration-300 flex items-center group"><span class="w-2 h-2 bg-primary-400 rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>Home</a></li>
                        <li><a href="{{ route('products.index') }}" class="text-neutral-400 hover:text-primary-400 transition-all duration-300 flex items-center group"><span class="w-2 h-2 bg-primary-400 rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>Shop</a></li>
                        <li><a href="{{ route('about') }}" class="text-neutral-400 hover:text-primary-400 transition-all duration-300 flex items-center group"><span class="w-2 h-2 bg-primary-400 rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>About</a></li>
                        <li><a href="{{ route('cart.index') }}" class="text-neutral-400 hover:text-primary-400 transition-all duration-300 flex items-center group"><span class="w-2 h-2 bg-primary-400 rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>Cart</a></li>
                        @if(auth()->check())
                            <li><a href="{{ route('orders.my') }}" class="text-neutral-400 hover:text-primary-400 transition-all duration-300 flex items-center group"><span class="w-2 h-2 bg-primary-400 rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>My Orders</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-neutral-400 hover:text-primary-400 transition-all duration-300 flex items-center group"><span class="w-2 h-2 bg-primary-400 rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>Logout</button>
                                </form>
                            </li>
                        @else
                            <li><a href="{{ route('login') }}" class="text-neutral-400 hover:text-primary-400 transition-all duration-300 flex items-center group"><span class="w-2 h-2 bg-primary-400 rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>Login</a></li>
                        @endif
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="space-y-4">
                    <h4 class="font-semibold text-white text-lg">Get in Touch</h4>
                    @if($siteSettings->contact_email)
                        <p class="text-gray-400 mb-2">{{ $siteSettings->contact_email }}</p>
                    @endif
                    @if($siteSettings->contact_phone)
                        <p class="text-gray-400 mb-2">{{ $siteSettings->contact_phone }}</p>
                    @endif
                    @if($siteSettings->whatsapp_number)
                        <a href="https://wa.me/{{ $siteSettings->whatsapp_number }}" target="_blank" class="inline-flex items-center mt-2 text-green-400 hover:text-green-300">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24L6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Chat on WhatsApp
                        </a>
                    @endif
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-500">
                <p>&copy; {{ date('Y') }} {{ $siteSettings->site_name }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu JavaScript -->
    <script>
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
