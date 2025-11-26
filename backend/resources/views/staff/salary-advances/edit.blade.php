@extends('layouts.app')

@section('title', 'Edit Salary Advance')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Salary Advance</h1>
                <p class="text-gray-600">Update salary advance information</p>
            </div>
            <a href="{{ route('staff-salary-advances.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded transition">
                Back to Advances
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('staff-salary-advances.update', $advance) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Staff Member -->
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Staff Member *
                    </label>
                    <select id="employee_id" 
                            name="employee_id" 
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('employee_id') border-red-500 @enderror"
                            required>
                        @foreach($staff as $member)
                            <option value="{{ $member->id }}" {{ old('employee_id', $advance->employee_id) == $member->id ? 'selected' : '' }}>
                                {{ $member->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Amount (₹) *
                    </label>
                    <input type="number" 
                           name="amount" 
                           id="amount" 
                           step="0.01"
                           min="0.01"
                           value="{{ old('amount', $advance->amount) }}"
                           class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('amount') border-red-500 @enderror"
                           required>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Advance Date -->
                <div>
                    <label for="advance_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Advance Date *
                    </label>
                    <input type="date" 
                           name="advance_date" 
                           id="advance_date" 
                           value="{{ old('advance_date', $advance->advance_date->format('Y-m-d')) }}"
                           class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('advance_date') border-red-500 @enderror"
                           required>
                    @error('advance_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                        Payment Method *
                    </label>
                    <select id="payment_method" 
                            name="payment_method" 
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('payment_method') border-red-500 @enderror"
                            required>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method }}" {{ old('payment_method', $advance->payment_method) == $method ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $method)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('payment_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes
                    </label>
                    <textarea name="notes" 
                              id="notes" 
                              rows="3"
                              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('notes') border-red-500 @enderror"
                              placeholder="Add any additional notes here">{{ old('notes', $advance->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="mt-6 flex items-center justify-between">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded transition">
                    Update Advance
                </button>
                <a href="{{ route('staff-salary-advances.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Advance Information -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">Advance Information</h3>
        <div class="text-sm text-blue-800 space-y-1">
            <p><strong>Advance ID:</strong> #{{ $advance->id }}</p>
            <p><strong>Created:</strong> {{ $advance->created_at->format('M d, Y h:i A') }}</p>
            <p><strong>Last Updated:</strong> {{ $advance->updated_at->format('M d, Y h:i A') }}</p>
            @if($advance->approvedBy)
                <p><strong>Approved By:</strong> {{ $advance->approvedBy->name }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
