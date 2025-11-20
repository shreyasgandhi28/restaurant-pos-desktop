@extends('layouts.app')

@section('title', 'Staff Salary Advances')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Staff Salary Advances</h1>
            <p class="text-gray-600">View and manage staff salary advances</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('staff.index') }}" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                Manage Staff List
            </a>
            <button 
                onclick="document.getElementById('addAdvanceModal').classList.remove('hidden')" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Advance
            </button>
        </div>
    </div>

    <!-- Simple Stats Row -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Advances</dt>
                <dd class="mt-1 text-2xl font-semibold text-gray-900">₹{{ number_format($total_advances, 2) }}</dd>
            </div>
            <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">This Month</dt>
                <dd class="mt-1 text-2xl font-semibold text-gray-900">₹{{ number_format($monthly_advances, 2) }}</dd>
            </div>
            <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Active Staff</dt>
                <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $staff->count() }}</dd>
            </div>
        </dl>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <form method="GET" action="{{ route('staff-salary-advances.index') }}" class="space-y-3">
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Search -->
                    <div class="flex-1 min-w-[200px]">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search staff or notes..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Staff Filter -->
                    <div class="w-40">
                        <select name="staff_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            <option value="">All Staff</option>
                            @foreach($staff as $staffMember)
                                <option value="{{ $staffMember->id }}" {{ request('staff_id') == $staffMember->id ? 'selected' : '' }}>
                                    {{ $staffMember->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Method Filter -->
                    <div class="w-40">
                        <select name="payment_method"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            <option value="">All Payments</option>
                            <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="upi" {{ request('payment_method') === 'upi' ? 'selected' : '' }}>UPI</option>
                            <option value="cheque" {{ request('payment_method') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="other" {{ request('payment_method') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <!-- Date Filter -->
                    <div class="w-40">
                        <input type="date"
                               name="date"
                               value="{{ request('date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Filter Buttons -->
                    <div class="flex-shrink-0 flex items-center">
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'staff_id', 'payment_method', 'date']))
                            <a href="{{ route('staff-salary-advances.index') }}" 
                               class="ml-2 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Clear
                            </a>
                        @endif
                    </div>
                </div>
                
                <div class="mt-2">
                    <div class="text-sm text-gray-500">
                        Showing {{ $advances->firstItem() }} to {{ $advances->lastItem() }} of {{ $advances->total() }} results
                    </div>
                </div>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="advancesTableBody">
                    @foreach($advances as $advance)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $advance->user->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">₹{{ number_format($advance->amount, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $advance->advance_date->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $paymentMethod = match($advance->payment_method) {
                                    'upi' => 'UPI',
                                    'cash' => 'Cash',
                                    'cheque' => 'Cheque',
                                    'other' => 'Other',
                                    default => ucfirst(str_replace('_', ' ', $advance->payment_method))
                                };
                                $bgColor = match($advance->payment_method) {
                                    'cash' => 'bg-green-100 text-green-800',
                                    'upi' => 'bg-blue-100 text-blue-800 font-bold',
                                    'cheque' => 'bg-yellow-100 text-yellow-800',
                                    'other' => 'bg-gray-100 text-gray-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $bgColor }} uppercase">
                                {{ $paymentMethod }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $advance->notes ? Str::limit($advance->notes, 30) : '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex justify-end space-x-1">
                                <a href="{{ route('staff-salary-advances.show', $advance) }}" 
                                   class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-indigo-500">
                                    View
                                </a>
                                <a href="{{ route('staff-salary-advances.edit', $advance) }}" 
                                   class="inline-flex items-center px-2 py-1 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-indigo-500">
                                    Edit
                                </a>
                                @if($advance->status === 'pending')
                                <button class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-green-500" 
                                        title="Approve">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-4 bg-gray-50">
            {{ $advances->links() }}
        </div>
    </div>
    </div>
</div>

<!-- Add Staff Modal -->
<div id="addStaffModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3 text-left">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Add New Staff</h3>
            <form id="addStaffForm" action="{{ route('staff.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" id="name" name="name" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="mb-4">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select id="role" name="role" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="waiter">Waiter</option>
                        <option value="staff">Staff</option>
                        <option value="manager">Manager</option>
                    </select>
                </div>
                <div class="flex items-center justify-end pt-2">
                    <button type="button" 
                            onclick="document.getElementById('addStaffModal').classList.add('hidden')" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="ml-3 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Add Staff
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Advance Modal -->
<div id="addAdvanceModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3 text-left">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Add Salary Advance</h3>
            <form id="addAdvanceForm" action="{{ route('staff-salary-advances.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Staff Member</label>
                    <select id="user_id" name="user_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Staff</option>
                        @foreach($staff as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (₹)</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0.01" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="mb-4">
                    <label for="advance_date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" id="advance_date" name="advance_date" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="mb-4">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select id="payment_method" name="payment_method" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">UPI</option>
                        <option value="cheque">Cheque</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea id="notes" name="notes" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                <div class="flex items-center justify-end pt-2">
                    <button type="button" 
                            onclick="document.getElementById('addAdvanceModal').classList.add('hidden')" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="ml-3 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save Advance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Error Messages -->
@if(session('error') || $errors->any())
<div class="mb-6">

    @if(session('error'))
    <div class="mt-2 p-4 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="mt-2 p-4 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
@endif

@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    // Initialize date range picker
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize date range picker
        $('input[name="date_range"]').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                format: 'YYYY-MM-DD',
                applyLabel: 'Apply',
                cancelLabel: 'Clear',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            },
            opens: 'left',
            autoApply: true,
            showDropdowns: true,
            linkedCalendars: false,
        });

        $('input[name="date_range"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('input[name="date_range"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        // Auto-hide success/error messages after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 1s ease-in-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 1000);
            }, 5000);
        });

        // Simple table filtering
        function filterTable(searchTerm) {
            const rows = document.querySelectorAll('#advancesTableBody tr');
            searchTerm = searchTerm.toLowerCase();
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    }

    // Handle form submission with fetch API
    document.getElementById('addAdvanceForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        
        try {
            submitButton.disabled = true;
            submitButton.innerHTML = 'Saving...';
            
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (response.ok) {
                // Show success message and reload
                window.location.href = '{{ route("staff-salary-advances.index") }}';
            } else {
                // Show error message
                const errorMessage = data.message || 'Failed to add advance';
                alert(errorMessage);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        }
    });
</script>
@endpush
