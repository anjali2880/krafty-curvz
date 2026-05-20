@extends('layouts.app')

@section('title', $product->name . ' | Handmade Craft Product')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 155, '') ?: ('Buy ' . $product->name . ' at Krafty Curvz. Handmade resin and craft product with premium artisan finish.'))
@section('canonical', route('products.show', $product->slug))
@section('og_type', 'product')
@section('og_image', $product->primary_image_url)

@push('head')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {"@type": "ListItem", "position": 1, "name": "Home", "item": "{{ route('home') }}"},
    {"@type": "ListItem", "position": 2, "name": "Shop", "item": "{{ route('products.index') }}"}@if($product->category),
    {"@type": "ListItem", "position": 3, "name": "{{ $product->category->name }}", "item": "{{ route('category.show', $product->category->slug) }}"}@endif,
    {"@type": "ListItem", "position": 4, "name": "{{ $product->name }}", "item": "{{ route('products.show', $product->slug) }}"}
  ]
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "{{ $product->name }}",
  "description": "{{ addslashes(strip_tags((string) $product->description)) }}",
  "image": [@foreach($product->images->filter(fn ($img) => !($img->is_canvas_image ?? false))->values() as $image)"{{ asset('storage/' . $image->image_path) }}"{{ !$loop->last ? ',' : '' }}@endforeach],
  "sku": "{{ $product->slug }}",
  "brand": {"@type": "Brand", "name": "{{ $siteSettings->site_name ?? 'Krafty Curvz' }}"},
  "offers": {
    "@type": "Offer",
    "priceCurrency": "INR",
    "price": "{{ $product->sale_price ?: $product->price }}",
    "availability": "https://schema.org/InStock",
    "url": "{{ route('products.show', $product->slug) }}"
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.8",
    "reviewCount": "24"
  },
  "review": [{
    "@type": "Review",
    "reviewRating": {"@type": "Rating", "ratingValue": "5"},
    "author": {"@type": "Person", "name": "Verified Customer"},
    "reviewBody": "Excellent handcrafted quality and beautiful finishing."
  }]
}
</script>
@endpush

@section('content')
@php
    $displayImages = $product->images
        ->filter(fn ($img) => !($img->is_canvas_image ?? false))
        ->values();
    $shapeOptions = collect($product->shape_options ?? [])->filter(fn ($shape) => is_string($shape) && trim($shape) !== '')->values();
    $maxOrderQuantity = max(1, (int) ($product->max_order_quantity ?? 10));
    $availableStock = $product->manage_stock ? max(0, (int) ($product->stock_quantity ?? 0)) : null;
    $isOutOfStock = $product->manage_stock && $availableStock === 0;
    $maxPurchasableQuantity = $availableStock !== null ? min($maxOrderQuantity, $availableStock) : $maxOrderQuantity;
    $showOrderType = $product->canUseResinOrderType();
    $hasSavedDesign = !empty($existingCustomizationData);
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-amber-700">Home</a> &gt;
            <a href="{{ route('products.index') }}" class="hover:text-amber-700">Shop</a> &gt;
        @if($product->category)
            <a href="{{ route('category.show', $product->category->slug) }}" class="hover:text-amber-700">{{ $product->category->name }}</a> &gt;
        @endif
        <span class="text-gray-800">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <!-- Product Images -->
        <div>
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-4">
                <div id="main-image" class="aspect-square bg-gray-100 flex items-center justify-center">
                    <img id="main-product-image" src="{{ $product->primary_image_url }}" alt="{{ $product->name }} handmade product image" fetchpriority="high" class="w-full h-full object-cover">
                </div>
            </div>
            @if($displayImages->count() > 1)
                <div class="flex gap-3 overflow-x-auto pb-2">
                    @foreach($displayImages as $image)
                        <button onclick="document.getElementById('main-product-image').src='{{ asset('storage/' . $image->image_path) }}'" class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 border-transparent hover:border-amber-500 transition-colors">
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }} gallery image {{ $loop->iteration }}" loading="lazy" decoding="async" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Product Info -->
        <div>
            @if($product->category)
                <p class="text-sm text-amber-600 font-medium mb-2">{{ $product->category->name }}</p>
            @endif
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>

            <div class="flex items-center space-x-3 mb-6">
                @if($product->sizes->count() > 0)
                    <span class="text-3xl font-bold text-amber-700" id="main-price">&#8377;{{ number_format($product->sizes->first()->price, 0) }}</span>
                @elseif($product->sale_price)
                    <span class="text-3xl font-bold text-amber-700">&#8377;{{ number_format($product->sale_price, 0) }}</span>
                    <span class="text-xl text-gray-400 line-through">&#8377;{{ number_format($product->price, 0) }}</span>
                    <span class="bg-red-100 text-red-700 text-sm px-2 py-0.5 rounded-full">{{ round((1 - $product->sale_price / $product->price) * 100) }}% OFF</span>
                @else
                    <span class="text-3xl font-bold text-gray-900">&#8377;{{ number_format($product->price, 0) }}</span>
                @endif
            </div>
            @if($isOutOfStock)
                <p class="inline-flex items-center bg-red-100 text-red-700 text-sm font-semibold px-3 py-1 rounded-full mb-4">Out of Stock</p>
            @elseif($availableStock !== null)
                <p class="text-sm text-gray-600 mb-4">Available: {{ $availableStock }}</p>
            @endif

            <div class="prose prose-sm text-gray-600 mb-6">
                <p>{{ $product->description }}</p>
            </div>

            @if($product->sizes->count() > 0)
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Size:</label>
                    <select id="size-select" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                        @foreach($product->sizes as $size)
                            <option value="{{ $size->id }}" data-price="{{ $size->price }}" data-name="{{ $size->name }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $size->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if($product->customizable_product)
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center space-x-2 mb-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        <span class="font-semibold text-purple-700">Customizable Product</span>
                    </div>
                    <p class="text-sm text-purple-600 mb-3">Customize this product with your own text, images, and design!</p>
                    <button type="button" id="open-customizer-btn" data-product-id="{{ $product->id }}" data-product-name="{{ json_encode($product->name) }}" data-product-price="{{ $product->effective_price }}" class="{{ $isOutOfStock ? 'bg-gray-400 cursor-not-allowed' : 'bg-purple-600 hover:bg-purple-700' }} text-white px-6 py-2 rounded-lg transition-colors font-medium" {{ $isOutOfStock ? 'disabled' : '' }}>
                        {{ $isOutOfStock ? 'Out of Stock' : ($hasSavedDesign ? 'Edit Your Design' : 'Customize') }}
                    </button>
                </div>
            @endif

            <!-- Add to Cart Form -->
            <form method="POST" action="{{ route('cart.add') }}" class="mb-6">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="size_id" id="selected_size_id" value="{{ $product->sizes->count() > 0 ? $product->sizes->first()->id : '' }}">
                <input type="hidden" name="size_name" id="selected_size_name" value="{{ $product->sizes->count() > 0 ? $product->sizes->first()->name : '' }}">
                <input type="hidden" name="size_price" id="selected_size_price" value="{{ $product->sizes->count() > 0 ? $product->sizes->first()->price : $product->effective_price }}">
                <input type="hidden" name="customization_data" id="customization_data_input">
                <input type="hidden" name="customization_image" id="customization_image_input" value="{{ $existingCustomizationImage }}">

                @if($showOrderType)
                    <div class="mb-4 p-4 rounded-lg border border-blue-200 bg-blue-50">
                        <p class="text-sm font-semibold text-blue-900 mb-2">Order Type</p>
                        <label class="flex items-start gap-2 text-sm text-blue-900 mb-2">
                            <input type="radio" name="order_type" value="normal" {{ old('order_type', 'normal') === 'normal' ? 'checked' : '' }} class="mt-1">
                            <span>Normal order</span>
                        </label>
                        <label class="flex items-start gap-2 text-sm text-blue-900">
                            <input type="radio" name="order_type" value="send_item" {{ old('order_type') === 'send_item' ? 'checked' : '' }} class="mt-1">
                            <span>I will send my own item for resin preservation</span>
                        </label>

                        <div id="send-item-fields-product" class="mt-3 {{ old('order_type') === 'send_item' ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium text-blue-900 mb-1">Item Description</label>
                            <textarea name="item_description" rows="2" class="w-full border border-blue-200 rounded-lg px-3 py-2 text-sm" placeholder="What item will you send?">{{ old('item_description') }}</textarea>
                            <label class="block text-sm font-medium text-blue-900 mb-1 mt-2">Special Customization Note (Optional)</label>
                            <textarea name="custom_note" rows="2" class="w-full border border-blue-200 rounded-lg px-3 py-2 text-sm" placeholder="Any preservation/custom note...">{{ old('custom_note') }}</textarea>
                        </div>
                    </div>
                @endif

                @if($product->has_shape_options && $shapeOptions->count() > 0)
                    <div class="mb-4">
                        <label for="shape_option" class="block text-sm font-medium text-gray-700 mb-2">Select Shape:</label>
                        <select name="shape_option" id="shape_option" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="">Choose a shape</option>
                            @foreach($shapeOptions as $shape)
                                <option value="{{ $shape }}" {{ old('shape_option') === $shape ? 'selected' : '' }}>{{ $shape }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="flex items-center space-x-4 mb-4">
                    <label class="text-sm font-medium text-gray-700">Quantity:</label>
                    <div class="flex items-center border border-gray-300 rounded-lg">
                        <button type="button" onclick="updateQty(-1)" class="px-3 py-2 text-gray-600 hover:text-gray-800">-</button>
                        <input type="number" name="quantity" id="qty-input" value="1" min="1" max="{{ max(1, $maxPurchasableQuantity) }}" class="w-12 text-center border-x border-gray-300 py-2" {{ $isOutOfStock ? 'disabled' : '' }}>
                        <button type="button" onclick="updateQty(1)" class="px-3 py-2 text-gray-600 hover:text-gray-800">+</button>
                    </div>
                </div>

                <button type="submit" id="add-to-cart-btn" class="w-full {{ $isOutOfStock ? 'bg-gray-400 cursor-not-allowed' : 'bg-amber-700 hover:bg-amber-800' }} text-white font-semibold py-3 rounded-lg transition-colors" {{ $isOutOfStock ? 'disabled' : '' }}>
                    {{ $isOutOfStock ? 'Out of Stock' : 'Add to Cart' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Custom Product Builder Modal -->
    @if($product->customizable_product)
        <div id="customizer-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold">Customize Your Product</h2>
                        <button onclick="closeCustomizer()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Canvas Area -->
                        <div>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg overflow-hidden bg-gray-50">
                                <canvas id="customizer-canvas" width="500" height="500"></canvas>
                            </div>
                        </div>

                        <!-- Controls -->
                        <div class="space-y-4">
                            <!-- Add Text -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Add Custom Text</label>
                                <input type="text" id="custom-text-input" placeholder="Enter your text..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                <button onclick="addTextToCanvas()" class="mt-2 bg-amber-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-amber-700">Add Text</button>
                            </div>

                            <!-- Upload Image -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Image/Icon</label>
                                <input type="file" id="custom-image-input" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                <button onclick="addImageToCanvas()" class="mt-2 bg-amber-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-amber-700">Add Image</button>
                            </div>

                            <!-- Controls Info -->
                            <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
                                <p class="font-semibold mb-2">Controls:</p>
                                <ul class="space-y-1 list-disc list-inside">
                                    <li>Click to select elements</li>
                                    <li>Drag to move elements</li>
                                    <li>Corner handles to resize</li>
                                    <li>Rotation handle to rotate</li>
                                    <li>Delete key to remove selected</li>
                                </ul>
                            </div>

                            <!-- Delete Selected -->
                            <button onclick="deleteSelected()" class="bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm hover:bg-red-200">Delete Selected Element</button>

                            <!-- Save & Add to Cart -->
                            <button onclick="saveDesign()" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 rounded-lg transition-colors">
                                Save Design
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Related Products -->
@if($relatedProducts->count() > 0)
        <div class="mt-16">
            <h2 class="text-2xl font-bold mb-6">You May Also Like</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function updateQty(delta) {
    const input = document.getElementById('qty-input');
    if (!input) return;

    const current = parseInt(input.value, 10) || 1;
    const max = parseInt(input.max, 10) || 10;
    let next = current + delta;

    if (next < 1) next = 1;
    if (next > max) next = max;
    input.value = next;
}

document.addEventListener('DOMContentLoaded', function() {
    const qtyInput = document.getElementById('qty-input');
    if (!qtyInput) return;

    qtyInput.addEventListener('input', function() {
        let val = parseInt(this.value, 10);
        const max = parseInt(this.max, 10) || 10;
        if (Number.isNaN(val)) return;
        if (val < 1) val = 1;
        if (val > max) val = max;
        this.value = val;
    });

    qtyInput.addEventListener('blur', function() {
        if (!this.value || parseInt(this.value, 10) < 1) {
            this.value = 1;
        }
    });

    const normalOrder = document.querySelector('input[name="order_type"][value="normal"]');
    const sendItemOrder = document.querySelector('input[name="order_type"][value="send_item"]');
    const sendItemFields = document.getElementById('send-item-fields-product');

    function toggleSendItemFields() {
        if (!sendItemFields || !sendItemOrder) return;
        sendItemFields.classList.toggle('hidden', !sendItemOrder.checked);
    }

    if (normalOrder) normalOrder.addEventListener('change', toggleSendItemFields);
    if (sendItemOrder) sendItemOrder.addEventListener('change', toggleSendItemFields);
    toggleSendItemFields();
});
</script>
@endpush

@if($product->customizable_product)
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
<script>
let fabricCanvas = null;
let currentProductId = null;
let currentProductPrice = 0;
const existingCustomizationData = @json($existingCustomizationData);
const canvasImages = @json(
    $product->images
        ->filter(fn ($image) => (bool) ($image->is_canvas_image ?? false))
        ->sortBy('sort_order')
        ->map(fn ($image) => asset('storage/' . $image->image_path))
        ->values()
);

// Handle size selection
document.addEventListener('DOMContentLoaded', function() {
    const sizeSelect = document.getElementById('size-select');
    if (sizeSelect) {
        sizeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const sizeId = this.value;
            const sizePrice = selectedOption.getAttribute('data-price');
            const sizeName = selectedOption.getAttribute('data-name');

            document.getElementById('selected_size_id').value = sizeId;
            document.getElementById('selected_size_price').value = sizePrice;
            document.getElementById('selected_size_name').value = sizeName;
            document.getElementById('display-price').textContent = number_format(sizePrice, 0);

            // Update main price display
            const mainPrice = document.getElementById('main-price');
            if (mainPrice) {
                mainPrice.textContent = '₹' + number_format(sizePrice, 0);
            }
        });
    }

    // Handle customizer button click
    const customizerBtn = document.getElementById('open-customizer-btn');
    if (customizerBtn) {
        customizerBtn.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = JSON.parse(this.getAttribute('data-product-name'));
            const productPrice = this.getAttribute('data-product-price');
            openCustomizer(productId, productName, productPrice);
        });
    }

});

function number_format(number, decimals) {
    return parseFloat(number).toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function openCustomizer(productId, productName, price) {
    console.log('openCustomizer called', productId, productName, price);
    currentProductId = productId;
    currentProductPrice = price;
    const modal = document.getElementById('customizer-modal');
    console.log('Modal element:', modal);
    if (modal) {
        modal.classList.remove('hidden');
        console.log('Modal opened');
    } else {
        console.error('Modal not found');
    }

    if (!fabricCanvas) {
        fabricCanvas = new fabric.Canvas('customizer-canvas', {
            backgroundColor: '#ffffff',
        });

        // Add base product placeholder
        fabric.Canvas.prototype._initInteraction = fabric.Canvas.prototype._initInteraction || function(){};
    }

    const renderCanvasWithBaseImages = function () {
        fabricCanvas.clear();
        if (existingCustomizationData && typeof existingCustomizationData === 'object') {
            const sanitizedDesignData = sanitizeSavedDesignData(existingCustomizationData);
            fabricCanvas.loadFromJSON(sanitizedDesignData, function() {
                renderCanvasImageGrid().then(function () {
                    sendCanvasImagesToBack();
                    fabricCanvas.renderAll();
                });
            });
        } else {
            renderCanvasImageGrid().then(function () {
                fabricCanvas.renderAll();
            });
        }
    };

    // Load previously saved design (if available) so customer can continue editing.
    renderCanvasWithBaseImages();
}

function sanitizeSavedDesignData(designData) {
    if (!designData || typeof designData !== 'object') {
        return designData;
    }

    const cloned = JSON.parse(JSON.stringify(designData));
    if (!Array.isArray(cloned.objects)) {
        return cloned;
    }

    // Remove previously-saved base canvas images to avoid duplicate/stacked backgrounds.
    cloned.objects = cloned.objects.filter((obj) => !(obj?.data?.isCanvasBaseImage));
    return cloned;
}

function renderCanvasImageGrid() {
    if (!fabricCanvas || !Array.isArray(canvasImages) || canvasImages.length === 0) {
        return Promise.resolve();
    }

    const urls = canvasImages.filter((url) => typeof url === 'string' && url.trim() !== '');
    if (urls.length === 0) {
        return Promise.resolve();
    }

    const canvasWidth = fabricCanvas.getWidth();
    const canvasHeight = fabricCanvas.getHeight();
    const imageCount = urls.length;

    function getSlots(count) {
        if (count <= 0) {
            return [];
        }

        if (count === 1) {
            return [{ x: 0, y: 0, w: canvasWidth, h: canvasHeight }];
        }

        if (count === 3) {
            const topRowHeight = canvasHeight / 2;
            const bottomRowHeight = canvasHeight - topRowHeight;
            const halfWidth = canvasWidth / 2;

            return [
                { x: 0, y: 0, w: halfWidth, h: topRowHeight },
                { x: halfWidth, y: 0, w: halfWidth, h: topRowHeight },
                { x: 0, y: topRowHeight, w: canvasWidth, h: bottomRowHeight },
            ];
        }

        // Adaptive grid based on canvas aspect ratio (width/height).
        const aspect = canvasWidth / canvasHeight;
        let best = { cols: 1, rows: count, score: Number.POSITIVE_INFINITY };

        for (let cols = 1; cols <= count; cols++) {
            const rows = Math.ceil(count / cols);
            const emptyCells = (rows * cols) - count;
            const cellAspect = (canvasWidth / cols) / (canvasHeight / rows);

            // Lower score is better: prefer fewer empty cells and cell aspect near square.
            const score = (emptyCells * 5) + Math.abs(Math.log(cellAspect));
            if (score < best.score) {
                best = { cols, rows, score };
            }
        }

        // Small orientation nudge so portrait canvases naturally stack more.
        if (aspect < 1 && best.cols > best.rows && count > 1) {
            best = { cols: best.rows, rows: best.cols, score: best.score };
        }

        const cellW = canvasWidth / best.cols;
        const cellH = canvasHeight / best.rows;
        const slots = [];
        for (let i = 0; i < count; i++) {
            const row = Math.floor(i / best.cols);
            const col = i % best.cols;
            slots.push({ x: col * cellW, y: row * cellH, w: cellW, h: cellH });
        }
        return slots;
    }

    const slots = getSlots(imageCount);

    const loadImage = (url, index) => new Promise((resolve) => {
        fabric.Image.fromURL(url, function(img) {
            if (!img) {
                resolve();
                return;
            }

            const slot = slots[index];
            if (!slot) {
                resolve();
                return;
            }

            const scale = Math.max(slot.w / img.width, slot.h / img.height);
            img.scale(scale);

            const scaledWidth = img.getScaledWidth();
            const scaledHeight = img.getScaledHeight();
            const left = slot.x + ((slot.w - scaledWidth) / 2);
            const top = slot.y + ((slot.h - scaledHeight) / 2);

            img.set({
                left,
                top,
                selectable: false,
                evented: false,
                hasControls: false,
                hasBorders: false,
                lockMovementX: true,
                lockMovementY: true,
                excludeFromExport: true,
                clipPath: new fabric.Rect({
                    left: slot.x,
                    top: slot.y,
                    width: slot.w,
                    height: slot.h,
                    absolutePositioned: true,
                }),
                data: {
                    isCanvasBaseImage: true
                }
            });

            fabricCanvas.add(img);
            resolve();
        });
    });

    return Promise.all(urls.map((url, index) => loadImage(url, index))).then(() => {
        sendCanvasImagesToBack();
    });
}

function sendCanvasImagesToBack() {
    if (!fabricCanvas) return;
    fabricCanvas.getObjects().forEach((obj) => {
        if (obj?.data?.isCanvasBaseImage) {
            obj.sendToBack();
        }
    });
}

function closeCustomizer() {
    document.getElementById('customizer-modal').classList.add('hidden');
}

function addTextToCanvas() {
    const text = document.getElementById('custom-text-input').value;
    if (!text.trim()) return;

    const textObj = new fabric.IText(text, {
        left: 100,
        top: 100,
        fontSize: 24,
        fill: '#000000',
        fontFamily: 'Arial',
    });
    fabricCanvas.add(textObj);
    fabricCanvas.setActiveObject(textObj);
    document.getElementById('custom-text-input').value = '';
}

function addImageToCanvas() {
    const fileInput = document.getElementById('custom-image-input');
    const file = fileInput.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        fabric.Image.fromURL(e.target.result, function(img) {
            img.scaleToWidth(150);
            img.set({ left: 100, top: 100 });
            fabricCanvas.add(img);
            fabricCanvas.setActiveObject(img);
        });
    };
    reader.readAsDataURL(file);

    // Also upload to server
    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]').value);

    fetch('{{ route("customization.upload-image") }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value }
    }).then(r => r.json()).then(data => {
        if (data.success) console.log('Image uploaded:', data.url);
    });
}

function deleteSelected() {
    const active = fabricCanvas.getActiveObject();
    if (active) {
        fabricCanvas.remove(active);
        fabricCanvas.renderAll();
    }
}

function saveDesign() {
    const designJson = fabricCanvas.toJSON();
    if (Array.isArray(designJson.objects)) {
        designJson.objects = designJson.objects.filter((obj) => !(obj?.data?.isCanvasBaseImage));
    }
    const designData = JSON.stringify(designJson);
    const designImage = fabricCanvas.toDataURL({ format: 'png', quality: 1 });

    // Save design to server
    fetch('{{ route("customization.save-design") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: currentProductId,
            design_data: designData,
            design_image: designImage,
            _token: document.querySelector('input[name="_token"]').value
        })
    }).then(r => r.json()).then(data => {
        if (data.success) {
            document.getElementById('customization_data_input').value = designData;
            document.getElementById('customization_image_input').value = data.image_path;
            closeCustomizer();
            window.location.href = '{{ route("products.show", $product->slug) }}';
        }
    });
}

// Keyboard delete
document.addEventListener('keydown', function(e) {
    if (e.key === 'Delete' && fabricCanvas) {
        deleteSelected();
    }
});
</script>
@endpush
@endif
