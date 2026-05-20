@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.categories.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold">Create Category</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                    @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug (auto-generated if empty)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                    @error('slug') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Belongs to Main Category</label>
                    <select name="parent_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="">No Parent (Main Category)</option>
                        @foreach($mainCategories as $mainCategory)
                            <option value="{{ $mainCategory->id }}" {{ old('parent_id') == $mainCategory->id ? 'selected' : '' }}>
                                {{ $mainCategory->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    @error('image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-amber-700 hover:bg-amber-800 text-white rounded-lg text-sm font-medium">Create Category</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
