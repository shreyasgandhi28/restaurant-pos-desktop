@extends('layouts.app')

@section('title', 'POS - Point of Sale')

@section('content')

<div class="w-full min-h-screen flex bg-white">
  <!-- LEFT SIDEBAR: Compact vertical menu -->
  <aside class="w-48 lg:w-56 flex-shrink-0 flex flex-col bg-white shadow-none z-10 rounded-none self-stretch border-r border-gray-200">
    <div class="flex items-center justify-center px-5 py-4">
      <span class="font-bold text-xl text-indigo-600">🍽️ POS</span>
    </div>
    <nav class="flex-1 overflow-y-auto px-4 py-4">
      <h3 class="text-xs font-bold text-gray-400 uppercase mb-3 pl-2">Categories</h3>
      <div class="flex flex-col gap-1.5">
        <button 
            onclick="filterCategory('all')" 
            data-category="all"
            class="category-btn w-full text-left px-3 py-2 text-sm font-medium rounded-lg border-2 transition-all transform hover:scale-[1.02]
                   hover:border-indigo-300 hover:shadow-sm
                   focus:outline-none focus:ring-1 focus:ring-indigo-300"
            style="transition: all 0.2s ease-in-out;">
            All Items
        </button>
        @foreach($categories as $category)
            <button 
                onclick="filterCategory('{{ $category->slug ?? 'all' }}')" 
                data-category="{{ $category->slug ?? 'all' }}"
                class="category-btn w-full text-left px-3 py-2 text-sm font-medium text-gray-700 rounded-lg border-2 border-transparent transition-all transform hover:scale-[1.02]
                       hover:border-indigo-300 hover:shadow-sm
                       focus:outline-none focus:ring-1 focus:ring-indigo-300"
                style="transition: all 0.2s ease-in-out;">
                {{ $category->name ?? 'Uncategorized' }}
            </button>
        @endforeach
      </div>
    </nav>
  </aside>
  <!-- MAIN & CART -->
  <main class="flex-1 flex gap-0 px-0 py-6 items-start">
    <!-- MIDDLE: Tables/Menu -->
    <section class="flex-1 min-w-0">
      <div class="bg-white rounded-lg shadow p-6 mb-6">
        <!-- Table Selection -->
        <div class="bg-gray-50 rounded-lg shadow-sm mb-3 p-3">
                <div class="flex items-center justify-start mb-2">
                    <h3 class="text-sm font-semibold text-gray-700">Select Table</h3>
                </div>
                <div class="flex flex-wrap gap-1.5 mb-2">
                    @foreach($tables as $table)
                        <button onclick="selectTable({{ $table->id }}, '{{ $table->table_number }}', '{{ $table->effective_status }}')" 
                                id="table-btn-{{ $table->id }}"
                                data-table-id="{{ $table->id }}"
                                data-status="{{ $table->effective_status }}"
                                class="table-btn px-3 py-1.5 text-xs font-semibold rounded-lg border-2 transition-all transform hover:scale-105
                                       {{ $table->effective_status === 'occupied' ? 'border-orange-400 bg-orange-50 text-orange-700 hover:bg-orange-100' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }} shadow-sm"
                                       style="transition: all 0.2s ease-in-out;">
                            T{{ $table->table_number }}
                        </button>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500" id="selectedTableInfo">No table selected</p>
            </div>

            <!-- Menu Items Grid -->
            <div class="flex-1 bg-white rounded-none lg:rounded-l-none shadow-none p-4 overflow-y-auto">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700">Menu Items</h3>
                    <input type="search" 
                           id="posSearchInput" 
                           class="w-40 px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500"
                           placeholder="Search menu...">
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3">
                    @foreach($menuItems as $item)
                        <div class="menu-item bg-white border border-gray-200 rounded-lg p-2 cursor-pointer hover:border-indigo-500 hover:shadow-sm transition flex flex-col" 
                             data-category="{{ $item->category ? $item->category->slug : 'uncategorized' }}"
                             onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name ?? '') }}', {{ $item->price ?? 0 }}, event)">
                            <div class="w-full h-20 bg-gray-100 rounded-md overflow-hidden">
                                @if($item->image ?? null)
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name ?? 'Menu Item' }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-500 to-violet-400">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                <div class="flex justify-between items-end">
                                    <h4 class="text-xs font-medium text-gray-900 truncate pr-2">{{ $item->name ?? 'Unnamed Item' }}</h4>
                                    <p class="text-xs font-bold text-indigo-600 whitespace-nowrap">₹{{ number_format($item->price ?? 0, 0) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
          </div>
        </section>

        <!-- RIGHT: Cart/Order section -->
        <aside class="w-[380px] min-w-[320px] bg-white rounded-lg shadow p-4 overflow-y-auto" style="max-height: calc(100vh - 3rem);">
            <!-- Current Order Section -->
            <div class="border-b border-gray-200">
                <div class="p-4 bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-extrabold text-white tracking-tight">Current Order</h2>
                        <div class="flex items-center">
                            <span id="currentTableInfo" class="text-white font-bold text-lg bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full">
                                No table selected
                            </span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('pos.store') }}" method="POST" id="orderForm">
                    @csrf
                    <input type="hidden" name="restaurant_table_id" id="tableIdInput">
                    
                    <div class="p-4 max-h-64 overflow-y-auto bg-gray-50" id="cartItems">
                        <p class="text-gray-400 text-center py-8 text-sm">No items added yet</p>
                    </div>

                    <div class="p-4 border-t border-gray-200 space-y-2 bg-white">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-semibold text-gray-900" id="subtotal">₹0.00</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">GST (<span id="taxPercentage">{{ $taxRate ?? 10 }}</span>%):</span>
                            <span class="font-semibold text-gray-900" id="tax">₹0.00</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Service (<span id="serviceChargePercentage">{{ $serviceChargeRate ?? 5 }}</span>%):</span>
                            <span class="font-semibold text-gray-900" id="serviceCharge">₹0.00</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-300">
                            <span class="text-gray-900">Total:</span>
                            <span class="text-indigo-600" id="total">₹0.00</span>
                        </div>
                    </div>

                    <div class="p-4 border-t border-gray-200 bg-gray-50">
                        <textarea name="notes" rows="2" placeholder="Special instructions (optional)" 
                                  class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-lg text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                        
                        <button type="submit" id="submitBtn" disabled
                                class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3 px-4 rounded-lg transition text-sm shadow-md">
                            Place Order
                        </button>
                        <button type="button" onclick="clearCart()" 
                                class="w-full bg-white hover:bg-gray-50 text-red-600 font-semibold py-2 px-4 rounded-lg mt-2 transition text-sm border border-red-200">
                            Clear Cart
                        </button>
                    </div>
                </form>
            </aside>
        </main>

            <!-- Ongoing Orders Section -->
            <div id="ongoingOrders" class="p-4 hidden bg-gray-50 overflow-y-auto" style="max-height: calc(100vh - 3rem);">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-bold text-gray-900">Ongoing Order</h3>
                    <button onclick="viewFullOrder()" id="viewOrderBtn" class="text-xs bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-medium px-3 py-1.5 rounded-lg transition">
                        View Details
                    </button>
                </div>
                
                <div class="mb-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-600">Order #</span>
                        <span class="text-sm font-bold text-blue-700" id="orderNumber"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total:</span>
                        <span class="font-bold text-xl text-blue-600" id="currentOrderTotal">₹0.00</span>
                    </div>
                </div>

                <!-- All Items in Order -->
                <div class="mb-3">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Items in Order</h4>
                    <div id="allItemsList" class="bg-white rounded-lg p-3 border border-gray-200 text-sm overflow-y-auto" style="max-height: 150px;">
                        <!-- Items will be listed here -->
                    </div>
                </div>

                <!-- KOTs Section -->
                <div class="mb-3">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Kitchen Orders (KOTs)</h4>
                    <div id="kotsList" class="space-y-2 overflow-y-auto" style="max-height: 150px;">
                        <!-- KOTs will be listed here -->
                    </div>
                </div>
                
                <div class="pt-3 border-t border-gray-300">
                    <button onclick="generateBill()" id="generateBillBtn" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition hidden text-sm shadow-md">
                        Generate Bill
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- POS Bill Modal -->
<div id="posBillModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-labelledby="posBillModalTitle" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closePosBillModal()"></div>
    <div class="relative z-10 flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-md rounded-lg bg-white shadow-xl">
            <div class="flex items-center justify-between border-b px-5 py-4">
                <div>
                    <h3 id="posBillModalTitle" class="text-lg font-semibold text-gray-900">Generate Bill</h3>
                    <p class="text-sm text-gray-500">Add an optional discount before generating the bill.</p>
                </div>
                <button type="button" onclick="closePosBillModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Close dialog">&times;</button>
            </div>
            <div class="space-y-4 px-5 py-4">
                <div>
                    <label for="posBillDiscount" class="block text-sm font-medium text-gray-700">Discount Percentage</label>
                    <input type="number" id="posBillDiscount" min="0" max="100" step="0.01" value="0"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="posBillReason" class="block text-sm font-medium text-gray-700">Discount Reason <span class="text-xs text-gray-400">(required when discount &gt; 0)</span></label>
                    <textarea id="posBillReason" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="Enter reason"></textarea>
                </div>
                <p id="posBillError" class="hidden rounded-md bg-red-50 px-3 py-2 text-sm text-red-600"></p>
            </div>
            <div class="flex items-center justify-end gap-3 border-t bg-gray-50 px-5 py-3">
                <button type="button" onclick="closePosBillModal()" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none">Cancel</button>
                <button type="button" id="posBillSubmitBtn" onclick="submitPosBillModal()" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Generate</button>
            </div>
        </div>
    </div>
</div>

@php
    $token = '';
    try {
        $token = auth()->user()?->createToken("pos-token")->plainTextToken ?? '';
    } catch (\Exception $e) {
        $token = '';
    }
@endphp
<script>
let authToken = @json($token);

// Cart and table state
let cart = [];
let selectedTable = null;
let currentOrder = null;

const posBillModal = document.getElementById('posBillModal');
const posBillDiscountInput = document.getElementById('posBillDiscount');
const posBillReasonInput = document.getElementById('posBillReason');
const posBillError = document.getElementById('posBillError');
const posBillSubmitBtn = document.getElementById('posBillSubmitBtn');

// Table selection
function selectTable(tableId, tableNumber, status) {
    // Store the selected table
    selectedTable = tableId;
    
    // Update the hidden input for form submission
    document.getElementById('tableIdInput').value = tableId;
    
    // Update the UI to show selected table
    document.getElementById('currentTableInfo').textContent = 'Table ' + tableNumber;
    document.getElementById('selectedTableInfo').textContent = 'Selected: Table ' + tableNumber;
    
    // Update button styles - remove active state from all table buttons
    document.querySelectorAll('.table-btn').forEach(btn => {
        // Reset to default state with explicit styles
        btn.classList.remove('ring-2', 'ring-indigo-500', 'ring-offset-2', 'ring-offset-white', 'shadow-lg', 'scale-105');
        btn.classList.add('shadow-sm', 'hover:scale-105');
    });
    
    // Add active state to selected table button
    const selectedBtn = document.getElementById('table-btn-' + tableId);
    if (selectedBtn) {
        selectedBtn.classList.add('ring-2', 'ring-indigo-500', 'ring-offset-2', 'ring-offset-white', 'shadow-lg', 'scale-105');
        selectedBtn.classList.remove('shadow-sm');
        
        // Add a subtle pulse animation
        selectedBtn.classList.add('animate-pulse');
        setTimeout(() => selectedBtn.classList.remove('animate-pulse'), 1000);
    }
    
    // Enable the submit button if there are items in the cart
    updateSubmitButton();
    
    // Load any existing orders for this table
    loadTableOrders(tableId);
    updateSubmitButton();
}

// Load table orders
async function loadTableOrders(tableId) {
    try {
        const response = await fetch(`/api/tables/${tableId}`, {
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json'
            }
        });
        
        const table = await response.json();
        
        if (table.orders && table.orders.length > 0) {
            currentOrder = table.orders[0];
            displayOngoingOrders(currentOrder);
        } else {
            currentOrder = null;
            document.getElementById('ongoingOrders').classList.add('hidden');
        }
    } catch (error) {
        console.error('Error loading table orders:', error);
    }
}

// Display ongoing orders
function displayOngoingOrders(order) {
    const ongoingDiv = document.getElementById('ongoingOrders');
    const kotsList = document.getElementById('kotsList');
    const allItemsList = document.getElementById('allItemsList');
    const orderNumber = document.getElementById('orderNumber');
    const currentTotal = document.getElementById('currentOrderTotal');
    const generateBillBtn = document.getElementById('generateBillBtn');
    
    ongoingDiv.classList.remove('hidden');
    
    // Order info
    orderNumber.textContent = order.order_number;
    currentTotal.textContent = `₹${parseFloat(order.total).toFixed(2)}`;
    
    // Update tax and service charge percentages
    const taxPercentageEl = document.getElementById('taxPercentage');
    const serviceChargePercentageEl = document.getElementById('serviceChargePercentage');
    
    if (taxPercentageEl && order.tax_rate !== undefined) {
        taxPercentageEl.textContent = order.tax_rate;
    }
    if (serviceChargePercentageEl && order.service_charge_rate !== undefined) {
        serviceChargePercentageEl.textContent = order.service_charge_rate;
    }
    
    // Show generate bill button
    if (currentOrder) {
        generateBillBtn.classList.remove('hidden');
    } else {
        generateBillBtn.classList.add('hidden');
    }
    
    // Display ALL items in the order
    let allItemsHtml = '';
    if (order.order_items && order.order_items.length > 0) {
        allItemsHtml = '<div class="space-y-1">';
        order.order_items.forEach(item => {
            allItemsHtml += `
                <div class="flex items-center justify-between py-1.5 border-b border-gray-200 last:border-0">
                    <div class="flex items-center gap-2">
                        <span class="flex items-center justify-center w-6 h-6 bg-indigo-100 text-indigo-700 rounded-full font-bold text-xs">
                            ${item.quantity}
                        </span>
                        <p class="text-xs font-medium text-gray-900">${item.menu_item.name}</p>
                    </div>
                    <span class="text-xs font-semibold text-gray-900">₹${parseFloat(item.total_price).toFixed(0)}</span>
                </div>
            `;
        });
        allItemsHtml += '</div>';
    } else {
        allItemsHtml = '<p class="text-gray-500 text-xs text-center py-4">No items</p>';
    }
    allItemsList.innerHTML = allItemsHtml;
    
    // Display KOTs
    let kotsHtml = '';
    if (order.kitchen_order_tickets && order.kitchen_order_tickets.length > 0) {
        // Sort KOTs with unprinted ones first
        const sortedKots = [...order.kitchen_order_tickets].sort((a, b) => {
            if (a.printed_at && !b.printed_at) return 1;
            if (!a.printed_at && b.printed_at) return -1;
            return new Date(b.created_at) - new Date(a.created_at);
        });

        sortedKots.forEach(kot => {
            const isPrinted = !!kot.printed_at;
            const isPending = kot.status === 'pending' && !isPrinted;
            
            kotsHtml += `
                <div class="border-2 ${isPrinted ? 'border-gray-200 bg-gray-50' : 'border-indigo-300 bg-indigo-50'} rounded-lg p-3 mb-2" 
                     data-kot-id="${kot.id}" 
                     data-status="${kot.status}">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm text-gray-900">${kot.kot_number}</span>
                            <span class="kot-status text-xs px-2 py-0.5 rounded-full font-semibold 
                                ${isPrinted ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                                ${kot.status.toUpperCase()}
                            </span>
                        </div>
                        <div class="flex items-center gap-1">
                            ${isPending ? `
                                <button onclick="event.stopPropagation(); printKOT(${kot.id})" 
                                        class="print-kot-btn text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-0.5 rounded font-medium transition">
                                    Print
                                </button>
                            ` : ''}
                        </div>
                    </div>
                    <ul class="text-xs space-y-1 mt-1.5">
                        ${kot.order_items.map(item => `
                            <li class="flex justify-between text-gray-700">
                                <span><span class="font-semibold">${item.quantity}x</span> ${item.menu_item.name}</span>
                                <span class="text-gray-600">₹${parseFloat(item.total_price).toFixed(0)}</span>
                            </li>
                        `).join('')}
                    </ul>
                    ${kot.notes ? `
                        <div class="mt-1.5 pt-1.5 border-t border-gray-100">
                            <p class="text-xs text-gray-500 italic">${kot.notes}</p>
                        </div>
                    ` : ''}
                </div>
            `;
        });
    } else {
        kotsHtml = `
            <div class="text-center py-4 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                <p class="text-xs text-gray-500">No KOTs yet</p>
            </div>
        `;
    }
    
    kotsList.innerHTML = kotsHtml;
}

// View full order
function viewFullOrder() {
    if (currentOrder) {
        window.location.href = `/orders/${currentOrder.id}`;
    }
}

// Filter menu items by category
function filterCategory(categorySlug) {
    const menuItems = document.querySelectorAll('.menu-item');
    const categoryButtons = document.querySelectorAll('.category-btn');
    let activeButton = null;

    // First, reset all buttons to default state
    categoryButtons.forEach(btn => {
        // Reset to default state with explicit styles
        btn.classList.remove('scale-[1.02]');
        btn.style.cssText = 'background-color: white; color: #374151; border-color: transparent; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);';
    });

    // Then, set the active button
    categoryButtons.forEach(btn => {
        if (btn.getAttribute('data-category') === categorySlug) {
            activeButton = btn;
            // Active state with solid background and explicit styles
            btn.classList.add('scale-[1.02]');
            btn.style.cssText = 'background-color: #4f46e5; color: white; border-color: transparent; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transform: scale(1.02);';
            btn.classList.add('animate-pulse');
            setTimeout(() => {
                btn.classList.remove('animate-pulse');
            }, 1000);
        }
    });

    // Filter menu items
    menuItems.forEach(item => {
        if (categorySlug === 'all' || item.dataset.category === categorySlug) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Add pulse animation to styles
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { transform: scale(0.98); }
        50% { transform: scale(0.96); }
        100% { transform: scale(0.98); }
    }
`;
document.head.appendChild(style);

// Initialize first category as active
document.addEventListener('DOMContentLoaded', function() {
    // Initialize first category as active
    const firstCategoryBtn = document.querySelector('.category-btn');
    if (firstCategoryBtn) {
        firstCategoryBtn.click();
    }

    // Check for table query parameter
    const urlParams = new URLSearchParams(window.location.search);
    const tableId = urlParams.get('table');
    
    if (tableId) {
        const tableBtn = document.getElementById(`table-btn-${tableId}`);
        if (tableBtn) {
            // Small delay to ensure everything is ready
            setTimeout(() => {
                tableBtn.click();
                // Scroll to table section if needed
                tableBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        }
    }
});

// Add to cart function
function addToCart(itemId, itemName, itemPrice, event = null) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    // Initialize cart if it doesn't exist
    if (!Array.isArray(cart)) {
        cart = [];
    }
    
    // Check if a table is selected
    if (!selectedTable) {
        alert('Please select a table first');
        const tableSection = document.querySelector('.bg-gray-50.rounded-lg');
        if (tableSection) {
            tableSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            tableSection.classList.add('ring-2', 'ring-red-500', 'ring-offset-2');
            setTimeout(() => tableSection.classList.remove('ring-2', 'ring-red-500', 'ring-offset-2'), 2000);
        }
        return false;
    }
    
    // Ensure itemId is a number and price is a float
    itemId = parseInt(itemId);
    const price = parseFloat(itemPrice) || 0;
    
    // Check if item already exists in cart
    const existingItemIndex = cart.findIndex(item => parseInt(item.id) === itemId);
    
    if (existingItemIndex >= 0) {
        // Item exists, increment quantity
        cart[existingItemIndex].quantity = (parseInt(cart[existingItemIndex].quantity) || 0) + 1;
    } else {
        // Add new item to cart
        cart.push({
            id: itemId,
            name: itemName || 'Unnamed Item',
            price: price,
            quantity: 1
        });
    }
    
    // Update the display
    updateCartDisplay();
    
    // Show visual feedback
    const itemElement = document.querySelector(`[onclick*="addToCart(${itemId},"]`);
    if (itemElement) {
        itemElement.classList.add('ring-2', 'ring-green-500');
        setTimeout(() => itemElement.classList.remove('ring-2', 'ring-green-500'), 500);
    }
    
    return false;
}

// Remove from cart function
function removeFromCart(index, event = null) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    // Ensure index is a number
    index = parseInt(index);
    
    if (isNaN(index) || !cart[index]) {
        console.error('Invalid cart item index for removal:', index);
        return false;
    }
    
    // Add a quick fade out effect
    const cartItem = document.querySelector(`#cartItems > div > div:nth-child(${index + 1})`);
    if (cartItem) {
        cartItem.classList.add('opacity-0', 'translate-x-4', 'transition-all', 'duration-300');
        setTimeout(() => {
            cart.splice(index, 1);
            updateCartDisplay();
        }, 300);
    } else {
        cart.splice(index, 1);
        updateCartDisplay();
    }
    
    return false;
}

// Update quantity function
function updateQuantity(index, change, event = null) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    // Ensure index is a number
    index = parseInt(index);
    
    if (isNaN(index) || !cart[index]) {
        console.error('Invalid cart item index:', index);
        return false;
    }
    
    // Update the quantity
    const newQuantity = cart[index].quantity + change;
    
    if (newQuantity < 1) {
        removeFromCart(index, event);
    } else {
        cart[index].quantity = newQuantity;
        updateCartDisplay();
    }
    
    return false;
}

// Update cart display
function updateCartDisplay() {
    const cartItems = document.getElementById('cartItems');
    
    if (!cart || cart.length === 0) {
        cart = []; // Ensure cart is always an array
        cartItems.innerHTML = '<p class="text-gray-400 text-center py-8 text-sm">No items added yet</p>';
        updateTotals();
        updateSubmitButton();
        return;
    }
    
    let html = '<div class="space-y-2">';
    
    cart.forEach((item, index) => {
        // Ensure quantity is a number
        const quantity = parseInt(item.quantity) || 1;
        const price = parseFloat(item.price) || 0;
        const total = (price * quantity).toFixed(2);
        
        html += `
            <div class="flex items-center justify-between p-2 bg-white rounded-lg border border-gray-200 hover:border-indigo-300 transition-colors">
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">${item.name || 'Unnamed Item'}</div>
                    <div class="flex items-center mt-1">
                        <button type="button" 
                                onclick="updateQuantity(${index}, -1, event)" 
                                class="w-6 h-6 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                            -
                        </button>
                        <span class="mx-2 w-8 text-center text-sm">${quantity}</span>
                        <button type="button" 
                                onclick="updateQuantity(${index}, 1, event)" 
                                class="w-6 h-6 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                            +
                        </button>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-semibold">₹${total}</div>
                    <button type="button" 
                            onclick="removeFromCart(${index}, event)" 
                            class="text-xs text-red-500 hover:text-red-700 mt-1 transition-colors">
                        Remove
                    </button>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    cartItems.innerHTML = html;
    
    updateTotals();
    updateSubmitButton();
}

// Update submit button state
function updateSubmitButton() {
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.disabled = !(cart && cart.length > 0 && selectedTable);
    }
}

// Update totals
function updateTotals() {
    // Ensure cart is an array
    if (!Array.isArray(cart)) {
        cart = [];
    }
    
    // Calculate subtotal
    const subtotal = cart.reduce((sum, item) => {
        const price = parseFloat(item.price) || 0;
        const quantity = parseInt(item.quantity) || 0;
        return sum + (price * quantity);
    }, 0);
    
    // Get tax and service charge rates from controller
    const taxRate = {{ ($taxRate ?? 10) / 100 }}; // Convert percentage to decimal
    const serviceChargeRate = {{ ($serviceChargeRate ?? 5) / 100 }}; // Convert percentage to decimal
    
    const tax = subtotal * taxRate;
    const serviceCharge = subtotal * serviceChargeRate;
    const total = subtotal + tax + serviceCharge;
    
    // Update the UI
    const formatCurrency = (amount) => {
        return '₹' + parseFloat(amount).toFixed(2);
    };
    
    const subtotalEl = document.getElementById('subtotal');
    const taxEl = document.getElementById('tax');
    const serviceChargeEl = document.getElementById('serviceCharge');
    const totalEl = document.getElementById('total');
    
    if (subtotalEl) subtotalEl.textContent = formatCurrency(subtotal);
    // Update the percentage labels in the UI
    const taxPercentageEl = document.getElementById('taxPercentage');
    const serviceChargePercentageEl = document.getElementById('serviceChargePercentage');
    
    if (taxEl && taxPercentageEl) {
        taxEl.textContent = formatCurrency(tax);
        taxPercentageEl.textContent = (taxRate * 100).toFixed(0);
    }
    if (serviceChargeEl && serviceChargePercentageEl) {
        serviceChargeEl.textContent = formatCurrency(serviceCharge);
        serviceChargePercentageEl.textContent = (serviceChargeRate * 100).toFixed(0);
    }
    if (totalEl) totalEl.textContent = formatCurrency(total);
    
    // Update the order form with the cart data
    const orderForm = document.getElementById('orderForm');
    if (orderForm) {
        // Clear existing items
        const existingItems = orderForm.querySelectorAll('input[name^="items"]');
        existingItems.forEach(el => el.remove());
        
        // Add current cart items to form
        cart.forEach((item, index) => {
            const itemPrefix = `items[${index}]`;
            
            // Create hidden inputs for form submission
            const createHiddenInput = (name, value) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                return input;
            };
            
            orderForm.appendChild(createHiddenInput(`${itemPrefix}[menu_item_id]`, item.id));
            orderForm.appendChild(createHiddenInput(`${itemPrefix}[quantity]`, item.quantity));
            orderForm.appendChild(createHiddenInput(`${itemPrefix}[unit_price]`, item.price));
        });
    }
}

// Clear cart
function clearCart() {
    if (confirm('Are you sure you want to clear the cart?')) {
        cart = [];
        updateCartDisplay();
    }
}

// Print KOT
async function printKOT(kotId) {
    try {
        const response = await fetch(`/api/kots/${kotId}/print`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const result = await response.json();
        
        if (response.ok) {
            // Update the KOT status in the UI
            const kotElement = document.querySelector(`[data-kot-id="${kotId}"]`);
            if (kotElement) {
                kotElement.setAttribute('data-status', 'ready');
                const statusElement = kotElement.querySelector('.kot-status');
                if (statusElement) {
                    statusElement.textContent = 'READY';
                    statusElement.classList.remove('bg-yellow-100', 'text-yellow-800');
                    statusElement.classList.add('bg-green-100', 'text-green-800');
                }
                
                // Remove the print button
                const printBtn = kotElement.querySelector('.print-kot-btn');
                if (printBtn) {
                    printBtn.remove();
                }
            }
            
            openKOTPrintWindow(result.kot);
            await loadTableOrders(selectedTable);
        } else {
            throw new Error(result.message || 'Failed to print KOT');
        }
    } catch (error) {
        console.error('Error printing KOT:', error);
        alert('Error: ' + (error.message || 'Failed to print KOT'));
    }
}

// Open KOT print window
function openKOTPrintWindow(kot) {
    const printWindow = window.open('', '_blank', 'width=300,height=600');
    
    let itemsHtml = '';
    kot.order_items.forEach(item => {
        itemsHtml += `
            <tr>
                <td>${item.menu_item.name}</td>
                <td style="font-weight: bold; font-size: 1.2em;">${item.quantity}</td>
                <td>${item.special_instructions || '-'}</td>
            </tr>
        `;
    });
    
    printWindow.document.write(`
        <html>
        <head>
            <title>KOT - ${kot.kot_number}</title>
            <style>
                @page { size: 80mm auto; margin: 0; }
                body { 
                    font-family: monospace; 
                    width: 70mm; 
                    margin: 0 auto; 
                    padding: 10px; 
                    padding-bottom: 2mm;
                    color: #000;
                    font-weight: bold;
                    font-size: 12px;
                }
                .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 10px; margin-bottom: 10px; }
                .info { margin-bottom: 10px; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; font-size: 12px; page-break-inside: auto; }
                tr { page-break-inside: avoid; page-break-after: auto; }
                th, td { padding: 5px 2px; text-align: left; border-bottom: 1px solid #000; color: #000; }
                @media print { 
                    button { display: none; }
                    * { color: #000 !important; font-weight: bold !important; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>${kot.kot_number}</h2>
            </div>
            <div class="info">
                <div><strong>Table:</strong> ${kot.order.restaurant_table.table_number}</div>
                <div><strong>Time:</strong> ${new Date(kot.created_at).toLocaleString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</div>
                <div><strong>Order:</strong> ${kot.order.order_number}</div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Instructions</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsHtml}
                </tbody>
            </table>
            ${kot.notes ? `<div style="margin-top: 10px;"><strong>Notes:</strong> ${kot.notes}</div>` : ''}
            <div style="margin-top: 20px; text-align: center;">
                <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Print</button>
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
}

function generateBill() {
    if (!currentOrder) {
        alert('No active order');
        return;
    }

    openPosBillModal();
}

function openPosBillModal() {
    posBillError.classList.add('hidden');
    posBillError.textContent = '';
    posBillDiscountInput.value = '0';
    posBillReasonInput.value = '';
    posBillModal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    setTimeout(() => posBillDiscountInput.focus(), 0);
}

function closePosBillModal() {
    posBillModal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function showPosBillError(message) {
    posBillError.textContent = message;
    posBillError.classList.remove('hidden');
}

function clearPosBillError() {
    posBillError.textContent = '';
    posBillError.classList.add('hidden');
}

async function submitPosBillModal() {
    if (!currentOrder) {
        alert('No active order');
        return;
    }

    clearPosBillError();
    const discountValue = parseFloat(posBillDiscountInput.value);

    if (isNaN(discountValue) || discountValue < 0 || discountValue > 100) {
        showPosBillError('Please enter a valid discount percentage between 0 and 100.');
        return;
    }

    const reason = posBillReasonInput.value.trim();
    if (discountValue > 0 && !reason) {
        showPosBillError('Discount reason is required when a discount is applied.');
        return;
    }

    posBillSubmitBtn.disabled = true;
    posBillSubmitBtn.textContent = 'Generating...';

    try {
        const response = await fetch(`/api/orders/${currentOrder.id}/bill`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                discount_percentage: discountValue,
                discount_reason: reason
            })
        });

        const result = await response.json();

        if (response.ok) {
            closePosBillModal();
            await loadTableOrders(selectedTable);
            window.location.href = `/bills/${result.bill.id}`;
        } else {
            showPosBillError(result.message || 'Error generating bill.');
        }
    } catch (error) {
        console.error('Error generating bill:', error);
        showPosBillError(error.message || 'Error generating bill.');
    } finally {
        posBillSubmitBtn.disabled = false;
        posBillSubmitBtn.textContent = 'Generate';
    }
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && !posBillModal.classList.contains('hidden')) {
        closePosBillModal();
    }
});

// Update table status
function updateTableStatus(tableId, status) {
    const tableBtn = document.getElementById(`table-btn-${tableId}`);
    if (!tableBtn) return;
    
    // Remove all status classes
    tableBtn.classList.remove('border-gray-300', 'bg-white', 'text-gray-700', 'hover:bg-gray-50');
    tableBtn.classList.remove('border-orange-400', 'bg-orange-50', 'text-orange-700', 'hover:bg-orange-100');
    
    // Add appropriate status class
    if (status === 'occupied') {
        tableBtn.classList.add('border-orange-400', 'bg-orange-50', 'text-orange-700', 'hover:bg-orange-100');
    } else {
        tableBtn.classList.add('border-gray-300', 'bg-white', 'text-gray-700', 'hover:bg-gray-50');
    }
    
    // Update data-status attribute
    tableBtn.setAttribute('data-status', status);
}

// Form submission
document.getElementById('orderForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (cart.length === 0 || !selectedTable) {
        alert('Please select a table and add items');
        return;
    }
    
    const formData = {
        restaurant_table_id: parseInt(selectedTable),
        items: cart.map(item => ({
            menu_item_id: item.id,
            quantity: item.quantity,
            unit_price: item.price
        })),
        notes: document.querySelector('textarea[name="notes"]').value
    };
    
    try {
        const response = await fetch('/api/orders', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (response.ok) {
            // Show a brief non-blocking notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
            notification.textContent = 'Order placed! Printing KOT...';
            document.body.appendChild(notification);
            
            // Auto-print KOT without confirmation
            await printKOT(result.kot.id);
            
            // Remove notification after 2 seconds
            setTimeout(() => {
                notification.remove();
            }, 2000);
            
            // Update table status to occupied
            updateTableStatus(selectedTable, 'occupied');
            
            // Clear cart and reset form
            cart = [];
            updateCartDisplay(); // This is the correct function name
            document.querySelector('textarea[name="notes"]').value = '';
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('orderForm').reset();
            await loadTableOrders(selectedTable);
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error submitting order:', error);
        alert('Error submitting order: ' + (error.message || 'Unknown error occurred'));
    }
});

// Search functionality
document.getElementById('posSearchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach(item => {
        const name = item.querySelector('h4')?.textContent.toLowerCase() || '';
        
        if (name.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>
@endsection
