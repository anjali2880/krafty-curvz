@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold">Edit Product</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <select name="category_id" id="category_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="">Select Category</option>
                            @foreach($categories->where('parent_id', null) as $mainCategory)
                                <option value="{{ $mainCategory->id }}" data-is-resin="{{ $mainCategory->slug === 'resin-products' ? '1' : '0' }}" {{ old('category_id', $product->category_id) == $mainCategory->id ? 'selected' : '' }}>{{ $mainCategory->name }}</option>
                                @if($mainCategory->children->count() > 0)
                                    @foreach($mainCategory->children as $subcategory)
                                        <option value="{{ $subcategory->id }}" data-is-resin="{{ $mainCategory->slug === 'resin-products' ? '1' : '0' }}" {{ old('category_id', $product->category_id) == $subcategory->id ? 'selected' : '' }}>-- {{ $subcategory->name }}</option>
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                        @error('price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sale Price</label>
                        <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                        @error('sale_price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Order Quantity *</label>
                    <input type="number" name="max_order_quantity" value="{{ old('max_order_quantity', $product->max_order_quantity ?? 10) }}" min="1" max="999" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                    <p class="text-xs text-gray-500 mt-1">Maximum units a customer can add for this product.</p>
                    @error('max_order_quantity') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Sizes</label>
                    <div id="sizes-container" class="space-y-2">
                        @foreach($product->sizes as $index => $size)
                            <div class="flex items-center space-x-2 size-row">
                                <input type="text" name="sizes[{{ $index }}][name]" value="{{ $size->name }}" placeholder="Size name (e.g., Small, 8x10)" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-amber-500 focus:border-amber-500">
                                <input type="number" name="sizes[{{ $index }}][price]" value="{{ $size->price }}" placeholder="Price" step="0.01" min="0" class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-amber-500 focus:border-amber-500">
                                <button type="button" onclick="removeSizeRow(this)" class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endforeach
                        @if($product->sizes->count() === 0)
                            <div class="flex items-center space-x-2 size-row">
                                <input type="text" name="sizes[0][name]" placeholder="Size name (e.g., Small, 8x10)" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-amber-500 focus:border-amber-500">
                                <input type="number" name="sizes[0][price]" placeholder="Price" step="0.01" min="0" class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-amber-500 focus:border-amber-500">
                                <button type="button" onclick="removeSizeRow(this)" class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endif
                    </div>
                    <button type="button" onclick="addSizeRow()" class="mt-2 text-sm text-amber-600 hover:text-amber-700">+ Add Size</button>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="has_shape_options" id="has_shape_options" value="1" {{ old('has_shape_options', $product->has_shape_options) ? 'checked' : '' }} class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                    <label for="has_shape_options" class="ml-2 text-sm text-gray-700">Enable Shape Options</label>
                </div>

                <div id="shape-options-section" class="{{ old('has_shape_options', $product->has_shape_options) ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Available Shapes</label>
                    <div id="shape-options-container" class="space-y-2">
                        @php $shapes = old('shape_options', $product->shape_options ?? ['Round']); @endphp
                        @foreach($shapes as $index => $shape)
                            <div class="flex items-center space-x-2 shape-row">
                                <input type="text" name="shape_options[{{ $index }}]" value="{{ $shape }}" placeholder="Shape name (e.g., Round)" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-amber-500 focus:border-amber-500">
                                <button type="button" onclick="removeShapeRow(this)" class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" onclick="addShapeRow()" class="mt-2 text-sm text-amber-600 hover:text-amber-700">+ Add Shape</button>
                    @error('shape_options') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                <!-- Existing Main Product Images -->
                @php
                    $mainProductImages = $product->images->filter(fn ($img) => !($img->is_canvas_image ?? false))->values();
                    $customizableCanvasImages = $product->images->filter(fn ($img) => (bool) ($img->is_canvas_image ?? false))->values();
                @endphp
                @if($mainProductImages->count() > 0)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Product Images</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach($mainProductImages as $image)
                                <div class="relative group">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="Product image" class="w-24 h-24 object-cover rounded-lg border {{ $image->is_primary ? 'border-amber-500 ring-2 ring-amber-200' : 'border-gray-200' }}">
                                    <div class="absolute inset-0 bg-black/50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center space-x-1">
                                        @if(!$image->is_primary)
                                            <button
                                                type="button"
                                                onclick="submitImageAction('{{ route('admin.products.images.primary', [$product, $image]) }}', 'POST')"
                                                class="text-white text-xs bg-amber-600 px-2 py-1 rounded"
                                                title="Set as primary"
                                            >
                                                Primary
                                            </button>
                                        @endif
                                        <button
                                            type="button"
                                            onclick="confirmImageDelete('{{ route('admin.products.images.delete', [$product, $image]) }}')"
                                            class="text-white text-xs bg-red-600 px-2 py-1 rounded"
                                            title="Delete"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                    @if($image->is_primary)
                                        <span class="absolute -top-1 -right-1 bg-amber-500 text-white text-xs px-1.5 py-0.5 rounded-full">Primary</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $mainProductImages->count() > 0 ? 'Add New Image' : 'Product Image' }}
                    </label>
                    <input type="file" name="images[]" accept="image/*" multiple class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1">Upload product images for this product.</p>
                    @error('images') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                </div>

                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="customizable_product" id="customizable_product" value="1" {{ old('customizable_product', $product->customizable_product) ? 'checked' : '' }} class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                        <label for="customizable_product" class="ml-2 text-sm text-gray-700">Customizable Product</label>
                    </div>
                </div>

                <div id="customizable-image-upload" class="{{ old('customizable_product', $product->customizable_product) ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customizable Product Images</label>
                    <input type="file" name="custom_images[]" accept="image/*" multiple class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1">This upload appears only when Customizable Product is checked.</p>
                    @error('custom_images') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    @error('custom_images.*') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

                    @if($customizableCanvasImages->count() > 0)
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Customizable Product Images</label>
                            <div class="flex flex-wrap gap-3">
                                @foreach($customizableCanvasImages as $image)
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Customizable product image" class="w-24 h-24 object-cover rounded-lg border border-sky-200">
                                        <div class="absolute inset-0 bg-black/50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center space-x-1">
                                            <button
                                                type="button"
                                                onclick="confirmImageDelete('{{ route('admin.products.images.delete', [$product, $image]) }}')"
                                                class="text-white text-xs bg-red-600 px-2 py-1 rounded"
                                                title="Delete"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                        <span class="absolute -top-1 -right-1 bg-sky-500 text-white text-xs px-1.5 py-0.5 rounded-full">Canvas</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                        <label for="is_featured" class="ml-2 text-sm text-gray-700">Featured</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="manage_stock" id="manage_stock" value="1" {{ old('manage_stock', $product->manage_stock) ? 'checked' : '' }} class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                        <label for="manage_stock" class="ml-2 text-sm text-gray-700">Mark As Out of Stock</label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-amber-700 hover:bg-amber-800 text-white rounded-lg text-sm font-medium">Update Product</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let sizeIndex = {{ $product->sizes->count() }};
let shapeIndex = {{ count(old('shape_options', $product->shape_options ?? ['Round'])) }};

function addSizeRow() {
    const container = document.getElementById('sizes-container');
    const newRow = document.createElement('div');
    newRow.className = 'flex items-center space-x-2 size-row';
    newRow.innerHTML = `
        <input type="text" name="sizes[${sizeIndex}][name]" placeholder="Size name (e.g., Small, 8x10)" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-amber-500 focus:border-amber-500">
        <input type="number" name="sizes[${sizeIndex}][price]" placeholder="Price" step="0.01" min="0" class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-amber-500 focus:border-amber-500">
        <button type="button" onclick="removeSizeRow(this)" class="text-red-500 hover:text-red-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    `;
    container.appendChild(newRow);
    sizeIndex++;
}

function removeSizeRow(button) {
    button.parentElement.remove();
}

function addShapeRow() {
    const container = document.getElementById('shape-options-container');
    const row = document.createElement('div');
    row.className = 'flex items-center space-x-2 shape-row';
    row.innerHTML = `
        <input type="text" name="shape_options[${shapeIndex}]" placeholder="Shape name (e.g., Round)" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-amber-500 focus:border-amber-500">
        <button type="button" onclick="removeShapeRow(this)" class="text-red-500 hover:text-red-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    `;
    container.appendChild(row);
    shapeIndex++;
}

function removeShapeRow(button) {
    const container = document.getElementById('shape-options-container');
    if (container.children.length > 1) {
        button.parentElement.remove();
    }
}

document.getElementById('has_shape_options')?.addEventListener('change', function () {
    document.getElementById('shape-options-section')?.classList.toggle('hidden', !this.checked);
});

function toggleCustomizableImageUpload() {
    const customizableCheckbox = document.getElementById('customizable_product');
    const categorySelect = document.getElementById('category_id');
    const imageSection = document.getElementById('customizable-image-upload');
    if (!customizableCheckbox || !categorySelect || !imageSection) return;

    const selectedOption = categorySelect.options[categorySelect.selectedIndex];
    const isResinCategory = selectedOption?.dataset?.isResin === '1';
    const shouldShow = customizableCheckbox.checked && isResinCategory;
    imageSection.classList.toggle('hidden', !shouldShow);
}

document.getElementById('customizable_product')?.addEventListener('change', toggleCustomizableImageUpload);
document.getElementById('category_id')?.addEventListener('change', toggleCustomizableImageUpload);
document.addEventListener('DOMContentLoaded', toggleCustomizableImageUpload);

function submitImageAction(action, method) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);

    if (method !== 'POST') {
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = method;
        form.appendChild(methodInput);
    }

    document.body.appendChild(form);
    form.submit();
}

function confirmImageDelete(action) {
    if (confirm('Delete this image?')) {
        submitImageAction(action, 'DELETE');
    }
}
</script>
@endpush
@endsection
