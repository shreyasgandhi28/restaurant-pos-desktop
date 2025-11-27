@extends('layouts.app')

@section('title', 'Edit Menu Item')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Menu Item</h1>
                <p class="text-gray-600">Update menu item information</p>
            </div>
            <a href="{{ route('menu-items.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded transition">
                Back to Menu
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('menu-items.update', $menuItem) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Item Name *
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $menuItem->name) }}"
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
                            <option value="{{ $category->id }}" {{ old('category_id', $menuItem->category_id) == $category->id ? 'selected' : '' }}>
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
                              placeholder="Brief description of the item">{{ old('description', $menuItem->description) }}</textarea>
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
                           value="{{ old('price', $menuItem->price) }}"
                           step="0.01"
                           min="0"
                           class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('price') border-red-500 @enderror"
                           required
                           placeholder="0.00">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Image -->
                @if($menuItem->image)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Current Image
                        </label>
                        <div class="flex items-start gap-4">
                            <img src="{{ asset('storage/' . $menuItem->image) }}" 
                                 alt="{{ $menuItem->name }}" 
                                 class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                            <div class="flex-1">
                                <p class="text-sm text-gray-600 mb-3">Upload a new image to replace the current one</p>
                                <button type="button" 
                                        onclick="removeImage()"
                                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded transition text-sm">
                                    Remove Image
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Image -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $menuItem->image ? 'Replace Image' : 'Upload Image' }}
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
                    
                    <!-- Hidden field to track image removal -->
                    <input type="hidden" name="remove_image" id="remove_image" value="0">
                </div>

                <!-- Is Available -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_available" 
                           id="is_available" 
                           value="1"
                           {{ old('is_available', $menuItem->is_available) ? 'checked' : '' }}
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
                           {{ old('is_vegetarian', $menuItem->is_vegetarian) ? 'checked' : '' }}
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
                           {{ old('is_spicy', $menuItem->is_spicy) ? 'checked' : '' }}
                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                    <label for="is_spicy" class="ml-2 block text-sm text-gray-700">
                        Spicy item
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="mt-6 flex items-center justify-between">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded transition">
                    Update Menu Item
                </button>
                <a href="{{ route('menu-items.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Item Information -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">Item Information</h3>
        <div class="text-sm text-blue-800 space-y-1">
            <p><strong>Created:</strong> {{ $menuItem->created_at->format('M d, Y h:i A') }}</p>
            <p><strong>Last Updated:</strong> {{ $menuItem->updated_at->format('M d, Y h:i A') }}</p>
        </div>
    </div>
    
    <!-- Hidden form for image removal -->
    @if($menuItem->image)
        <form id="remove-image-form" action="{{ route('menu-items.update', $menuItem) }}" method="POST" style="display: none;">
            @csrf
            @method('PUT')
            <input type="hidden" name="category_id" value="{{ $menuItem->category_id }}">
            <input type="hidden" name="name" value="{{ $menuItem->name }}">
            <input type="hidden" name="description" value="{{ $menuItem->description }}">
            <input type="hidden" name="price" value="{{ $menuItem->price }}">
            <input type="hidden" name="is_available" value="{{ $menuItem->is_available ? '1' : '0' }}">
            <input type="hidden" name="remove_image" value="1">
        </form>
    @endif
</div>

<script>
function removeImage() {
    // Use the same confirmation modal as delete operations
    confirmDelete('remove-image-form', 'Are you sure you want to remove the current image?');
}
</script>
@endsection
