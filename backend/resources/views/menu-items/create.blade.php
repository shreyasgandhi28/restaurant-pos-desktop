@extends('layouts.app')

@section('title', 'Create Menu Item')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create Menu Item</h1>
                <p class="text-gray-600">Add a new item to your menu</p>
            </div>
            <a href="{{ route('menu-items.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded transition">
                Back to Menu
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('menu-items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Item Name *
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}"
                           class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                           required
                           placeholder="e.g., Margherita Pizza">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Category *
                    </label>
                    <select name="category_id" 
                            id="category_id" 
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('category_id') border-red-500 @enderror"
                            required>
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="3"
                              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror"
                              placeholder="Brief description of the item">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                        Price (₹) *
                    </label>
                    <input type="number" 
                           name="price" 
                           id="price" 
                           value="{{ old('price') }}"
                           step="0.01"
                           min="0"
                           class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('price') border-red-500 @enderror"
                           required
                           placeholder="0.00">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                        Image
                    </label>
                    <input type="file" 
                           name="image" 
                           id="image" 
                           accept="image/*"
                           class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('image') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Accepted formats: JPG, PNG, GIF (Max: 2MB)</p>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Available -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_available" 
                           id="is_available" 
                           value="1"
                           {{ old('is_available', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_available" class="ml-2 block text-sm text-gray-700">
                        Item is available for ordering
                    </label>
                </div>

                <!-- Is Vegetarian -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_vegetarian" 
                           id="is_vegetarian" 
                           value="1"
                           {{ old('is_vegetarian') ? 'checked' : '' }}
                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                    <label for="is_vegetarian" class="ml-2 block text-sm text-gray-700">
                        Vegetarian item
                    </label>
                </div>

                <!-- Is Spicy -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_spicy" 
                           id="is_spicy" 
                           value="1"
                           {{ old('is_spicy') ? 'checked' : '' }}
                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                    <label for="is_spicy" class="ml-2 block text-sm text-gray-700">
                        Spicy item
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="mt-6 flex items-center justify-between">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded transition">
                    Create Menu Item
                </button>
                <a href="{{ route('menu-items.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
