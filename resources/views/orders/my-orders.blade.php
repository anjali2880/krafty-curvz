@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
        <p class="text-gray-600 mt-2">All placed orders and their current status.</p>
    </div>

    @if($orders->count() === 0)
        <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
            <p class="text-gray-600">No orders found.</p>
        </div>
    @else
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Order</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Items</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Order Type</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Total</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($orders as $order)
                            @php
                                $statusText = ucwords(str_replace('_', ' ', $order->status));
                                $statusClass = match($order->status) {
                                    'completed' => 'bg-emerald-100 text-emerald-700',
                                    'resin_work_in_progress' => 'bg-violet-100 text-violet-700',
                                    'parcel_received' => 'bg-sky-100 text-sky-700',
                                    'parcel_shipped_by_customer' => 'bg-blue-100 text-blue-700',
                                    'waiting_for_customer_parcel' => 'bg-amber-100 text-amber-800',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-5 py-4 text-sm font-semibold">
                                    <a href="{{ route('orders.show', $order) }}" class="text-amber-700 hover:text-amber-800 hover:underline">
                                        #{{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-600">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                <td class="px-5 py-4 text-sm text-gray-700">{{ $order->items->count() }}</td>
                                <td class="px-5 py-4 text-sm text-gray-700">{{ $order->customer_will_send_item ? 'Resin Preservation' : 'Normal' }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-gray-900">&#8377;{{ number_format($order->total, 0) }}</td>
                                <td class="px-5 py-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full font-medium {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center px-3 py-1.5 rounded-md bg-amber-100 text-amber-800 hover:bg-amber-200 font-medium transition-colors">
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
