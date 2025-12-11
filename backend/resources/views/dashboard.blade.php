@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600">Restaurant analytics and insights</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-indigo-500">
            <div class="text-right">
                <div class="text-sm font-medium text-gray-500">{{ now()->format('l, F j, Y') }}</div>
                <div id="current-time" class="text-xl font-bold text-indigo-600">{{ now()->format('g:i A') }}</div>
            </div>
        </div>
    </div>

    @role('admin|manager')
    <!-- Full Dashboard for Admin/Manager -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Tables -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Tables</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalTables }}</p>
                </div>
            </div>
        </div>

        <!-- Available Tables -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Available</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $availableTables }}</p>
                </div>
            </div>
        </div>

        <!-- Active Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $activeOrders }}</p>
                </div>
            </div>
        </div>

        <!-- Today's Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ $period === 'daily' ? 'Today\'s' : ($period === 'monthly' ? 'This Month\'s' : 'This Year\'s') }} Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">₹{{ number_format($todayRevenue, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Limited Dashboard for Staff -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Tables -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Tables</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalTables }}</p>
                </div>
            </div>
        </div>

        <!-- Available Tables -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Available</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $availableTables }}</p>
                </div>
            </div>
        </div>

        <!-- Active Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $activeOrders }}</p>
                </div>
            </div>
        </div>
    </div>
    @endrole

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Orders</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bill #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Table</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentOrders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->order_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->bill ? $order->bill->bill_number : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->restaurantTable ? $order->restaurantTable->table_number : ($order->type === 'miscellaneous' ? 'Misc' : '-') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($order->bill)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($order->bill->status === 'paid') bg-green-100 text-green-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($order->bill->status) }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Unpaid
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ $order->orderItems->count() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₹{{ number_format($order->total, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->diffForHumans() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex justify-end">
                                    <x-bill.generate-button :order="$order" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No recent orders</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @role('admin|manager')
    <!-- Revenue Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        <!-- Daily Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Daily Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">₹{{ number_format($revenueData['daily'] ?? 0, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Payment Method Breakdown -->
            <div class="pt-4 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-4">Breakdown</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8">
                    <!-- Cash -->
                    <div class="flex items-center pr-6">
                        <div class="flex items-center w-24">
                            <svg class="w-4 h-4 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Cash</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">₹{{ number_format($todayPaymentBreakdown['cash'] ?? 0, 2) }}</span>
                    </div>
                    
                    <!-- UPI -->
                    <div class="flex items-center pl-6">
                        <div class="flex items-center w-24">
                            <svg class="w-4 h-4 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">UPI</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">₹{{ number_format($todayPaymentBreakdown['upi'] ?? 0, 2) }}</span>
                    </div>
                    
                    <!-- Card -->
                    <div class="flex items-center pr-6">
                        <div class="flex items-center w-24">
                            <svg class="w-4 h-4 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Card</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">₹{{ number_format($todayPaymentBreakdown['card'] ?? 0, 2) }}</span>
                    </div>
                    
                    <!-- Other -->
                    <div class="flex items-center pl-6">
                        <div class="flex items-center w-24">
                            <svg class="w-4 h-4 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Other</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">₹{{ number_format($todayPaymentBreakdown['other'] ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Monthly Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">₹{{ number_format($revenueData['monthly'] ?? 0, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Payment Method Breakdown -->
            <div class="pt-4 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-4">Breakdown</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8">
                    <!-- Cash -->
                    <div class="flex items-center pr-6">
                        <div class="flex items-center w-24">
                            <svg class="w-4 h-4 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Cash</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">₹{{ number_format($monthlyPaymentBreakdown['cash'] ?? 0, 2) }}</span>
                    </div>
                    
                    <!-- UPI -->
                    <div class="flex items-center pl-6">
                        <div class="flex items-center w-24">
                            <svg class="w-4 h-4 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">UPI</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">₹{{ number_format($monthlyPaymentBreakdown['upi'] ?? 0, 2) }}</span>
                    </div>
                    
                    <!-- Card -->
                    <div class="flex items-center pr-6">
                        <div class="flex items-center w-24">
                            <svg class="w-4 h-4 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Card</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">₹{{ number_format($monthlyPaymentBreakdown['card'] ?? 0, 2) }}</span>
                    </div>
                    
                    <!-- Other -->
                    <div class="flex items-center pl-6">
                        <div class="flex items-center w-24">
                            <svg class="w-4 h-4 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Other</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">₹{{ number_format($monthlyPaymentBreakdown['other'] ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yearly Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Yearly Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">₹{{ number_format($revenueData['yearly'] ?? 0, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Payment Method Breakdown -->
            <div class="pt-4 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-4">Breakdown</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8">
                    <!-- Cash -->
                    <div class="flex items-center pr-6">
                        <div class="flex items-center w-24">
                            <svg class="w-4 h-4 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Cash</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">₹{{ number_format($yearlyPaymentBreakdown['cash'] ?? 0, 2) }}</span>
                    </div>
                    
                    <!-- UPI -->
                    <div class="flex items-center pl-6">
                        <div class="flex items-center w-24">
                            <svg class="w-4 h-4 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">UPI</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">₹{{ number_format($yearlyPaymentBreakdown['upi'] ?? 0, 2) }}</span>
                    </div>
                    
                    <!-- Card -->
                    <div class="flex items-center pr-6">
                        <div class="flex items-center w-24">
                            <svg class="w-4 h-4 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Card</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">₹{{ number_format($yearlyPaymentBreakdown['card'] ?? 0, 2) }}</span>
                    </div>
                    
                    <!-- Other -->
                    <div class="flex items-center pl-6">
                        <div class="flex items-center w-24">
                            <svg class="w-4 h-4 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Other</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">₹{{ number_format($yearlyPaymentBreakdown['other'] ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Items & Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Popular Items -->
        <div class="lg:col-span-1 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Popular Items</h3>
            <div class="space-y-3">
                @forelse($popularItems as $item)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $item->menuItem->name }}</p>
                            <p class="text-sm text-gray-500">{{ $item->menuItem->category->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">{{ $item->total_quantity }} sold</p>
                            <p class="text-sm text-gray-500">₹{{ number_format($item->total_revenue, 2) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">No popular items data</p>
                @endforelse
            </div>
        </div>

        <!-- Charts Column -->
        <div class="lg:col-span-2 flex flex-col h-full">
            <!-- Peak Hours Chart -->
            <div class="bg-white rounded-lg shadow p-6 mb-6 flex-1 flex flex-col">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Peak Hours (10 AM - 10 PM)</h3>
                    <div class="flex items-center">
                        <label for="dateSelect" class="text-xs text-gray-600 mr-3">View:</label>
                        <select id="dateSelect" class="w-32 h-8 text-xs rounded border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="overall">Overall</option>
                            @foreach($availableDates as $date => $label)
                                <option value="{{ $date }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="relative flex-1">
                    <canvas id="peakHoursChart"></canvas>
                </div>
            </div>

            <!-- Monthly Revenue Chart -->
            <div class="bg-white rounded-lg shadow p-6 flex-1 flex flex-col">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Monthly Revenue</h3>
                    <div class="flex items-center">
                        <label for="yearSelect" class="text-xs text-gray-600 mr-3">Year:</label>
                        <select id="yearSelect" class="w-[5.5rem] pl-2 h-8 text-xs rounded border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="relative flex-1">
                    <canvas id="monthlyRevenueChart"></canvas>
                </div>
            </div>
        </div>
        
        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Make charts fill their containers
                function resizeCharts() {
                    const chartContainers = document.querySelectorAll('.relative.flex-1');
                    chartContainers.forEach(container => {
                        const height = container.offsetHeight;
                        const canvas = container.querySelector('canvas');
                        if (canvas) {
                            canvas.style.width = '100%';
                            canvas.style.height = height + 'px';
                            if (canvas.chart) {
                                canvas.chart.resize();
                            }
                        }
                    });
                }

                // Initial resize
                window.addEventListener('resize', resizeCharts);
                
                // Peak Hours Chart
                const peakHoursCtx = document.getElementById('peakHoursChart').getContext('2d');
                let peakHoursChart;

                function loadPeakHoursData(date) {
                    console.log('Loading peak hours data for:', date);
                    // Show loading state
                    const loadingElement = document.createElement('div');
                    loadingElement.className = 'absolute inset-0 flex items-center justify-center bg-white bg-opacity-75';
                    loadingElement.innerHTML = '<div class="text-gray-500">Loading...</div>';
                    const chartContainer = document.querySelector('#peakHoursChart').parentNode;
                    chartContainer.style.position = 'relative';
                    chartContainer.appendChild(loadingElement);

                    // Fetch data for the selected date or overall
                    const url = date === 'overall' 
                        ? '/dashboard/peak-hours/overall' 
                        : `/dashboard/peak-hours/date/${date}`;

                    console.log('Fetching from URL:', url);
                    
                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Received data:', data);
                            // Remove loading state
                            chartContainer.removeChild(loadingElement);

                            const chartData = {
                                labels: data.labels,
                                datasets: [{
                                    label: data.label,
                                    data: data.data,
                                    borderColor: 'rgb(79, 70, 229)',
                                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                    pointBackgroundColor: 'rgb(79, 70, 229)',
                                    pointBorderColor: '#fff',
                                    pointHoverBackgroundColor: '#fff',
                                    pointHoverBorderColor: 'rgb(79, 70, 229)'
                                }]
                            };

                            // If chart already exists, update it
                            if (peakHoursChart) {
                                peakHoursChart.data = chartData;
                                peakHoursChart.update();
                            } else {
                                // Create new chart
                                peakHoursChart = new Chart(peakHoursCtx, {
                                    type: 'line',
                                    data: chartData,
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: { display: false },
                                            tooltip: {
                                                displayColors: false,
                                                callbacks: {
                                                    label: (context) => `${context.parsed.y.toFixed(1)} orders`
                                                }
                                            }
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                ticks: { 
                                                    precision: 0,
                                                    stepSize: 1
                                                }
                                            }
                                        }
                                    }
                                });
                                document.getElementById('peakHoursChart').chart = peakHoursChart;
                            }
                        })
                        .catch(error => {
                            console.error('Error loading peak hours data:', error);
                            chartContainer.removeChild(loadingElement);
                            alert('Failed to load peak hours data. Please try again.');
                        });
                }

                // Initial chart load with overall data
                console.log('Initializing peak hours chart...');
                loadPeakHoursData('overall');

                // Handle date change
                document.getElementById('dateSelect').addEventListener('change', function() {
                    loadPeakHoursData(this.value);
                });

                // Monthly Revenue Chart
                const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
                let monthlyRevenueChart;

                function loadMonthlyRevenueChart(year) {
                    // Show loading state
                    const loadingElement = document.createElement('div');
                    loadingElement.className = 'absolute inset-0 flex items-center justify-center bg-white bg-opacity-75';
                    loadingElement.innerHTML = '<div class="text-gray-500">Loading...</div>';
                    const chartContainer = document.querySelector('#monthlyRevenueChart').parentNode;
                    chartContainer.style.position = 'relative';
                    chartContainer.appendChild(loadingElement);

                    // Fetch data for the selected year
                    fetch(`/dashboard/monthly-revenue?year=${year}`)
                        .then(response => response.json())
                        .then(data => {
                            // Remove loading state
                            chartContainer.removeChild(loadingElement);

                            const chartData = {
                                labels: data.labels,
                                datasets: [{
                                    label: 'Revenue (₹)',
                                    data: data.data,
                                    breakdown: data.breakdown, // Pass breakdown data to dataset
                                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                                    borderColor: 'rgb(16, 185, 129)',
                                    borderWidth: 1,
                                    borderRadius: 4,
                                    pointStyle: 'circle' // Ensure circle point style for tooltip
                                }]
                            };

                            // If chart already exists, update it
                            if (monthlyRevenueChart) {
                                monthlyRevenueChart.data = chartData;
                                monthlyRevenueChart.update();
                            } else {
                                // Create new chart
                                monthlyRevenueChart = new Chart(monthlyCtx, {
                                    type: 'bar',
                                    data: chartData,
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: { display: false },
                                            tooltip: {
                                                displayColors: false,
                                                callbacks: {
                                                    label: (context) => `Total: ₹${context.parsed.y.toLocaleString('en-IN')}`,
                                                    afterBody: (context) => {
                                                        const dataIndex = context[0].dataIndex;
                                                        const breakdown = context[0].dataset.breakdown[dataIndex];
                                                        const lines = [];
                                                        
                                                        if (breakdown) {
                                                            lines.push(''); // Spacer
                                                            if (breakdown.cash > 0) lines.push(`Cash: ₹${breakdown.cash.toLocaleString('en-IN')}`);
                                                            if (breakdown.upi > 0) lines.push(`UPI: ₹${breakdown.upi.toLocaleString('en-IN')}`);
                                                            if (breakdown.card > 0) lines.push(`Card: ₹${breakdown.card.toLocaleString('en-IN')}`);
                                                            if (breakdown.other > 0) lines.push(`Other: ₹${breakdown.other.toLocaleString('en-IN')}`);
                                                        }
                                                        
                                                        return lines;
                                                    }
                                                }
                                            }
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                ticks: {
                                                    callback: (value) => `₹${value.toLocaleString('en-IN')}`
                                                }
                                            }
                                        }
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error loading monthly revenue data:', error);
                            chartContainer.removeChild(loadingElement);
                            alert('Failed to load monthly revenue data. Please try again.');
                        });
                }

                // Initial chart load
                loadMonthlyRevenueChart({{ $currentYear }});

                // Handle year change
                document.getElementById('yearSelect').addEventListener('change', function() {
                    loadMonthlyRevenueChart(this.value);
                });
            });
        </script>
        @endpush
    </div>

    @endrole
</div>

@push('scripts')
<script>
    // Update time every second
    function updateTime() {
        const now = new Date();
        const timeElement = document.getElementById('current-time');
        
        // Format time as 12-hour with AM/PM
        let hours = now.getHours();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        const minutes = String(now.getMinutes()).padStart(2, '0');
        
        timeElement.textContent = `${hours}:${minutes} ${ampm}`;
    }

    // Update time immediately and then every second
    updateTime();
    setInterval(updateTime, 60000); // Update every minute
</script>
@endpush

@endsection
