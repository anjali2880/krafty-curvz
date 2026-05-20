@extends('layouts.admin')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="max-w-4xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.orders.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold">Order {{ $order->order_number }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Order Information</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Order Number</p>
                        <p class="font-medium">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Date</p>
                        <p class="font-medium">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Customer</p>
                        <p class="font-medium">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Email</p>
                        <p class="font-medium">{{ $order->customer_email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Phone</p>
                        <p class="font-medium">{{ $order->customer_phone }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Payment Method</p>
                        <p class="font-medium">{{ ucfirst($order->payment_method) }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-gray-500">Shipping Address</p>
                        <p class="font-medium">{{ $order->shipping_address }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-gray-500">Customer Will Parcel Item</p>
                        <p class="font-medium">{{ $order->customer_will_send_item ? 'Yes' : 'No' }}</p>
                    </div>
                    @if($order->customer_will_send_item)
                        <div class="col-span-2">
                            <p class="text-gray-500">Item Description</p>
                            <p class="font-medium">{{ $order->item_description ?: '-' }}</p>
                        </div>
                        @if($order->custom_note)
                            <div class="col-span-2">
                                <p class="text-gray-500">Custom Note</p>
                                <p class="font-medium">{{ $order->custom_note }}</p>
                            </div>
                        @endif
                        <div class="col-span-2">
                            <p class="text-gray-500">Courier Agency</p>
                            <p class="font-medium">{{ $order->courier_agency_name ?: '-' }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-500">Tracking Number</p>
                            <p class="font-medium">{{ $order->tracking_number ?: '-' }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-500">Parcel Slip</p>
                            @if($order->parcel_slip_path)
                                <a href="{{ asset('storage/' . $order->parcel_slip_path) }}" target="_blank" class="font-medium text-amber-700 hover:underline">View Parcel Slip</a>
                            @else
                                <p class="font-medium">-</p>
                            @endif
                        </div>
                    @endif
                    @if($order->notes)
                        <div class="col-span-2">
                            <p class="text-gray-500">Notes</p>
                            <p class="font-medium">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Order Items</h2>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                            @if($item->is_customized && $item->customization_image)
                                <img src="{{ asset('storage/' . $item->customization_image) }}" alt="{{ $item->product_name }} - Custom Design" class="w-16 h-16 object-cover rounded-lg border-2 border-purple-300">
                            @elseif($item->product && $item->product->images->count() > 0)
                                <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" alt="{{ $item->product_name }}" class="w-16 h-16 object-cover rounded-lg">
                            @else
                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">{{ $item->product_name }}</p>
                                @if($item->size_name)
                                    <p class="text-sm text-gray-500">Size: {{ $item->size_name }}</p>
                                @endif
                                @if($item->shape_option)
                                    <p class="text-sm text-gray-500">Shape: {{ $item->shape_option }}</p>
                                @endif
                                <p class="text-sm text-gray-500">&#8377;{{ number_format($item->price, 0) }} x {{ $item->quantity }}</p>
                                @php
                                    $hasCustomization = $item->is_customized || !empty($item->customization_data) || !empty($item->customization_image);
                                @endphp
                                @if($hasCustomization)
                                    <div class="mt-2">
                                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">Customized</span>
                                        @if($item->customization_image)
                                            <a href="{{ asset('storage/' . $item->customization_image) }}" target="_blank" class="text-xs text-amber-700 ml-2 hover:underline">View Full Preview</a>
                                        @endif
                                    </div>
                                    @php
                                        $designObjects = [];
                                        if (is_array($item->customization_data)) {
                                            $designObjects = $item->customization_data['objects'] ?? [];
                                        } elseif (is_string($item->customization_data) && trim($item->customization_data) !== '') {
                                            $decodedData = json_decode($item->customization_data, true);
                                            $designObjects = is_array($decodedData) ? ($decodedData['objects'] ?? []) : [];
                                        }
                                        $customSummary = is_array($item->customization_data) ? ($item->customization_data['custom_summary'] ?? null) : null;

                                        $customTexts = collect($customSummary['texts'] ?? [])
                                            ->filter(fn ($row) => is_array($row) && trim((string) ($row['text'] ?? '')) !== '')
                                            ->values();

                                        if ($customTexts->count() === 0) {
                                            $customTexts = collect($designObjects)
                                                ->filter(fn ($obj) => in_array(($obj['type'] ?? ''), ['i-text', 'text', 'textbox'], true))
                                                ->map(function ($obj) {
                                                    $styleColor = null;
                                                    if (isset($obj['styles']) && is_array($obj['styles'])) {
                                                        foreach ($obj['styles'] as $lineStyles) {
                                                            if (!is_array($lineStyles)) {
                                                                continue;
                                                            }
                                                            foreach ($lineStyles as $charStyle) {
                                                                if (is_array($charStyle) && !empty($charStyle['fill'])) {
                                                                    $styleColor = $charStyle['fill'];
                                                                    break 2;
                                                                }
                                                            }
                                                        }
                                                    }

                                                    return [
                                                        'text' => $obj['text'] ?? '',
                                                        'font' => $obj['fontFamily'] ?? 'Default',
                                                        'color' => $obj['fill'] ?? $styleColor ?? '#000000',
                                                    ];
                                                })
                                                ->filter(fn ($row) => trim((string) $row['text']) !== '')
                                                ->values();
                                        }

                                        $summaryImages = collect($customSummary['images'] ?? [])
                                            ->filter(fn ($row) => is_array($row) && !empty($row['url']))
                                            ->values();

                                        $imageObjects = collect($designObjects)
                                            ->filter(fn ($obj) => ($obj['type'] ?? '') === 'image')
                                            ->filter(fn ($obj) => !((bool) data_get($obj, 'data.isCanvasBaseImage', false)))
                                            ->values();
                                    @endphp
                                    @if($customTexts->count() > 0 || $summaryImages->count() > 0 || $imageObjects->count() > 0 || !empty($item->customization_image))
                                        <div class="mt-3 space-y-3">
                                            <div class="p-3 bg-purple-50 border border-purple-200 rounded-lg text-xs text-purple-900">
                                                <p class="font-semibold mb-2">Custom Text Added by Customer</p>
                                                @if($customTexts->count() > 0)
                                                    <div class="space-y-2">
                                                        @foreach($customTexts as $idx => $textData)
                                                            <div class="bg-white border border-purple-200 rounded p-2">
                                                                <p><span class="font-medium">Text {{ $idx + 1 }}:</span> "{{ $textData['text'] }}"</p>
                                                                <p><span class="font-medium">Font Style:</span> {{ $textData['font'] }}</p>
                                                                <p class="flex items-center gap-2">
                                                                    <span class="font-medium">Font Color:</span>
                                                                    <span>{{ $textData['color'] }}</span>
                                                                    <span class="inline-block w-4 h-4 rounded border border-gray-300" style="background-color: {{ $textData['color'] }};"></span>
                                                                </p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p>No custom text data found for this design.</p>
                                                @endif
                                            </div>

                                            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-900">
                                                <p class="font-semibold mb-2">Customer Uploaded Images</p>
                                                @if($summaryImages->count() > 0)
                                                    <div class="space-y-3">
                                                        @foreach($summaryImages as $index => $imgData)
                                                            @php
                                                                $imageSrc = $imgData['url'] ?? null;
                                                            @endphp
                                                            <div class="bg-white border border-blue-200 rounded p-2">
                                                                <p class="font-medium mb-2">Image {{ $index + 1 }}</p>
                                                                @if(is_string($imageSrc) && trim($imageSrc) !== '')
                                                                    <img src="{{ $imageSrc }}" alt="Customer uploaded image {{ $index + 1 }}" class="w-20 h-20 object-cover rounded border border-gray-200 mb-2">
                                                                    <a href="{{ $imageSrc }}" target="_blank" download="customer-image-{{ $order->order_number }}-{{ $item->id }}-{{ $index + 1 }}" class="text-amber-700 hover:underline">
                                                                        Download Image
                                                                    </a>
                                                                @else
                                                                    <span>-</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @elseif($imageObjects->count() > 0)
                                                    <div class="space-y-3">
                                                        @foreach($imageObjects as $index => $imgObj)
                                                            @php
                                                                $imageSrc = $imgObj['src'] ?? null;
                                                            @endphp
                                                            <div class="bg-white border border-blue-200 rounded p-2">
                                                                <p class="font-medium mb-2">Image {{ $index + 1 }}</p>
                                                                @if(is_string($imageSrc) && trim($imageSrc) !== '')
                                                                    <img src="{{ $imageSrc }}" alt="Customer uploaded image {{ $index + 1 }}" class="w-20 h-20 object-cover rounded border border-gray-200 mb-2">
                                                                    <a href="{{ $imageSrc }}" target="_blank" download="customer-image-{{ $order->order_number }}-{{ $item->id }}-{{ $index + 1 }}" class="text-amber-700 hover:underline">
                                                                        Download Image
                                                                    </a>
                                                                @else
                                                                    <span>-</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @elseif(!empty($item->customization_image))
                                                    <p>Original uploaded image not available in saved data for this item.</p>
                                                @else
                                                    <p>No uploaded image data found for this design.</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    @if($item->customization_data)
                                        <details class="mt-2">
                                            <summary class="text-xs text-gray-500 cursor-pointer hover:text-gray-700">View Customization Data</summary>
                                            <pre class="mt-1 text-xs bg-white p-2 rounded border overflow-x-auto max-h-40">{{ json_encode($item->customization_data, JSON_PRETTY_PRINT) }}</pre>
                                        </details>
                                    @endif
                                @endif
                            </div>
                            <p class="font-semibold">&#8377;{{ number_format($item->total, 0) }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-gray-200 mt-4 pt-4">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span>&#8377;{{ number_format($order->total, 0) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Update Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Update Order Status</h2>
                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Order Status</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-amber-500 focus:border-amber-500">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="waiting_for_customer_parcel" {{ $order->status == 'waiting_for_customer_parcel' ? 'selected' : '' }}>Waiting for Customer Parcel</option>
                            <option value="parcel_shipped_by_customer" {{ $order->status == 'parcel_shipped_by_customer' ? 'selected' : '' }}>Parcel Shipped by Customer</option>
                            <option value="parcel_received" {{ $order->status == 'parcel_received' ? 'selected' : '' }}>Parcel Received</option>
                            <option value="resin_work_in_progress" {{ $order->status == 'resin_work_in_progress' ? 'selected' : '' }}>Resin Work In Progress</option>
                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-amber-700 hover:bg-amber-800 text-white py-2 rounded-lg text-sm font-medium transition-colors">Update Status</button>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Update Payment Status</h2>
                <form method="POST" action="{{ route('admin.orders.update-payment-status', $order) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                        <select name="payment_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-amber-500 focus:border-amber-500">
                            <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg text-sm font-medium transition-colors">Update Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
