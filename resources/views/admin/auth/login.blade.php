<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Krafty Curvz</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold">
                    <span class="text-amber-700">Krafty</span>
                    <span class="text-gray-800">Curvz</span>
                </h1>
                <p class="text-gray-500 mt-1">Admin Panel</p>
            </div>

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                    @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                    @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full bg-amber-700 hover:bg-amber-800 text-white font-semibold py-2.5 rounded-lg transition-colors">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</body>
</html>
