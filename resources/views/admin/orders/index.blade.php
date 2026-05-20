@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Orders</h1>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Order # or customer..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-amber-500 focus:border-amber-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-amber-500 focus:border-amber-500">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="waiting_for_customer_parcel" {{ request('status') == 'waiting_for_customer_parcel' ? 'selected' : '' }}>Awaiting Parcel</option>
                <option value="parcel_shipped_by_customer" {{ request('status') == 'parcel_shipped_by_customer' ? 'selected' : '' }}>Parcel Shipped by Customer</option>
                <option value="parcel_received" {{ request('status') == 'parcel_received' ? 'selected' : '' }}>Parcel Received</option>
                <option value="resin_work_in_progress" {{ request('status') == 'resin_work_in_progress' ? 'selected' : '' }}>Resin Work In Progress</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
        <button type="submit" class="bg-amber-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-amber-800">Filter</button>
        <a href="{{ route('admin.orders.index') }}" class="text-gray-500 hover:text-gray-700 text-sm px-4 py-2">Clear</a>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Will Parcel Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($orders as $order)
                    @php
                        $statusColors = ['pending' => 'bg-yellow-100 text-yellow-700', 'processing' => 'bg-blue-100 text-blue-700', 'shipped' => 'bg-purple-100 text-purple-700', 'delivered' => 'bg-green-100 text-green-700', 'cancelled' => 'bg-red-100 text-red-700', 'waiting_for_customer_parcel' => 'bg-orange-100 text-orange-700', 'parcel_shipped_by_customer' => 'bg-sky-100 text-sky-700', 'parcel_received' => 'bg-indigo-100 text-indigo-700', 'resin_work_in_progress' => 'bg-violet-100 text-violet-700', 'completed' => 'bg-emerald-100 text-emerald-700'];
                        $statusLabels = ['waiting_for_customer_parcel' => 'Awaiting Parcel'];
                        $paymentColors = ['unpaid' => 'bg-red-100 text-red-700', 'paid' => 'bg-green-100 text-green-700', 'refunded' => 'bg-gray-100 text-gray-700'];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-amber-700 hover:text-amber-800">{{ $order->order_number }}</a>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-800">{{ $order->customer_name }}</p>
                            <p class="text-xs text-gray-500">{{ $order->customer_email }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 text-xs rounded-full {{ $order->customer_will_send_item ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $order->customer_will_send_item ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">&#8377;{{ number_format($order->total, 0) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$order->status] ?? '' }}">{{ $statusLabels[$order->status] ?? ucwords(str_replace('_', ' ', $order->status)) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $paymentColors[$order->payment_status] ?? '' }}">{{ ucfirst($order->payment_status) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex items-center gap-3">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-amber-700 hover:text-amber-800 text-sm font-medium">View</a>
                                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this order? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-200">
        {{ $orders->withQueryString()->links() }}
    </div>
</div>
@endsection
