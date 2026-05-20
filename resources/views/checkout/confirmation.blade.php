@extends('layouts.app')

@section('title', 'Order Confirmed')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Placed Successfully!</h1>
        <p class="text-gray-600">Thank you for your order.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-8 mb-6">
        <div class="text-center mb-6">
            <p class="text-sm text-gray-500">Order Number</p>
            <p class="text-3xl font-bold text-amber-700">{{ $order->order_number }}</p>
        </div>

        <div class="border-t border-gray-200 pt-6 space-y-4">
            <div class="flex justify-between">
                <span class="text-gray-600">Customer</span>
                <span class="font-medium">{{ $order->customer_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Email</span>
                <span class="font-medium">{{ $order->customer_email }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Phone</span>
                <span class="font-medium">{{ $order->customer_phone }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Order Total</span>
                <span class="font-bold text-lg">&#8377;{{ number_format($order->total, 0) }}</span>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-6 mt-6">
            <h3 class="font-semibold mb-3">Order Items</h3>
            @foreach($order->items as $item)
                <div class="flex items-center justify-between py-3 text-sm">
                    <div class="flex items-center space-x-3">
                        @if($item->is_customized && $item->customization_image)
                            <img src="{{ asset('storage/' . $item->customization_image) }}" alt="{{ $item->product_name }} - Custom Design" class="w-12 h-12 object-cover rounded-lg border-2 border-purple-300">
                        @elseif($item->product && $item->product->images->count() > 0)
                            <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" alt="{{ $item->product_name }}" class="w-12 h-12 object-cover rounded-lg">
                        @else
                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        <div>
                            <span class="font-medium">{{ $item->product_name }}</span>
                            @if($item->size_name)
                                <span class="text-gray-500 text-xs ml-1">({{ $item->size_name }})</span>
                            @endif
                            @if($item->shape_option)
                                <span class="text-gray-500 text-xs ml-1">[{{ $item->shape_option }}]</span>
                            @endif
                            <span class="text-gray-500">x {{ $item->quantity }}</span>
                            @if($item->is_customized)
                                <span class="text-purple-600 text-xs ml-1">(Custom)</span>
                                @if($item->customization_image)
                                    <a href="{{ asset('storage/' . $item->customization_image) }}" target="_blank" class="text-xs text-amber-700 hover:underline ml-2">View Preview</a>
                                @endif
                            @endif
                        </div>
                    </div>
                    <span>&#8377;{{ number_format($item->total, 0) }}</span>
                </div>
            @endforeach
        </div>

        @if($order->customer_will_send_item)
            <div class="mt-6 p-4 rounded-lg bg-blue-50 border border-blue-200 text-blue-900 text-sm">
                <p class="font-semibold mb-1">Parcel Required for Resin Preservation</p>
                <p>After placing the order, you will need to parcel your item to us. You can upload your courier slip later from My Orders.</p>
            </div>
        @endif
    </div>

    <!-- WhatsApp CTA -->
    <div id="whatsapp-confirmation-section" class="bg-emerald-50 border-2 border-emerald-200 rounded-xl p-8 text-center">
        <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-3">Complete Your Order via WhatsApp</h2>
        <p class="text-gray-600 mb-6 max-w-lg mx-auto">
            Please send your order number <strong class="text-amber-700">{{ $order->order_number }}</strong> on WhatsApp and complete payment directly to proceed with your order.
        </p>
        @php
            $waNumber = preg_replace('/\D+/', '', (string) $whatsappNumber);
            if ($waNumber !== '' && !str_starts_with($waNumber, '91')) {
                // Auto-prefix India country code when admin enters local 10-digit number.
                if (strlen($waNumber) === 10) {
                    $waNumber = '91' . $waNumber;
                }
            }
            $waMessage = rawurlencode("Hi! My order number is {$order->order_number}. I'd like to complete my payment.");
        @endphp
        @if($waNumber)
            <p class="text-sm text-gray-700 mb-4">
                WhatsApp Number: <strong class="text-green-700">+{{ $waNumber }}</strong>
            </p>
        @endif
        <a href="{{ $waNumber ? 'https://wa.me/' . $waNumber . '?text=' . $waMessage : '#' }}"
           target="_blank"
           class="inline-flex items-center {{ $waNumber ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-400 cursor-not-allowed' }} text-white font-semibold px-8 py-3 rounded-lg transition-colors"
           @if(!$waNumber) aria-disabled="true" onclick="return false;" @endif>
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Send on WhatsApp
        </a>
        @if(!$waNumber)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-yellow-700 text-sm">WhatsApp number not configured in Website Settings. Please add it from Admin > Site Settings.</p>
            </div>
        @endif
    </div>

    <div class="text-center mt-8">
        <a href="{{ route('products.index') }}" class="text-amber-700 hover:text-amber-800 font-medium">Continue Shopping &rarr;</a>
    </div>
</div>
@endsection
