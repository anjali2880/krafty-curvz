<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - Krafty Curvz Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 min-h-full overflow-x-hidden">
    <div class="min-h-screen md:flex">
        <!-- Sidebar -->
        <aside class="hidden md:flex md:flex-col md:fixed md:inset-y-0 md:left-0 md:w-64 bg-gray-900 text-white overflow-y-auto">
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
                <a href="{{ route('admin.deploy.index') }}" class="flex items-center px-4 py-2.5 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.deploy.*') ? 'bg-gray-800 text-amber-400' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.82m5.84-2.56a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.82m2.56-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/></svg>
                    Deploy
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
        <div class="md:hidden fixed top-0 left-0 right-0 bg-gray-900 text-white z-50 h-16 px-4 flex justify-between items-center shadow-lg">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 min-w-0">
                <span class="font-bold text-amber-400">Krafty Curvz</span>
                <span class="text-xs bg-amber-600 text-white px-2 py-0.5 rounded-full">Admin</span>
            </a>
            <button type="button" onclick="document.getElementById('mobile-sidebar').classList.toggle('hidden')" class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-white/10 text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
        <div id="mobile-sidebar" class="hidden md:hidden fixed inset-0 z-[60] bg-gray-950/95 text-white p-5 overflow-y-auto">
            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <span class="text-lg font-bold text-amber-400">Krafty Curvz</span>
                    <span class="text-xs bg-amber-600 text-white px-2 py-0.5 rounded-full">Admin</span>
                </a>
                <button type="button" onclick="document.getElementById('mobile-sidebar').classList.toggle('hidden')" class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-white/10 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav class="mt-6 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-4 py-3 text-gray-200 hover:bg-white/10 hover:text-amber-300 {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-amber-300' : '' }}">Dashboard</a>
                <a href="{{ route('admin.products.index') }}" class="block rounded-lg px-4 py-3 text-gray-200 hover:bg-white/10 hover:text-amber-300 {{ request()->routeIs('admin.products.*') ? 'bg-white/10 text-amber-300' : '' }}">Products</a>
                <a href="{{ route('admin.categories.index') }}" class="block rounded-lg px-4 py-3 text-gray-200 hover:bg-white/10 hover:text-amber-300 {{ request()->routeIs('admin.categories.*') ? 'bg-white/10 text-amber-300' : '' }}">Categories</a>
                <a href="{{ route('admin.orders.index') }}" class="block rounded-lg px-4 py-3 text-gray-200 hover:bg-white/10 hover:text-amber-300 {{ request()->routeIs('admin.orders.*') ? 'bg-white/10 text-amber-300' : '' }}">Orders</a>
                <a href="{{ route('admin.settings.index') }}" class="block rounded-lg px-4 py-3 text-gray-200 hover:bg-white/10 hover:text-amber-300 {{ request()->routeIs('admin.settings.*') ? 'bg-white/10 text-amber-300' : '' }}">Settings</a>
                <a href="{{ route('admin.deploy.index') }}" class="block rounded-lg px-4 py-3 text-gray-200 hover:bg-white/10 hover:text-amber-300 {{ request()->routeIs('admin.deploy.*') ? 'bg-white/10 text-amber-300' : '' }}">Deploy</a>
                <form method="POST" action="{{ route('admin.logout') }}" class="pt-3 border-t border-white/10">
                    @csrf
                    <button type="submit" class="block w-full rounded-lg px-4 py-3 text-left text-gray-200 hover:bg-white/10 hover:text-amber-300">Logout</button>
                </form>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="min-w-0 flex-1 md:ml-64">
            <div class="pt-16 md:pt-0">
                @if(session('success'))
                    <div class="flash-message mx-4 md:mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="flash-message mx-4 md:mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <main class="p-4 sm:p-5 md:p-6 lg:p-8 min-w-0">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
