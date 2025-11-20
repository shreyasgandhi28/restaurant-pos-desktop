@extends('layouts.app')

@section('title', 'Create Table')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create Table</h1>
                <p class="text-gray-600">Add a new table to your restaurant</p>
            </div>
            <a href="{{ route('tables.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded transition">
                Back to Tables
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('tables.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- Table Number -->
                <div>
                    <label for="table_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Table Number *
                    </label>
                    <input type="text"
                           name="table_number"
                           id="table_number"
                           value="{{ old('table_number') }}"
                           class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('table_number') border-red-500 @enderror"
                           required
                           placeholder="e.g., T001, Table 1">
                    @error('table_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Capacity -->
                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">
                        Capacity (Number of Seats) *
                    </label>
                    <input type="number"
                           name="capacity"
                           id="capacity"
                           value="{{ old('capacity', 2) }}"
                           min="1"
                           max="20"
                           class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('capacity') border-red-500 @enderror"
                           required>
                    @error('capacity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status *
                    </label>
                    <select name="status"
                            id="status"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror"
                            required>
                        <option value="available" {{ old('status', 'available') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="occupied" {{ old('status') === 'occupied' ? 'selected' : '' }}>Occupied</option>
                        <option value="reserved" {{ old('status') === 'reserved' ? 'selected' : '' }}>Reserved</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Position X (Optional) -->
                <div>
                    <label for="position_x" class="block text-sm font-medium text-gray-700 mb-2">
                        Position X (Optional)
                    </label>
                    <input type="number"
                           name="position_x"
                           id="position_x"
                           value="{{ old('position_x') }}"
                           step="0.01"
                           class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('position_x') border-red-500 @enderror"
                           placeholder="0.00">
                    <p class="mt-1 text-xs text-gray-500">X coordinate for floor plan layout</p>
                    @error('position_x')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Position Y (Optional) -->
                <div>
                    <label for="position_y" class="block text-sm font-medium text-gray-700 mb-2">
                        Position Y (Optional)
                    </label>
                    <input type="number"
                           name="position_y"
                           id="position_y"
                           value="{{ old('position_y') }}"
                           step="0.01"
                           class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('position_y') border-red-500 @enderror"
                           placeholder="0.00">
                    <p class="mt-1 text-xs text-gray-500">Y coordinate for floor plan layout</p>
                    @error('position_y')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="mt-6 flex items-center justify-between">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded transition">
                    Create Table
                </button>
                <a href="{{ route('tables.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
