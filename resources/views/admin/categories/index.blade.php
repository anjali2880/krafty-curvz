@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="bg-amber-700 hover:bg-amber-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Add Category</a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Main Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Products</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($categories as $category)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $category->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $category->parent?->name ?? 'Main Category' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $category->slug }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $category->products_count }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
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
    <div class="p-4 border-t border-gray-200">
        {{ $categories->withQueryString()->links() }}
    </div>
</div>
@endsection
