@extends('layouts.app')

@section('title', 'Order #' . $order->order_number)
@section('robots', 'noindex, nofollow')

@section('content')
@php
    $statusText = $order->status === 'waiting_for_customer_parcel'
        ? 'Awaiting Parcel'
        : ucwords(str_replace('_', ' ', $order->status));

    $statusClass = match($order->status) {
        'completed'                  => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'resin_work_in_progress'     => 'bg-violet-100 text-violet-700 border-violet-200',
        'parcel_received'            => 'bg-sky-100 text-sky-700 border-sky-200',
        'parcel_shipped_by_customer' => 'bg-blue-100 text-blue-700 border-blue-200',
        'waiting_for_customer_parcel'=> 'bg-amber-50 text-amber-800 border-amber-200',
        default                      => 'bg-gray-100 text-gray-700 border-gray-200',
    };
    $statusDot = match($order->status) {
        'completed'                  => 'bg-emerald-500',
        'resin_work_in_progress'     => 'bg-violet-500',
        'parcel_received'            => 'bg-sky-500',
        'parcel_shipped_by_customer' => 'bg-blue-500',
        'waiting_for_customer_parcel'=> 'bg-amber-500',
        default                      => 'bg-gray-400',
    };

    $paymentStatusText = ucfirst($order->payment_status);
    $paymentStatusClass = match($order->payment_status) {
        'paid'     => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'refunded' => 'bg-rose-100 text-rose-700 border-rose-200',
        default    => 'bg-yellow-50 text-yellow-800 border-yellow-200',
    };
    $paymentDot = match($order->payment_status) {
        'paid'     => 'bg-emerald-500',
        'refunded' => 'bg-rose-500',
        default    => 'bg-yellow-400',
    };

    $steps = $order->customer_will_send_item
        ? ['Order Placed', 'Awaiting Parcel', 'Parcel Shipped', 'Parcel Received', 'Work In Progress', 'Completed']
        : ['Order Placed', 'Work In Progress', 'Completed'];

    $stepStatusMap = $order->customer_will_send_item ? [
        'pending'                     => 0,
        'waiting_for_customer_parcel' => 1,
        'parcel_shipped_by_customer'  => 2,
        'parcel_received'             => 3,
        'resin_work_in_progress'      => 4,
        'completed'                   => 5,
    ] : [
        'pending'                => 0,
        'resin_work_in_progress' => 1,
        'completed'              => 2,
    ];
    $currentStep = $stepStatusMap[$order->status] ?? 0;
@endphp

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-7">
        <a href="{{ route('orders.my') }}" class="hover:text-amber-700 transition-colors font-medium">My Orders</a>
        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-800 font-semibold">#{{ $order->order_number }}</span>
    </nav>

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">Order #{{ $order->order_number }}</h1>
            <p class="text-sm text-gray-500 mt-1">Placed on {{ $order->created_at->format('d M Y') }} at {{ $order->created_at->format('h:i A') }}</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-xs font-semibold {{ $statusClass }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span>
                {{ $statusText }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-xs font-semibold {{ $paymentStatusClass }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $paymentDot }}"></span>
                {{ $paymentStatusText }}
            </span>
        </div>
    </div>

    {{-- Order Progress Timeline --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 md:p-8 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-6">Order Progress</h2>
        <div class="relative flex items-start justify-between">
            {{-- Connecting line --}}
            <div class="absolute top-3.5 left-0 right-0 h-0.5 bg-gray-100 z-0"></div>
            <div class="absolute top-3.5 left-0 h-0.5 bg-amber-500 z-0 transition-all duration-500"
                 style="width: {{ count($steps) > 1 ? ($currentStep / (count($steps) - 1)) * 100 : 100 }}%"></div>

            @foreach($steps as $i => $label)
                @php
                    $done    = $i < $currentStep;
                    $active  = $i === $currentStep;
                    $pending = $i > $currentStep;
                @endphp
                <div class="relative z-10 flex flex-col items-center text-center" style="width: {{ 100 / count($steps) }}%">
                    <div @class([
                        'w-7 h-7 rounded-full flex items-center justify-center border-2 transition-all',
                        'bg-amber-500 border-amber-500'  => $done,
                        'bg-white border-amber-500 ring-4 ring-amber-100' => $active,
                        'bg-white border-gray-200'        => $pending,
                    ])>
                        @if($done)
                            <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @elseif($active)
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                        @endif
                    </div>
                    <p @class([
                        'mt-2 text-[10px] md:text-xs font-medium leading-tight max-w-[64px]',
                        'text-amber-700'  => $done || $active,
                        'text-gray-400'   => $pending,
                    ])>{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex flex-col gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[11px] uppercase tracking-widest text-gray-400 font-medium">Order Total</p>
                <p class="text-xl font-bold text-gray-900 mt-0.5">&#8377;{{ number_format($order->total, 0) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex flex-col gap-3">
            <div class="w-9 h-9 rounded-xl bg-slate-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
            </div>
            <div>
                <p class="text-[11px] uppercase tracking-widest text-gray-400 font-medium">Items</p>
                <p class="text-xl font-bold text-gray-900 mt-0.5">{{ $order->items->count() }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex flex-col gap-3">
            <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/></svg>
            </div>
            <div>
                <p class="text-[11px] uppercase tracking-widest text-gray-400 font-medium">Order Type</p>
                <p class="text-sm font-bold text-gray-900 mt-0.5 leading-snug">{{ $order->customer_will_send_item ? 'Resin Preservation' : 'Standard' }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex flex-col gap-3">
            <div class="w-9 h-9 rounded-xl bg-sky-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
            </div>
            <div>
                <p class="text-[11px] uppercase tracking-widest text-gray-400 font-medium">Placed On</p>
                <p class="text-sm font-bold text-gray-900 mt-0.5 leading-snug">{{ $order->created_at->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Order Items --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Order Items</h2>
            <span class="text-xs text-gray-400 font-medium">{{ $order->items->count() }} {{ $order->items->count() === 1 ? 'item' : 'items' }}</span>
        </div>

        <div class="divide-y divide-gray-50">
            @foreach($order->items as $item)
                @php
                    $imagePath = null;
                    if ($item->is_customized && $item->customization_image) {
                        $imagePath = asset('storage/' . $item->customization_image);
                    } elseif ($item->product && $item->product->images->count() > 0) {
                        $imagePath = asset('storage/' . $item->product->images->first()->image_path);
                    }
                @endphp
                <div class="flex items-center gap-4 px-6 py-5">
                    {{-- Product image --}}
                    <div class="flex-shrink-0 w-16 h-16 rounded-xl overflow-hidden bg-gray-50 border border-gray-100">
                        @if($imagePath)
                            <img src="{{ $imagePath }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-7 h-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 21h18M6.75 10.5h.008v.008H6.75V10.5zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                            </div>
                        @endif
                    </div>

                    {{-- Product info --}}
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 truncate text-sm">{{ $item->product_name }}</p>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                            <span class="text-xs text-gray-500">Qty: <span class="font-medium text-gray-700">{{ $item->quantity }}</span></span>
                            @if($item->size_name)
                                <span class="text-gray-300">·</span>
                                <span class="text-xs text-gray-500">Size: <span class="font-medium text-gray-700">{{ $item->size_name }}</span></span>
                            @endif
                            @if($item->shape_option)
                                <span class="text-gray-300">·</span>
                                <span class="text-xs text-gray-500">Shape: <span class="font-medium text-gray-700">{{ $item->shape_option }}</span></span>
                            @endif
                        </div>
                        @if($item->is_customized)
                            <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded-full bg-violet-100 text-violet-700 text-[10px] font-semibold uppercase tracking-wide">Customized</span>
                        @endif
                    </div>

                    {{-- Pricing --}}
                    <div class="text-right flex-shrink-0">
                        <p class="text-xs text-gray-400 mb-0.5">&#8377;{{ number_format($item->price, 0) }} × {{ $item->quantity }}</p>
                        <p class="text-base font-bold text-gray-900">&#8377;{{ number_format($item->total, 0) }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Price summary --}}
        <div class="border-t border-gray-100 bg-gray-50/60 px-6 py-4">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">Subtotal ({{ $order->items->count() }} {{ $order->items->count() === 1 ? 'item' : 'items' }})</span>
                <span class="text-sm font-medium text-gray-700">&#8377;{{ number_format($order->items->sum('total'), 0) }}</span>
            </div>
            <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-200">
                <span class="font-semibold text-gray-900">Order Total</span>
                <span class="text-lg font-bold text-gray-900">&#8377;{{ number_format($order->total, 0) }}</span>
            </div>
        </div>
    </div>

    {{-- Customer & Parcel Details --}}
    <div class="grid grid-cols-1 {{ $order->customer_will_send_item ? 'lg:grid-cols-2' : '' }} gap-6">

        {{-- Customer Details --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                </div>
                <h3 class="font-semibold text-gray-900">Customer Details</h3>
            </div>
            <dl class="space-y-3">
                <div class="flex flex-col sm:flex-row sm:gap-4">
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide sm:w-28 flex-shrink-0 mb-0.5 sm:mb-0 sm:pt-0.5">Name</dt>
                    <dd class="text-sm text-gray-800 font-medium">{{ $order->customer_name }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4">
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide sm:w-28 flex-shrink-0 mb-0.5 sm:mb-0 sm:pt-0.5">Email</dt>
                    <dd class="text-sm text-gray-800">{{ $order->customer_email }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4">
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide sm:w-28 flex-shrink-0 mb-0.5 sm:mb-0 sm:pt-0.5">Phone</dt>
                    <dd class="text-sm text-gray-800">{{ $order->customer_phone }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:gap-4">
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide sm:w-28 flex-shrink-0 mb-0.5 sm:mb-0 sm:pt-0.5">Address</dt>
                    <dd class="text-sm text-gray-800 leading-relaxed">{{ $order->shipping_address }}</dd>
                </div>
                @if($order->notes)
                    <div class="flex flex-col sm:flex-row sm:gap-4">
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide sm:w-28 flex-shrink-0 mb-0.5 sm:mb-0 sm:pt-0.5">Notes</dt>
                        <dd class="text-sm text-gray-800 leading-relaxed">{{ $order->notes }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Parcel Details (resin preservation only) --}}
        @if($order->customer_will_send_item)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Parcel Details</h3>
                </div>
                <dl class="space-y-3">
                    <div class="flex flex-col sm:flex-row sm:gap-4">
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide sm:w-32 flex-shrink-0 mb-0.5 sm:mb-0 sm:pt-0.5">Item Description</dt>
                        <dd class="text-sm text-gray-800">{{ $order->item_description ?: '—' }}</dd>
                    </div>
                    @if($order->custom_note)
                        <div class="flex flex-col sm:flex-row sm:gap-4">
                            <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide sm:w-32 flex-shrink-0 mb-0.5 sm:mb-0 sm:pt-0.5">Custom Note</dt>
                            <dd class="text-sm text-gray-800 leading-relaxed">{{ $order->custom_note }}</dd>
                        </div>
                    @endif
                    @if($order->courier_agency_name)
                        <div class="flex flex-col sm:flex-row sm:gap-4">
                            <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide sm:w-32 flex-shrink-0 mb-0.5 sm:mb-0 sm:pt-0.5">Courier Agency</dt>
                            <dd class="text-sm text-gray-800 font-medium">{{ $order->courier_agency_name }}</dd>
                        </div>
                    @endif
                    @if($order->tracking_number)
                        <div class="flex flex-col sm:flex-row sm:gap-4">
                            <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide sm:w-32 flex-shrink-0 mb-0.5 sm:mb-0 sm:pt-0.5">Tracking No.</dt>
                            <dd class="text-sm font-mono font-semibold text-gray-900 bg-gray-50 border border-gray-100 rounded-lg px-2.5 py-1 inline-block">{{ $order->tracking_number }}</dd>
                        </div>
                    @endif
                    @if($order->parcel_slip_path)
                        <div class="flex flex-col sm:flex-row sm:gap-4">
                            <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide sm:w-32 flex-shrink-0 mb-0.5 sm:mb-0 sm:pt-0.5">Parcel Slip</dt>
                            <dd>
                                <a href="{{ asset('storage/' . $order->parcel_slip_path) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-sm text-amber-700 hover:text-amber-800 font-medium hover:underline transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                    View Uploaded Slip
                                </a>
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endif
    </div>

    {{-- Footer action --}}
    <div class="mt-8 flex items-center justify-between">
        <a href="{{ route('orders.my') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Back to My Orders
        </a>
    </div>

</div>
@endsection
