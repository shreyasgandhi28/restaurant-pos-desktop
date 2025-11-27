@extends('layouts.app')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                <p class="text-gray-600">Table {{ $order->restaurantTable->table_number }} • Server: {{ $order->user->name }}</p>
            </div>
            <div class="flex space-x-2">
                <button onclick="generateBill()" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded text-sm transition">
                    Generate Bill
                </button>
                <button onclick="cancelOrder()" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded text-sm transition">
                    Cancel Order
                </button>
                <a href="{{ route('pos.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded text-sm transition">
                    Back to POS
                </a>
            </div>
        </div>
    </div>

    <!-- Order Information -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Order Information</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-6">
            <div class="text-center">
                <p class="text-gray-500 mb-1">Order #</p>
                <p class="font-bold text-lg text-indigo-600">{{ $order->order_number }}</p>
            </div>
            <div class="text-center">
                <p class="text-gray-500 mb-1">Table</p>
                <p class="font-bold">{{ $order->restaurantTable->table_number }}</p>
            </div>
            <div class="text-center">
                <p class="text-gray-500 mb-1">Server</p>
                <p class="font-bold">{{ $order->user->name }}</p>
            </div>
            <div class="text-center">
                <p class="text-gray-500 mb-1">Status</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>

        @if($order->notes)
            <div class="pt-4 border-t">
                <p class="text-gray-600 mb-2 font-medium">Special Instructions:</p>
                <p class="text-gray-800 bg-gray-50 p-3 rounded-lg">{{ $order->notes }}</p>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Order Items -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Order Items</h3>
            <div class="space-y-3">
                @foreach($order->orderItems as $item)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div class="flex items-center space-x-4 flex-1">
                            <div class="flex items-center justify-center w-12 h-12 bg-indigo-100 text-indigo-700 rounded-full font-bold text-lg">
                                {{ $item->quantity }}
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $item->menuItem->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $item->menuItem->category->name }}</p>
                                @if($item->special_instructions)
                                    <p class="text-sm text-indigo-600 mt-1">Note: {{ $item->special_instructions }}</p>
                                @endif
                                <div class="mt-2">
                                    <select onchange="updateItemStatus({{ $item->id }}, this.value)"
                                            class="text-sm border border-gray-300 rounded px-2 py-1 bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500 w-40">
                                        <option value="pending" {{ $item->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="preparing" {{ $item->status == 'preparing' ? 'selected' : '' }}>Preparing</option>
                                        <option value="ready" {{ $item->status == 'ready' ? 'selected' : '' }}>Ready</option>
                                        <option value="served" {{ $item->status == 'served' ? 'selected' : '' }}>Served</option>
                                        <option value="cancelled" {{ $item->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="text-right ml-4">
                            <p class="font-semibold text-gray-900">
                                @if($item->status === 'cancelled')
                                    <span class="text-red-600">₹0.00</span>
                                    <span class="text-xs text-red-600 block">(Cancelled)</span>
                                @else
                                    ₹{{ number_format($item->total_price, 2) }}
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Financial Summary & Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Payment Summary</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-semibold" data-order-total="subtotal">₹{{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Tax ({{ $order->bill ? $order->bill->tax_percentage : 10 }}%):</span>
                    <span class="font-semibold" data-order-total="tax">₹{{ number_format($order->tax, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Service Charge:</span>
                    <span class="font-semibold" data-order-total="service_charge">₹{{ number_format($order->service_charge, 2) }}</span>
                </div>
                @if($order->discount > 0)
                    <div class="flex justify-between py-2 border-b border-gray-100 text-green-600">
                        <span>Discount:</span>
                        <span class="font-semibold">-₹{{ number_format($order->discount, 2) }}</span>
                    </div>
                @endif
                @if($order->bill && $order->bill->discount_amount > 0)
                    <div class="flex justify-between py-2 border-b border-gray-100 text-green-600">
                        <span>Bill Discount ({{ $order->bill->discount_percentage }}%):</span>
                        <span class="font-semibold">-₹{{ number_format($order->bill->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between py-3 border-t-2 border-gray-300 text-lg font-bold">
                    <span>TOTAL:</span>
                    <span class="text-indigo-600" data-order-total="total">₹{{ number_format($order->total, 2) }}</span>
                </div>
            </div>

            @if($order->bill)
                <div class="mt-6 pt-4 border-t">
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-sm text-gray-600 mb-1">Bill Number</p>
                        <p class="font-bold text-xl text-indigo-600">{{ $order->bill->bill_number }}</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-2
                            @if($order->bill->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->bill->status == 'paid') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($order->bill->status) }}
                        </span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- KOTs Section (if any exist) -->
    @if($order->kitchenOrderTickets && $order->kitchenOrderTickets->count() > 0)
        <div class="mt-6 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Kitchen Order Tickets (KOTs)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($order->kitchenOrderTickets as $kot)
                    <div class="border-2 {{ $kot->printed_at ? 'border-gray-300' : 'border-indigo-300' }} rounded-lg p-4 {{ $kot->printed_at ? 'bg-gray-50' : 'bg-indigo-50' }}">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="font-bold text-gray-900">{{ $kot->kot_number }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm px-2 py-1 rounded-full font-semibold {{ $kot->printed_at ? 'bg-gray-100 text-gray-800' : 'bg-indigo-100 text-indigo-800' }}">{{ $kot->status }}</span>
                                @if(!$kot->printed_at)
                                    <button onclick="printKOT({{ $kot->id }})" class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded font-medium transition">
                                        Print
                                    </button>
                                @endif
                            </div>
                        </div>
                        <ul class="text-sm space-y-1">
                            @foreach($kot->orderItems as $item)
                                <li class="flex justify-between text-gray-700">
                                    <span>{{ $item->quantity }}x {{ $item->menuItem->name }}</span>
                                    <span class="text-gray-600">₹{{ number_format($item->total_price, 2) }}</span>
                                </li>
                            @endforeach
                        </ul>
                        @if($kot->printed_at)
                            <p class="text-xs text-gray-500 mt-2">Printed: {{ $kot->printed_at->format('M d, Y H:i') }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Bill Modal -->
<div id="orderBillModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-labelledby="orderBillModalTitle" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeOrderBillModal()"></div>
    <div class="relative z-10 flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-md rounded-lg bg-white shadow-xl">
            <div class="flex items-center justify-between border-b px-5 py-4">
                <div>
                    <h3 id="orderBillModalTitle" class="text-lg font-semibold text-gray-900">Generate Bill</h3>
                    <p class="text-sm text-gray-500">Add an optional discount before generating the bill.</p>
                </div>
                <button type="button" onclick="closeOrderBillModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Close dialog">&times;</button>
            </div>
            <div class="space-y-4 px-5 py-4">
                <div>
                    <label for="orderBillDiscount" class="block text-sm font-medium text-gray-700">Discount Percentage</label>
                    <input type="number" id="orderBillDiscount" min="0" max="100" step="0.01" value="0"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="orderBillReason" class="block text-sm font-medium text-gray-700">Discount Reason <span class="text-xs text-gray-400">(required when discount &gt; 0)</span></label>
                    <textarea id="orderBillReason" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="Enter reason"></textarea>
                </div>
                <p id="orderBillError" class="hidden rounded-md bg-red-50 px-3 py-2 text-sm text-red-600"></p>
            </div>
            <div class="flex items-center justify-end gap-3 border-t bg-gray-50 px-5 py-3">
                <button type="button" onclick="closeOrderBillModal()" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none">Cancel</button>
                <button type="button" id="orderBillSubmitBtn" onclick="submitOrderBillModal()" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Generate</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentOrder = @json($order);
let authToken = '{{ auth()->user()->createToken("pos-token")->plainTextToken }}';

const orderBillModal = document.getElementById('orderBillModal');
const orderBillDiscountInput = document.getElementById('orderBillDiscount');
const orderBillReasonInput = document.getElementById('orderBillReason');
const orderBillError = document.getElementById('orderBillError');
const orderBillSubmitBtn = document.getElementById('orderBillSubmitBtn');

// Update individual item status
async function updateItemStatus(itemId, newStatus) {
    // For cancelled status, require confirmation
    if (newStatus === 'cancelled') {
        const itemElement = document.querySelector(`select[onchange*="updateItemStatus(${itemId}"]`);
        const itemName = itemElement?.closest('.flex-1')?.querySelector('h4')?.textContent || 'this item';

        if (!confirm(`Are you sure you want to cancel "${itemName}"? This will remove it from the bill total.`)) {
            // Reset dropdown to previous value
            location.reload();
            return;
        }
    }

    try {
        const response = await fetch(`/api/orders/${currentOrder.id}/items/${itemId}/status`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                status: newStatus
            })
        });

        const result = await response.json();

        if (response.ok) {
            // Show subtle success feedback
            showStatusChangeFeedback(newStatus, 'success');

            // Update the order totals display if available
            updateOrderTotalsDisplay(result.order);
        } else {
            alert('Error updating item status: ' + result.message);
            location.reload();
        }
    } catch (error) {
        console.error('Error updating item status:', error);
        alert('Error updating item status');
        location.reload();
    }
}

// Show visual feedback for status changes
function showStatusChangeFeedback(status, type) {
    // Create a temporary notification
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white text-sm font-medium z-50 ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    notification.textContent = `Item marked as ${status}`;

    document.body.appendChild(notification);

    // Remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}

// Update order totals display
function updateOrderTotalsDisplay(order) {
    // Update any displayed totals if they exist in the current view
    const subtotalElement = document.querySelector('[data-order-total="subtotal"]');
    const taxElement = document.querySelector('[data-order-total="tax"]');
    const serviceChargeElement = document.querySelector('[data-order-total="service_charge"]');
    const totalElement = document.querySelector('[data-order-total="total"]');

    if (subtotalElement) subtotalElement.textContent = `₹${parseFloat(order.subtotal).toFixed(2)}`;
    if (taxElement) taxElement.textContent = `₹${parseFloat(order.tax).toFixed(2)}`;
    if (serviceChargeElement) serviceChargeElement.textContent = `₹${parseFloat(order.service_charge).toFixed(2)}`;
    if (totalElement) totalElement.textContent = `₹${parseFloat(order.total).toFixed(2)}`;
}

// Cancel order
async function cancelOrder() {
    if (!confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(`/api/orders/${currentOrder.id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const result = await response.json();

        if (response.ok) {
            alert('Order cancelled successfully!');
            window.location.href = '/pos';
        } else {
            alert('Error cancelling order: ' + result.message);
        }
    } catch (error) {
        console.error('Error cancelling order:', error);
        alert('Error cancelling order');
    }
}

function generateBill() {
    orderBillError.classList.add('hidden');
    orderBillError.textContent = '';
    orderBillDiscountInput.value = '0';
    orderBillReasonInput.value = '';
    orderBillModal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    setTimeout(() => orderBillDiscountInput.focus(), 0);
}

function closeOrderBillModal() {
    orderBillModal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function showOrderBillError(message) {
    orderBillError.textContent = message;
    orderBillError.classList.remove('hidden');
}

function clearOrderBillError() {
    orderBillError.textContent = '';
    orderBillError.classList.add('hidden');
}

async function submitOrderBillModal() {
    clearOrderBillError();
    const discountValue = parseFloat(orderBillDiscountInput.value);

    if (isNaN(discountValue) || discountValue < 0 || discountValue > 100) {
        showOrderBillError('Please enter a valid discount percentage between 0 and 100.');
        return;
    }

    const reason = orderBillReasonInput.value.trim();
    if (discountValue > 0 && !reason) {
        showOrderBillError('Discount reason is required when a discount is applied.');
        return;
    }

    orderBillSubmitBtn.disabled = true;
    orderBillSubmitBtn.textContent = 'Generating...';

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
            closeOrderBillModal();
            alert('Bill generated successfully!');
            window.location.href = `/bills/${result.bill.id}`;
        } else {
            showOrderBillError(result.message || 'Error generating bill.');
        }
    } catch (error) {
        console.error('Error generating bill:', error);
        showOrderBillError(error.message || 'Error generating bill.');
    } finally {
        orderBillSubmitBtn.disabled = false;
        orderBillSubmitBtn.textContent = 'Generate';
    }
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && !orderBillModal.classList.contains('hidden')) {
        closeOrderBillModal();
    }
});

// Print KOT
async function printKOT(kotId) {
    try {
        const response = await fetch(`/api/kots/${kotId}/print`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const result = await response.json();

        if (response.ok) {
            openKOTPrintWindow(result.kot);
            location.reload();
            alert('KOT marked as printed!');
        } else {
            alert('Error printing KOT: ' + result.message);
        }
    } catch (error) {
        console.error('Error printing KOT:', error);
        alert('Error printing KOT');
    }
}

// Open KOT print window
function openKOTPrintWindow(kot) {
    const printWindow = window.open('', '_blank', 'width=300,height=600');

    let itemsHtml = '';
    kot.order_items.forEach(item => {
        itemsHtml += `
            <tr>
                <td style="font-weight: bold; font-size: 1.2em;">${item.quantity}</td>
                <td>${item.menu_item.name}</td>
                <td>${item.special_instructions || '-'}</td>
            </tr>
        `;
    });

    printWindow.document.write(`
        <html>
        <head>
            <title>KOT - ${kot.kot_number}</title>
            <style>
                body { font-family: monospace; padding: 10px; color: #000; font-weight: bold; font-size: 12px; width: 70mm; margin: 0 auto; }
                .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 10px; margin-bottom: 10px; }
                .info { margin-bottom: 10px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { padding: 5px; text-align: left; border-bottom: 1px solid #000; color: #000; }
                .qty { font-weight: bold; }
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
                <div><strong>Time:</strong> ${new Date(kot.created_at).toLocaleString()}</div>
                <div><strong>Order:</strong> ${kot.order.order_number}</div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Qty</th>
                        <th>Item</th>
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
</script>
@endsection