@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="mb-5 md:mb-6">
    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-700">Admin Panel</p>
    <h1 class="mt-1 text-2xl md:text-3xl font-bold text-gray-950">Dashboard</h1>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-sm text-gray-500">Total Orders</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalOrders }}</p>
            </div>
            <div class="w-11 h-11 md:w-12 md:h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-sm text-gray-500">Pending Orders</p>
                <p class="text-2xl font-bold text-amber-600">{{ $pendingOrders }}</p>
            </div>
            <div class="w-11 h-11 md:w-12 md:h-12 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-sm text-gray-500">Total Products</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalProducts }}</p>
            </div>
            <div class="w-11 h-11 md:w-12 md:h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-sm text-gray-500">Revenue (Paid)</p>
                <p class="text-2xl font-bold text-green-600">&#8377;{{ number_format($totalRevenue, 0) }}</p>
            </div>
            <div class="w-11 h-11 md:w-12 md:h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-4 md:p-6 border-b border-gray-200">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-lg font-semibold">Recent Orders</h2>
            <a href="{{ route('admin.orders.index') }}" class="shrink-0 text-amber-700 hover:text-amber-800 text-sm font-medium">View All &rarr;</a>
        </div>
    </div>
    @if($recentOrders->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px]">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentOrders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 md:px-6 py-4 text-sm font-medium text-amber-700">
                                <a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a>
                            </td>
                            <td class="px-4 md:px-6 py-4 text-sm text-gray-800">{{ $order->customer_name }}</td>
                            <td class="px-4 md:px-6 py-4 text-sm font-medium">&#8377;{{ number_format($order->total, 0) }}</td>
                            <td class="px-4 md:px-6 py-4">
                                @php
                                    $statusColors = ['pending' => 'bg-yellow-100 text-yellow-700', 'processing' => 'bg-blue-100 text-blue-700', 'shipped' => 'bg-purple-100 text-purple-700', 'delivered' => 'bg-green-100 text-green-700', 'cancelled' => 'bg-red-100 text-red-700'];
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td class="px-4 md:px-6 py-4 text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="p-8 text-center text-gray-400">No orders yet.</div>
    @endif
</div>
@endsection
