<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - Krafty Curvz Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 min-h-full">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white flex-shrink-0 hidden md:flex md:flex-col">
            <div class="p-6 border-b border-gray-700">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                    <span class="text-xl font-bold text-amber-400">Krafty</span>
                    <span class="text-xl font-bold text-white">Curvz</span>
                    <span class="text-xs bg-amber-600 text-white px-2 py-0.5 rounded ml-1">Admin</span>
                </a>
            </div>
            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-amber-400' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-gray-800 text-amber-400' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Products
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-gray-800 text-amber-400' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Categories
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-gray-800 text-amber-400' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Orders
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-gray-800 text-amber-400' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Settings
                </a>
            </nav>
            <div class="p-4 border-t border-gray-700">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-800 text-gray-300 hover:text-white transition-colors w-full">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Mobile Header -->
        <div class="md:hidden fixed top-0 left-0 right-0 bg-gray-900 text-white z-50 p-4 flex justify-between items-center">
            <span class="font-bold">KC Admin</span>
            <button onclick="document.getElementById('mobile-sidebar').classList.toggle('hidden')" class="text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
        <div id="mobile-sidebar" class="hidden md:hidden fixed inset-0 z-50 bg-gray-900 text-white p-6">
            <button onclick="document.getElementById('mobile-sidebar').classList.toggle('hidden')" class="absolute top-4 right-4 text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <nav class="mt-12 space-y-4">
                <a href="{{ route('admin.dashboard') }}" class="block py-2 text-gray-300 hover:text-amber-400">Dashboard</a>
                <a href="{{ route('admin.products.index') }}" class="block py-2 text-gray-300 hover:text-amber-400">Products</a>
                <a href="{{ route('admin.categories.index') }}" class="block py-2 text-gray-300 hover:text-amber-400">Categories</a>
                <a href="{{ route('admin.orders.index') }}" class="block py-2 text-gray-300 hover:text-amber-400">Orders</a>
                <a href="{{ route('admin.settings.index') }}" class="block py-2 text-gray-300 hover:text-amber-400">Settings</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="block py-2 text-gray-300 hover:text-amber-400">Logout</button>
                </form>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 md:ml-0">
            <div class="md:pt-0 pt-16">
                @if(session('success'))
                    <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="p-6">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
