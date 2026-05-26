@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5 md:mb-6">
    <div>
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-700">Catalog</p>
        <h1 class="mt-1 text-2xl md:text-3xl font-bold text-gray-950">Categories</h1>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center justify-center bg-amber-700 hover:bg-amber-800 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">Add Category</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full min-w-[780px]">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Main Category</th>
                <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Products</th>
                <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-4 md:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($categories as $category)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 md:px-6 py-4 text-sm font-medium text-gray-800">{{ $category->name }}</td>
                    <td class="px-4 md:px-6 py-4 text-sm text-gray-600">{{ $category->parent?->name ?? 'Main Category' }}</td>
                    <td class="px-4 md:px-6 py-4 text-sm text-gray-500">{{ $category->slug }}</td>
                    <td class="px-4 md:px-6 py-4 text-sm text-gray-600">{{ $category->products_count }}</td>
                    <td class="px-4 md:px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 md:px-6 py-4 text-right whitespace-nowrap space-x-2">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-amber-700 hover:text-amber-800 text-sm font-medium">Edit</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Are you sure?')">
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
        {{ $categories->withQueryString()->links() }}
    </div>
</div>
@endsection
