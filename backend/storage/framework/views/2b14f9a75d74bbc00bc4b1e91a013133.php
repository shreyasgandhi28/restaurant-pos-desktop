<?php $__env->startSection('title', 'Order #' . $order->order_number); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <div class="flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Order #<?php echo e($order->order_number); ?></h1>
                        <div class="flex flex-wrap items-center text-sm text-gray-600 gap-2 mt-1">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span>Table <?php echo e($order->restaurantTable->table_number); ?></span>
                            </div>
                            <span>•</span>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span><?php echo e($order->user->name); ?></span>
                            </div>
                            <span>•</span>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span><?php echo e($order->created_at->format('M d, Y h:i A')); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <?php if($order->bill): ?>
                            <div class="flex items-center gap-3">
                                <a href="<?php echo e(route('bills.show', $order->bill)); ?>" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-file-invoice mr-2"></i> View Bill
                                </a>
                                <?php if($order->bill->status === 'pending'): ?>
                                    <a href="<?php echo e(route('bills.edit', $order->bill)); ?>" 
                                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fas fa-check-circle mr-2"></i> Mark Paid
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="flex items-center gap-3">
                                <div class="flex space-x-3">
                                    <a href="<?php echo e(route('pos.index')); ?>" 
                                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Back to POS
                                    </a>
                                    <form id="generateBillForm" action="<?php echo e(route('bills.store')); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="order_id" value="<?php echo e($order->id); ?>">
                                        <input type="hidden" name="discount_percentage" id="discount_percentage" value="0">
                                        <input type="hidden" name="discount_reason" id="discount_reason" value="">
                                        <button type="button" 
                                                onclick="openBillModal()"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                Generate Bill
                                            </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php if($order->notes): ?>
                <div class="mt-2 pt-2 border-t">
                    <p class="text-gray-600 text-2xs font-medium">Notes: <span class="text-gray-800"><?php echo e($order->notes); ?></span></p>
                </div>
            <?php endif; ?>
        </div>

            <!-- Order Items -->
            <div class="bg-white">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-medium text-gray-900">Order Items</h3>
                </div>
                <?php $__currentLoopData = $order->orderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="px-6 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex items-start">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-800 text-xs font-semibold mt-1">
                                <?php echo e($item->quantity); ?>x
                            </span>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-900"><?php echo e($item->menuItem->name); ?></h4>
                                    <div class="text-right ml-4">
                                        <p class="text-sm font-medium text-gray-900">₹<?php echo e(number_format($item->total_price, 2)); ?></p>
                                        <p class="text-xs text-gray-500">₹<?php echo e(number_format($item->unit_price, 2)); ?>/each</p>
                                    </div>
                                </div>
                                
                                <?php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'preparing' => 'bg-blue-100 text-blue-800',
                                        'ready' => 'bg-green-100 text-green-800',
                                        'served' => 'bg-indigo-100 text-indigo-800',
                                        'cancelled' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusIcons = [
                                        'pending' => 'clock',
                                        'preparing' => 'utensils',
                                        'ready' => 'check-circle',
                                        'served' => 'check-double',
                                        'cancelled' => 'times-circle'
                                    ];
                                ?>
                                
                                <div class="mt-1 flex items-center">
                                    <div class="relative inline-block text-left" x-data="{ open: false }" @click.away="open = false">
                                        <button type="button" 
                                            @click="open = !open" 
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($statusColors[$item->status] ?? 'bg-gray-100 text-gray-800'); ?> hover:opacity-90 focus:outline-none">
                                            <i class="fas fa-<?php echo e($statusIcons[$item->status] ?? 'ellipsis-h'); ?> mr-1"></i>
                                            <?php echo e(ucfirst($item->status)); ?>

                                            <svg class="ml-1 -mr-0.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <div x-show="open" 
                                            x-transition:enter="transition ease-out duration-100" 
                                            x-transition:enter-start="transform opacity-0 scale-95" 
                                            x-transition:enter-end="transform opacity-100 scale-100" 
                                            x-transition:leave="transition ease-in duration-75" 
                                            x-transition:leave-start="transform opacity-100 scale-100" 
                                            x-transition:leave-end="transform opacity-0 scale-95" 
                                            class="origin-top-left absolute left-0 mt-1 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10" 
                                            style="display: none;">
                                            <div class="py-1">
                                                <button type="button" onclick="updateOrderItemStatus(this, <?php echo e($item->id); ?>, 'pending')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center <?php echo e($item->status === 'pending' ? 'bg-gray-100' : ''); ?>">
                                                    <i class="fas fa-clock w-4 text-center mr-3 text-yellow-500"></i>
                                                    <span>Pending</span>
                                                </button>
                                                <button type="button" onclick="updateOrderItemStatus(this, <?php echo e($item->id); ?>, 'preparing')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center <?php echo e($item->status === 'preparing' ? 'bg-gray-100' : ''); ?>">
                                                    <i class="fas fa-utensils w-4 text-center mr-3 text-blue-500"></i>
                                                    <span>Preparing</span>
                                                </button>
                                                <button type="button" onclick="updateOrderItemStatus(this, <?php echo e($item->id); ?>, 'ready')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center <?php echo e($item->status === 'ready' ? 'bg-gray-100' : ''); ?>">
                                                    <i class="fas fa-check-circle w-4 text-center mr-3 text-green-500"></i>
                                                    <span>Ready</span>
                                                </button>
                                                <button type="button" onclick="updateOrderItemStatus(this, <?php echo e($item->id); ?>, 'served')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center <?php echo e($item->status === 'served' ? 'bg-gray-100' : ''); ?>">
                                                    <i class="fas fa-check-double w-4 text-center mr-3 text-indigo-500"></i>
                                                    <span>Served</span>
                                                </button>
                                                <div class="border-t border-gray-100"></div>
                                                <div class="border-t border-gray-100"></div>
                                                <button type="button" onclick="updateOrderItemStatus(this, <?php echo e($item->id); ?>, 'cancelled')" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                                    <i class="fas fa-times-circle w-4 text-center mr-3 text-red-500"></i>
                                                    <span>Cancel Order</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if($item->special_instructions): ?>
                                    <div class="mt-1">
                                        <p class="text-xs text-indigo-600 bg-indigo-50 rounded px-2 py-1 inline-flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <?php echo e($item->special_instructions); ?>

                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Financial Summary -->
            <div class="bg-white border-t border-gray-200 p-6">
                <h3 class="text-base font-medium text-gray-900 mb-4">Order Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal (<?php echo e($order->orderItems->sum('quantity')); ?> items)</span>
                        <span class="font-medium text-gray-900">₹<?php echo e(number_format($order->subtotal, 2)); ?></span>
                    </div>
                    
                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tax (<?php echo e(number_format($order->tax_rate, 2)); ?>%)</span>
                            <span class="text-gray-700">₹<?php echo e(number_format($order->tax, 2)); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Service Charge (<?php echo e(number_format($order->service_charge_rate, 2)); ?>%)</span>
                            <span class="text-gray-700">₹<?php echo e(number_format($order->service_charge, 2)); ?></span>
                        </div>
                    </div>
                    
                    <?php if($order->discount > 0 || ($order->bill && $order->bill->discount_amount > 0)): ?>
                        <div class="pt-2 mt-2 border-t border-gray-100 space-y-1.5">
                            <?php if($order->discount > 0): ?>
                                <div class="flex justify-between text-green-600">
                                    <span>Discount</span>
                                    <span class="font-medium">-₹<?php echo e(number_format($order->discount, 2)); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if($order->bill && $order->bill->discount_amount > 0): ?>
                                <div class="flex justify-between text-green-600">
                                    <span>Bill Discount (<?php echo e($order->bill->discount_percentage); ?>%)</span>
                                    <span class="font-medium">-₹<?php echo e(number_format($order->bill->discount_amount, 2)); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="pt-3 mt-3 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-900">Total Amount</span>
                            <span class="text-lg font-bold text-indigo-600">₹<?php echo e(number_format($order->total, 2)); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if($order->bill && ($order->bill->payment_method || $order->bill->paid_at)): ?>
                <div class="border-t border-gray-100 bg-white p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Payment Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <?php if($order->bill->payment_method): ?>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Payment Method</p>
                                <div class="flex items-center">
                                    <?php
                                        $paymentIcons = [
                                            'cash' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                                            'card' => 'M3 10h18M7 15h1m4 0h1m-1-4h1m4 0h1m-9 4h.01M6 6h12a2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2z'
                                        ];
                                        $icon = $paymentIcons[strtolower($order->bill->payment_method)] ?? 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
                                    ?>
                                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($icon); ?>"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900"><?php echo e(ucfirst($order->bill->payment_method)); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if($order->bill->paid_at): ?>
                            <div class="text-right">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Paid On</p>
                                <div class="flex items-center justify-end">
                                    <svg class="w-5 h-5 text-green-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-green-600"><?php echo e($order->bill->paid_at->format('M d, Y H:i')); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
    </div>
</div>

<!-- Bill Discount Modal -->
<div id="billDiscountModal" class="fixed inset-0 z-50 hidden" aria-labelledby="billModalTitle" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeBillModal()"></div>
    <div class="relative z-10 flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-md rounded-lg bg-white shadow-xl">
            <div class="flex items-center justify-between border-b px-5 py-4">
                <div>
                    <h3 id="billModalTitle" class="text-lg font-semibold text-gray-900">Generate Bill</h3>
                    <p class="text-sm text-gray-500">Add an optional discount before generating the bill.</p>
                </div>
                <button type="button" onclick="closeBillModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>
            <div class="space-y-4 px-5 py-4">
                <div>
                    <label for="billDiscountPercentage" class="block text-sm font-medium text-gray-700">Discount Percentage</label>
                    <input type="number" id="billDiscountPercentage" min="0" max="100" step="0.01" value="0"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="billDiscountReason" class="block text-sm font-medium text-gray-700">Discount Reason <span class="text-xs text-gray-400">(required when discount &gt; 0)</span></label>
                    <textarea id="billDiscountReason" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="Enter reason for the discount"></textarea>
                </div>
                <p id="billModalError" class="hidden rounded-md bg-red-50 px-3 py-2 text-sm text-red-600"></p>
            </div>
            <div class="flex items-center justify-end gap-3 border-t bg-gray-50 px-5 py-3">
                <button type="button" onclick="closeBillModal()" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none">Cancel</button>
                <button type="button" onclick="submitBillModal()" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Generate</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function getBillModalElements() {
        return {
            modal: document.getElementById('billDiscountModal'),
            discountInput: document.getElementById('billDiscountPercentage'),
            reasonInput: document.getElementById('billDiscountReason'),
            errorBox: document.getElementById('billModalError'),
            discountField: document.getElementById('discount_percentage'),
            reasonField: document.getElementById('discount_reason'),
            form: document.getElementById('generateBillForm')
        };
    }

    window.openBillModal = function() {
        const { modal, discountInput, reasonInput, errorBox, discountField, reasonField } = getBillModalElements();
        if (!modal || !discountInput || !reasonInput) return;

        errorBox?.classList.add('hidden');
        if (errorBox) errorBox.textContent = '';

        discountInput.value = discountField?.value || 0;
        reasonInput.value = reasonField?.value || '';

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        setTimeout(() => discountInput.focus(), 0);
    };

    window.closeBillModal = function() {
        const { modal } = getBillModalElements();
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    function showBillError(message) {
        const { errorBox } = getBillModalElements();
        if (!errorBox) return;
        errorBox.textContent = message;
        errorBox.classList.remove('hidden');
    }

    function clearBillError() {
        const { errorBox } = getBillModalElements();
        if (!errorBox) return;
        errorBox.textContent = '';
        errorBox.classList.add('hidden');
    }

    window.submitBillModal = function() {
        clearBillError();
        const { discountInput, reasonInput, discountField, reasonField, form } = getBillModalElements();
        if (!discountInput || !reasonInput || !form) return;

        const discountValue = parseFloat(discountInput.value);

        if (isNaN(discountValue) || discountValue < 0 || discountValue > 100) {
            showBillError('Please enter a valid discount percentage between 0 and 100.');
            return;
        }

        const reason = reasonInput.value.trim();
        if (discountValue > 0 && !reason) {
            showBillError('Discount reason is required when a discount is applied.');
            return;
        }

        if (discountField) discountField.value = discountValue;
        if (reasonField) reasonField.value = reason;

        closeBillModal();
        form.submit();
    };

    document.addEventListener('keydown', function(event) {
        const { modal } = getBillModalElements();
        if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            window.closeBillModal();
        }
    });

    // Update order item status
    function updateOrderItemStatus(button, orderItemId, status) {
    // If called from a select element
    if (button.tagName === 'SELECT') {
        status = button.value;
    }
    
    const baseUrl = '<?php echo e(url("/")); ?>';
    const url = `${baseUrl}/order-items/${orderItemId}/update-status`;
    
    // Show loading state
    const originalContent = button.innerHTML;
    if (button.tagName === 'BUTTON') {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...';
    }
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the status button
            const button = document.querySelector(`[onclick*="updateOrderItemStatus(this, ${orderItemId}"]`);
            if (button) {
                const statusColors = {
                    'pending': 'bg-yellow-100 text-yellow-800',
                    'preparing': 'bg-blue-100 text-blue-800',
                    'ready': 'bg-green-100 text-green-800',
                    'served': 'bg-indigo-100 text-indigo-800',
                    'cancelled': 'bg-red-100 text-red-800'
                };
                const statusIcons = {
                    'pending': 'clock',
                    'preparing': 'utensils',
                    'ready': 'check-circle',
                    'served': 'check-double',
                    'cancelled': 'times-circle'
                };
                
                // Update the main status button
                button.className = `inline-flex items-center px-3 py-1 border border-gray-300 rounded-md shadow-sm text-xs font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ${statusColors[status] || 'bg-gray-100 text-gray-800'}`;
                button.innerHTML = `<i class="fas fa-${statusIcons[status] || 'ellipsis-h'} mr-1.5 text-xs"></i> ${status.charAt(0).toUpperCase() + status.slice(1)} <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>`;
                
                // Close the dropdown
                const dropdown = button.closest('.relative').querySelector('[x-show="open"]');
                if (dropdown) {
                    button._x_dataStack[0].open = false;
                }
            }
            
            // Show success message
            showToast('Status updated successfully', 'success');
        } else {
            throw new Error(data.message || 'Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert the button state
        if (button.tagName === 'BUTTON') {
            button.innerHTML = originalContent;
            button.disabled = false;
        } else if (button.tagName === 'SELECT') {
            button.value = button._x_initialValue;
        }
        
        // Show error message
        showToast(error.message || 'Failed to update status. Please try again.', 'error');
    });
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-4 py-3 rounded-md shadow-lg text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } flex items-center`;
    
    const icon = document.createElement('i');
    icon.className = `fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2`;
    
    const text = document.createElement('span');
    text.textContent = message;
    
    toast.appendChild(icon);
    toast.appendChild(text);
    
    document.body.appendChild(toast);
    
    // Position the toast
    const toasts = document.querySelectorAll('[class*="fixed bottom-4 right-4"]');
    const toastIndex = Array.from(toasts).indexOf(toast);
    toast.style.bottom = `${4 + (toastIndex * 60)}rem`;
    
    // Remove toast after delay
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\restaurant-pos-desktop\backend\resources\views/orders/show.blade.php ENDPATH**/ ?>