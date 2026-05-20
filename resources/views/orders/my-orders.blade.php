@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-6">My Orders</h1>

    @if($orders->count() === 0)
        <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-600">
            You have no orders yet.
        </div>
    @else
        <div class="space-y-6">
            @foreach($orders as $order)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-4">
                        <div>
                            <p class="font-semibold text-gray-900">Order #{{ $order->order_number }}</p>
                            <p class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div class="text-sm">
                            <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span>
                        </div>
                    </div>

                    <div class="text-sm text-gray-700 mb-3">
                        <p><strong>Total:</strong> &#8377;{{ number_format($order->total, 0) }}</p>
                        <p><strong>Items:</strong> {{ $order->items->count() }}</p>
                    </div>

                    @if($order->customer_will_send_item)
                        <div class="mb-3 p-3 rounded-lg bg-blue-50 border border-blue-200 text-blue-900 text-sm">
                            <p><strong>Resin Preservation Order:</strong> Yes</p>
                            <p><strong>Item Description:</strong> {{ $order->item_description }}</p>
                            @if($order->custom_note)
                                <p><strong>Custom Note:</strong> {{ $order->custom_note }}</p>
                            @endif
                        </div>

                        @if($order->parcel_slip_path)
                            <div class="mb-3 p-3 rounded-lg bg-green-50 border border-green-200 text-green-900 text-sm">
                                <p><strong>Courier:</strong> {{ $order->courier_agency_name }}</p>
                                <p><strong>Tracking Number:</strong> {{ $order->tracking_number }}</p>
                                <p><strong>Parcel Slip:</strong> <a href="{{ asset('storage/' . $order->parcel_slip_path) }}" target="_blank" class="underline">View Upload</a></p>
                            </div>
                        @endif

                        @if(!$order->parcel_slip_path)
                            <details class="mt-2">
                                <summary class="cursor-pointer text-amber-700 font-medium">Upload Parcel Details</summary>
                                <form method="POST" action="{{ route('orders.parcel-details', $order) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Courier Agency Name *</label>
                                        <input type="text" name="courier_agency_name" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tracking Number *</label>
                                        <input type="text" name="tracking_number" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Parcel Slip Upload (image/pdf) *</label>
                                        <input type="file" name="parcel_slip" accept=".jpg,.jpeg,.png,.webp,.pdf" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
                                        <textarea name="parcel_additional_notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
                                    </div>
                                    <button type="submit" class="bg-amber-700 hover:bg-amber-800 text-white px-4 py-2 rounded-lg text-sm font-medium">Submit Parcel Details</button>
                                </form>
                            </details>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
