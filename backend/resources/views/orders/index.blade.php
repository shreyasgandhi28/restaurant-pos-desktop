@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Orders</h1>
            <p class="text-gray-600">View and manage all orders</p>
        </div>
        <a href="{{ route('orders.create-miscellaneous') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md text-sm transition-colors flex items-center">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Add Miscellaneous Order
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-3 mb-6">
        <form method="GET" action="{{ route('orders.index') }}" class="flex flex-wrap items-center gap-3">
            <!-- Search -->
            <div class="flex-1 min-w-60">
                <div class="relative">
                    <input type="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search orders..."
                           class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            
            <!-- Date Filter -->
            <div class="flex items-center space-x-2">
                <div class="w-40">
                    <input type="date"
                           name="from_date"
                           value="{{ request('from_date') }}"
                           class="w-full h-10 py-2 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                           placeholder="From Date"
                           title="From Date">
                </div>
                <span class="text-gray-500">-</span>
                <div class="w-40">
                    <input type="date"
                           name="to_date"
                           value="{{ request('to_date') }}"
                           class="w-full h-10 py-2 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                           placeholder="To Date"
                           title="To Date">
                </div>
            </div>

            <!-- Payment Status Filter -->
            <div class="w-32">
                <select name="payment_status"
                        class="w-full h-10 py-2 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="all" {{ request('payment_status') === 'all' || !request('payment_status') ? 'selected' : '' }}>All Payments</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="cancelled" {{ request('payment_status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Table Filter -->
            <div class="w-24">
                <input type="text"
                       name="table"
                       value="{{ request('table') }}"
                       placeholder="Table"
                       class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Server Filter -->
            <div class="w-32">
                <input type="text"
                       name="server"
                       value="{{ request('server') }}"
                       placeholder="Server"
                       class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Filter Button -->
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md text-sm transition-colors">
                Filter
            </button>

            <!-- Clear Filters -->
            @if(request()->hasAny(['search', 'status', 'payment_status', 'table', 'server']))
                <a href="{{ route('orders.index') }}" class="text-gray-600 hover:text-gray-800 font-medium py-2 px-3 text-sm">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @php
                        $sortableHeaders = [
                            'order_number' => 'Order #',
                            'bill_number' => 'Bill #',
                            'table' => 'Table',
                            'server' => 'Server',
                            'payment_status' => 'Payment Status',
                            'amount_paid' => 'Amount Paid',
                            'created_at' => 'Created'
                        ];
                    @endphp
                    
                    @foreach($sortableHeaders as $key => $label)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer group hover:bg-gray-100" 
                            onclick="window.location.href='{{ route('orders.index', array_merge(request()->query(), ['sort_by' => $key, 'sort_order' => request('sort_by') == $key && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}'">
                            <div class="flex items-center space-x-1">
                                <span>{{ $label }}</span>
                                @if(request('sort_by') == $key)
                                    @if(request('sort_order') == 'asc')
                                        <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    @else
                                        <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    @endif
                                @else
                                    <svg class="w-3 h-3 text-gray-300 group-hover:text-gray-400 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                @endif
                            </div>
                        </th>
                    @endforeach
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $order->bill ? $order->bill->bill_number : '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($order->restaurantTable)
                                    {{ $order->restaurantTable->table_number }}
                                @elseif($order->type === 'miscellaneous')
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Misc</span>
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $order->user->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $paymentStatus = $order->bill ? $order->bill->status : 'unpaid';
                                $statusClass = [
                                    'paid' => 'bg-green-100 text-green-800',
                                    'partially_paid' => 'bg-blue-100 text-blue-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'unpaid' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-yellow-100 text-yellow-800',
                                    'refunded' => 'bg-purple-100 text-purple-800'
                                ][$paymentStatus] ?? 'bg-gray-100 text-gray-800';
                                $statusDisplay = [
                                    'paid' => 'Paid',
                                    'pending' => 'Pending',
                                    'cancelled' => 'Cancelled'
                                ][$paymentStatus] ?? ucfirst($paymentStatus);
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ $statusDisplay }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">
                                @if($order->bill)
                                    ₹{{ number_format($order->bill->amount_paid, 2) }}
                                    @if($order->bill->status !== 'paid' && $order->bill->total_amount > 0)
                                        <span class="text-xs text-gray-500">/ ₹{{ number_format($order->bill->total_amount, 2) }}</span>
                                    @endif
                                @else
                                    ₹0.00
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex justify-end">
                                <x-bill.generate-button :order="$order" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No orders</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new order from a table.</p>
                            <div class="mt-6">
                                <a href="{{ route('tables.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Go to Tables
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif


@endsection
