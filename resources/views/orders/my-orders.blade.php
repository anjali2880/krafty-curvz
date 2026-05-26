@extends('layouts.app')

@section('title', 'My Orders')
@section('robots', 'noindex, nofollow')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8 rounded-2xl p-6 md:p-8 shadow-lg border border-amber-200 bg-amber-50">
        <p class="text-xs uppercase tracking-[0.2em] text-amber-700 font-semibold">Account</p>
        <h1 class="text-3xl md:text-4xl font-bold mt-1 text-gray-900">My Orders</h1>
        <p class="text-gray-700 mt-2">Track all placed orders and check the latest status updates.</p>
    </div>

    @if($orders->count() === 0)
        <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
            <p class="text-gray-600">No orders found.</p>
        </div>
    @else
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden mb-8 shadow-md">
            <div class="overflow-x-auto">
                <table class="w-full table-auto divide-y divide-gray-200">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-200">Order</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-200">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-200">Items</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-200">Order Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-200">Total</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-200">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-200">Payment Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-200">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($orders as $order)
                            @php
                                $statusText = $order->status === 'waiting_for_customer_parcel'
                                    ? 'Awaiting Parcel'
                                    : ucwords(str_replace('_', ' ', $order->status));
                                $statusClass = match($order->status) {
                                    'completed' => 'bg-emerald-100 text-emerald-700',
                                    'resin_work_in_progress' => 'bg-violet-100 text-violet-700',
                                    'parcel_received' => 'bg-sky-100 text-sky-700',
                                    'parcel_shipped_by_customer' => 'bg-blue-100 text-blue-700',
                                    'waiting_for_customer_parcel' => 'bg-amber-100 text-amber-800',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                                $paymentStatusText = ucfirst($order->payment_status);
                                $paymentStatusClass = match($order->payment_status) {
                                    'paid' => 'bg-emerald-100 text-emerald-700',
                                    'refunded' => 'bg-rose-100 text-rose-700',
                                    default => 'bg-yellow-100 text-yellow-800',
                                };
                            @endphp
                            <tr class="hover:bg-amber-50/40 transition-colors">
                                <td class="px-6 py-4 text-sm font-semibold">
                                    <a href="{{ route('orders.show', $order) }}" class="text-amber-700 hover:text-amber-800 hover:underline">
                                        #{{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700 font-medium">{{ $order->items->count() }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ $order->customer_will_send_item ? 'Resin Preservation' : 'Normal' }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-900">&#8377;{{ number_format($order->total, 0) }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full font-semibold {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full font-semibold {{ $paymentStatusClass }}">{{ $paymentStatusText }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-amber-600 text-white hover:bg-amber-700 font-semibold transition-colors shadow-sm">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-2">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
