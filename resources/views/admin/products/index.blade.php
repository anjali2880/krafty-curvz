@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Products</h1>
    <a href="{{ route('admin.products.create') }}" class="bg-amber-700 hover:bg-amber-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Add Product</a>
</div>

<div class="bg-white rounded-xl shadow-sm p-4 mb-4">
    <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
        <div class="sm:w-80">
            <label for="product_name" class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
            <input
                type="text"
                id="product_name"
                name="product_name"
                value="{{ request('product_name') }}"
                placeholder="Search by product name"
                class="h-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500"
            >
        </div>
        <div class="sm:w-80">
            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Main Category</label>
            <select name="category_id" id="category_id" class="h-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                <option value="">All Categories</option>
                @foreach($categories as $mainCategory)
                    <option value="{{ $mainCategory->id }}" {{ request('category_id') == $mainCategory->id ? 'selected' : '' }}>
                        {{ $mainCategory->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="sm:w-80">
            <label for="subcategory_id" class="block text-sm font-medium text-gray-700 mb-1">Sub Category</label>
            <select name="subcategory_id" id="subcategory_id" class="h-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                <option value="">All Sub Categories</option>
            </select>
        </div>
        <div class="sm:w-56">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" id="status" class="h-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                <option value="">All Statuses</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="unpublished" {{ request('status') === 'unpublished' ? 'selected' : '' }}>Unpublished</option>
            </select>
        </div>
        <div class="flex items-end gap-2 sm:pb-0.5">
            <button type="submit" class="h-10 px-4 bg-amber-700 hover:bg-amber-800 text-white rounded-lg text-sm font-medium transition-colors inline-flex items-center justify-center">Filter</button>
            @if(request()->filled('product_name') || request()->filled('category_id') || request()->filled('subcategory_id') || request()->filled('status'))
                <a href="{{ route('admin.products.index') }}" class="h-10 px-4 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors inline-flex items-center justify-center">Clear</a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customizable</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                @if($product->images->count() > 0)
                                    <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-lg object-cover">
                                @else
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $product->name }}</p>
                                    @if($product->is_featured)
                                        <span class="text-xs text-amber-600">Featured</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->category->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($product->sale_price)
                                <span class="font-medium text-amber-700">&#8377;{{ number_format($product->sale_price, 0) }}</span>
                                <span class="text-gray-400 line-through text-xs ml-1">&#8377;{{ number_format($product->price, 0) }}</span>
                            @else
                                <span class="font-medium">&#8377;{{ number_format($product->price, 0) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($product->customizable_product)
                                <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700">Yes</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $product->is_active ? 'Published' : 'Unpublished' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <form method="POST" action="{{ route('admin.products.toggle-publish', $product) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sm font-medium {{ $product->is_active ? 'text-red-600 hover:text-red-700' : 'text-green-600 hover:text-green-700' }}">
                                    {{ $product->is_active ? 'Unpublish' : 'Publish' }}
                                </button>
                            </form>
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-amber-700 hover:text-amber-800 text-sm font-medium">Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-200">
        {{ $products->withQueryString()->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const categorySelect = document.getElementById('category_id');
        const subcategorySelect = document.getElementById('subcategory_id');
        if (!categorySelect || !subcategorySelect) return;

        const subcategoryMap = {
            @foreach($categories as $mainCategory)
                "{{ $mainCategory->id }}": [
                    @foreach($mainCategory->children as $subcategory)
                        { id: "{{ $subcategory->id }}", name: "{{ addslashes($subcategory->name) }}" },
                    @endforeach
                ],
            @endforeach
        };

        const selectedSubcategory = "{{ request('subcategory_id', '') }}";

        function populateSubcategories(categoryId) {
            subcategorySelect.innerHTML = '<option value="">All Sub Categories</option>';
            if (!categoryId || !subcategoryMap[categoryId] || subcategoryMap[categoryId].length === 0) {
                subcategorySelect.disabled = true;
                return;
            }

            subcategorySelect.disabled = false;
            subcategoryMap[categoryId].forEach((subcategory) => {
                const option = document.createElement('option');
                option.value = subcategory.id;
                option.textContent = subcategory.name;
                if (subcategory.id === selectedSubcategory) {
                    option.selected = true;
                }
                subcategorySelect.appendChild(option);
            });
        }

        populateSubcategories(categorySelect.value);

        categorySelect.addEventListener('change', function () {
            populateSubcategories(this.value);
        });
    })();
</script>
@endpush
