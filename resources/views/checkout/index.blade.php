@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-8">Checkout</h1>

    <form method="POST" action="{{ route('checkout.place-order') }}">
        @csrf

        <div class="space-y-6">
            <!-- Customer Details -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Customer Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                        @error('customer_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                        @error('customer_email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                        <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                        @error('customer_phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Shipping Address</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Address *</label>
                    <textarea name="shipping_address" rows="3" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">{{ old('shipping_address') }}</textarea>
                    @error('shipping_address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Order Notes -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Order Notes (Optional)</h2>
                <textarea name="notes" rows="2" placeholder="Any special instructions..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">{{ old('notes') }}</textarea>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
                <div class="space-y-3 mb-4">
                    @foreach($cart as $key => $item)
                        <div class="flex justify-between text-sm">
                            <span>
                                {{ $item['name'] }}
                                @if($item['size_name'] ?? false) ({{ $item['size_name'] }}) @endif
                                @if($item['shape_option'] ?? false) [{{ $item['shape_option'] }}] @endif
                                x {{ $item['quantity'] }}
                            </span>
                            <span>&#8377;{{ number_format($item['price'] * $item['quantity'], 0) }}</span>
                        </div>
                    @endforeach
                    <hr>
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span>&#8377;{{ number_format($subtotal, 0) }}</span>
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-amber-700">
                        <strong>Payment Method:</strong> Pay via WhatsApp<br>
                        After placing your order, you will receive your order number. Please send it on WhatsApp and complete payment directly to proceed with your order.
                    </p>
                </div>

                <button type="submit" class="w-full bg-amber-700 hover:bg-amber-800 text-white font-semibold py-3 rounded-lg transition-colors">
                    Place Order
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
