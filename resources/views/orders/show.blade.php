@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    @php
        $statusText = $order->status === 'waiting_for_customer_parcel'
            ? 'Awaiting Parcel'
            : ucwords(str_replace('_', ' ', $order->status));
        $statusClass = match($order->status) {
            'completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'resin_work_in_progress' => 'bg-violet-100 text-violet-700 border-violet-200',
            'parcel_received' => 'bg-sky-100 text-sky-700 border-sky-200',
            'parcel_shipped_by_customer' => 'bg-blue-100 text-blue-700 border-blue-200',
            'waiting_for_customer_parcel' => 'bg-white text-gray-900 border-gray-300',
            default => 'bg-gray-100 text-gray-700 border-gray-200',
        };
        $paymentStatusText = ucfirst($order->payment_status);
        $paymentStatusClass = match($order->payment_status) {
            'paid' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'refunded' => 'bg-rose-100 text-rose-700 border-rose-200',
            default => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        };
    @endphp

    <div class="relative overflow-hidden bg-gray-900 text-white p-8 md:p-10 mb-10 shadow-xl border border-gray-800">
        <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-slate-800 to-amber-900/60"></div>
        <div class="absolute -top-12 -right-12 w-40 h-40 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-10 left-10 w-32 h-32 bg-amber-300/10 rounded-full"></div>
        <div class="relative flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div>
                <p class="text-sm text-gray-200 uppercase tracking-wide">Order Details</p>
                <h1 class="text-3xl md:text-4xl font-bold tracking-tight mt-1">#{{ $order->order_number }}</h1>
                <p class="text-gray-200 mt-2">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <span class="inline-flex items-center px-3 py-1.5 rounded-full border text-sm font-semibold {{ $statusClass }}">{{ $statusText }}</span>
                <a href="{{ route('orders.my') }}" class="inline-flex items-center px-4 py-2 rounded-full bg-amber-600 text-white hover:bg-amber-700 font-semibold transition-colors">Back to Orders</a>
            </div>
        </div>
    </div>

    <div class="mb-10 grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="rounded-[24px] border border-amber-100 p-6 md:p-7 bg-gradient-to-br from-white to-amber-50/40 min-h-[138px] flex flex-col justify-between shadow-sm">
                <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500 font-semibold">Order Total</p>
                <div>
                    <p class="text-3xl font-bold text-slate-900 leading-none">&#8377;{{ number_format($order->total, 0) }}</p>
                    <p class="text-sm text-slate-500 mt-2">Final payable amount</p>
                </div>
            </div>
            <div class="rounded-[24px] border border-amber-100 p-6 md:p-7 bg-gradient-to-br from-white to-slate-50 min-h-[138px] flex flex-col justify-between shadow-sm">
                <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500 font-semibold">Items</p>
                <div>
                    <p class="text-3xl font-bold text-slate-900 leading-none">{{ $order->items->count() }}</p>
                    <p class="text-sm text-slate-500 mt-2">Products in this order</p>
                </div>
            </div>
            <div class="rounded-[24px] border border-amber-100 p-6 md:p-7 bg-gradient-to-br from-white to-slate-50 min-h-[138px] flex flex-col justify-between shadow-sm">
                <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500 font-semibold">Order Type</p>
                <div>
                    <p class="text-xl font-semibold text-slate-900 leading-snug">{{ $order->customer_will_send_item ? 'Resin Preservation' : 'Normal' }}</p>
                    <p class="text-sm text-slate-500 mt-2">Selected checkout workflow</p>
                </div>
            </div>
            <div class="rounded-[24px] border border-amber-100 p-6 md:p-7 bg-gradient-to-br from-white to-slate-50 min-h-[138px] flex flex-col justify-between shadow-sm">
                <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500 font-semibold">Order Status</p>
                <div>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full border text-sm font-semibold {{ $statusClass }}">{{ $statusText }}</span>
                    <p class="text-sm text-slate-500 mt-3">Current progress update</p>
                </div>
            </div>
            <div class="rounded-[24px] border border-amber-100 p-6 md:p-7 bg-gradient-to-br from-white to-slate-50 min-h-[138px] flex flex-col justify-between shadow-sm">
                <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500 font-semibold">Payment Status</p>
                <div>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full border text-sm font-semibold {{ $paymentStatusClass }}">{{ $paymentStatusText }}</span>
                    <p class="text-sm text-slate-500 mt-3">Latest payment state</p>
                </div>
            </div>
    </div>

    <div class="bg-white/95 border border-gray-200 overflow-hidden shadow-sm mb-10">
        <div class="px-6 py-4 border-b border-gray-200 bg-slate-50">
            <h2 class="font-semibold text-gray-900">Order Items</h2>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($order->items as $item)
                @php
                    $imagePath = null;
                    if ($item->is_customized && $item->customization_image) {
                        $imagePath = asset('storage/' . $item->customization_image);
                    } elseif ($item->product && $item->product->images->count() > 0) {
                        $imagePath = asset('storage/' . $item->product->images->first()->image_path);
                    }
                @endphp
                <div class="p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-5 hover:bg-amber-50/30 transition-colors">
                    <div class="flex items-center gap-4 min-w-0">
                        @if($imagePath)
                            <img src="{{ $imagePath }}" alt="{{ $item->product_name }}" class="w-20 h-20 object-cover border border-gray-200 flex-shrink-0">
                        @else
                            <div class="w-20 h-20 bg-gray-100 border border-gray-200 flex-shrink-0"></div>
                        @endif
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ $item->product_name }}</p>
                            <div class="mt-1 text-sm text-gray-600 space-x-2">
                                <span>Qty: {{ $item->quantity }}</span>
                                @if($item->size_name)
                                    <span>• Size: {{ $item->size_name }}</span>
                                @endif
                                @if($item->shape_option)
                                    <span>• Shape: {{ $item->shape_option }}</span>
                                @endif
                            </div>
                            @if($item->is_customized)
                                <span class="inline-flex mt-2 px-2.5 py-1 bg-purple-100 text-purple-700 text-xs font-semibold">Customized</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-left lg:text-right min-w-[140px]">
                        <p class="text-xs uppercase tracking-wide text-gray-500">Unit Price</p>
                        <p class="font-semibold text-gray-900">&#8377;{{ number_format($item->price, 0) }}</p>
                        <p class="text-xs uppercase tracking-wide text-gray-500 mt-2">Line Total</p>
                        <p class="text-lg font-bold text-gray-900">&#8377;{{ number_format($item->total, 0) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white/95 border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
            <h3 class="font-semibold text-gray-900 mb-4">Customer Details</h3>
            <div class="space-y-2 text-sm text-gray-700">
                <p><span class="font-medium text-gray-900">Name:</span> {{ $order->customer_name }}</p>
                <p><span class="font-medium text-gray-900">Email:</span> {{ $order->customer_email }}</p>
                <p><span class="font-medium text-gray-900">Phone:</span> {{ $order->customer_phone }}</p>
                <p><span class="font-medium text-gray-900">Shipping Address:</span> {{ $order->shipping_address }}</p>
                @if($order->notes)
                    <p><span class="font-medium text-gray-900">Notes:</span> {{ $order->notes }}</p>
                @endif
            </div>
        </div>

        @if($order->customer_will_send_item)
            <div class="bg-white/95 border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                <h3 class="font-semibold text-gray-900 mb-4">Parcel Details</h3>
                <div class="space-y-2 text-sm text-gray-700">
                    <p><span class="font-medium text-gray-900">Item Description:</span> {{ $order->item_description ?: 'Not provided' }}</p>
                    @if($order->custom_note)
                        <p><span class="font-medium text-gray-900">Custom Note:</span> {{ $order->custom_note }}</p>
                    @endif
                    @if($order->courier_agency_name)
                        <p><span class="font-medium text-gray-900">Courier Agency:</span> {{ $order->courier_agency_name }}</p>
                    @endif
                    @if($order->tracking_number)
                        <p><span class="font-medium text-gray-900">Tracking Number:</span> {{ $order->tracking_number }}</p>
                    @endif
                    @if($order->parcel_slip_path)
                        <p><span class="font-medium text-gray-900">Parcel Slip:</span> <a class="text-amber-700 hover:text-amber-800 hover:underline" href="{{ asset('storage/' . $order->parcel_slip_path) }}" target="_blank">View Uploaded Slip</a></p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
