@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-8">Shopping Cart</h1>

    @if(empty($cart) || count($cart) === 0)
        <div class="text-center py-16">
            <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
            <h2 class="text-xl font-semibold text-gray-600 mb-2">Your cart is empty</h2>
            <a href="{{ route('products.index') }}" class="text-amber-700 hover:text-amber-800 font-medium">Continue Shopping &rarr;</a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cart as $key => $item)
                    <div class="bg-white rounded-xl shadow-sm p-4 flex items-center space-x-4">
                        <div class="w-20 h-20 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                            @if(($item['is_customized'] ?? false) && ($item['customization_image'] ?? false))
                                <img src="{{ asset('storage/' . $item['customization_image']) }}" alt="{{ $item['name'] }} - Custom Design" class="w-full h-full object-cover">
                            @elseif($item['image'])
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('products.show', $item['slug']) }}" class="font-semibold text-gray-800 hover:text-amber-700">{{ $item['name'] }}</a>
                            @if($item['size_name'] ?? false)
                                <span class="block text-xs text-gray-500 mt-1">Size: {{ $item['size_name'] }}</span>
                            @endif
                            @if($item['shape_option'] ?? false)
                                <span class="block text-xs text-gray-500 mt-1">Shape: {{ $item['shape_option'] }}</span>
                            @endif
                            @if($item['is_customized'] ?? false)
                                <span class="block text-xs text-purple-600 mt-1">Custom Design</span>
                                @if($item['customization_image'] ?? false)
                                    <a href="{{ asset('storage/' . $item['customization_image']) }}" target="_blank" class="text-xs text-amber-700 hover:underline">View Preview</a>
                                @endif
                            @endif
                            <p class="text-amber-700 font-bold mt-1">&#8377;{{ number_format($item['price'], 0) }}</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <form method="POST" action="{{ route('cart.update') }}">
                                @csrf
                                <input type="hidden" name="cart_key" value="{{ $key }}">
                                @php
                                    $itemOrderMaxQty = max(1, (int) ($item['max_order_quantity'] ?? 10));
                                    $itemStockMaxQty = array_key_exists('available_stock', $item) && $item['available_stock'] !== null
                                        ? max(0, (int) $item['available_stock'])
                                        : null;
                                    $itemMaxQty = $itemStockMaxQty !== null ? min($itemOrderMaxQty, $itemStockMaxQty) : $itemOrderMaxQty;
                                @endphp
                                <div class="flex items-center border border-gray-300 rounded-lg">
                                    <button type="button" onclick="this.parentElement.querySelector('input').value = Math.max(1, parseInt(this.parentElement.querySelector('input').value) - 1); this.closest('form').submit()" class="px-2 py-1 text-gray-600" {{ $itemMaxQty <= 0 ? 'disabled' : '' }}>-</button>
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ max(1, $itemMaxQty) }}" class="w-10 text-center border-x border-gray-300 py-1 text-sm" {{ $itemMaxQty <= 0 ? 'disabled' : '' }}>
                                    <button type="button" onclick="this.parentElement.querySelector('input').value = Math.min({{ max(1, $itemMaxQty) }}, parseInt(this.parentElement.querySelector('input').value) + 1); this.closest('form').submit()" class="px-2 py-1 text-gray-600" {{ $itemMaxQty <= 0 ? 'disabled' : '' }}>+</button>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('cart.remove') }}">
                                @csrf
                                <input type="hidden" name="cart_key" value="{{ $key }}">
                                <button type="submit" class="text-red-400 hover:text-red-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Cart Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">
                    <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>&#8377;{{ number_format($total, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span class="text-green-600">Free</span>
                        </div>
                        <hr>
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span>&#8377;{{ number_format($total, 0) }}</span>
                        </div>
                    </div>
                    @if(auth()->check())
                        <a href="{{ route('checkout.index') }}" class="block w-full bg-amber-700 hover:bg-amber-800 text-white text-center font-semibold py-3 rounded-lg transition-colors">
                            Proceed to Checkout
                        </a>
                    @else
                        <a href="{{ route('login', ['redirect_to' => route('checkout.index')]) }}" class="block w-full bg-amber-700 hover:bg-amber-800 text-white text-center font-semibold py-3 rounded-lg transition-colors">
                            Login to Checkout
                        </a>
                        <p class="text-center text-xs text-gray-500 mt-2">You need to be logged in to place an order</p>
                    @endif
                    <a href="{{ route('products.index') }}" class="block text-center mt-3 text-sm text-gray-500 hover:text-amber-700">Continue Shopping</a>
                    <form method="POST" action="{{ route('cart.clear') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="block w-full text-center text-sm text-red-400 hover:text-red-600">Clear Cart</button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
