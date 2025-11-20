<?php $__env->startSection('title', 'Bill Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Bill #<?php echo e($bill->bill_number); ?></h1>
            <p class="text-gray-600">Order #<?php echo e($bill->order->order_number); ?></p>
        </div>
        <div class="flex space-x-2">
            <a href="<?php echo e(route('bills.print', $bill)); ?>" target="_blank"
               class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Print
            </a>
            <a href="<?php echo e(route('bills.download', $bill)); ?>"
               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
               title="Download PDF">
                Download
            </a>
            <a href="<?php echo e(route('orders.index')); ?>" 
               class="bg-white border border-gray-300 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="py-8 px-8 bg-gradient-to-r from-gray-100 to-gray-200 border-b">
            <div class="text-center max-w-md mx-auto">
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight leading-tight">Restaurant POS</h2>
            </div>
        </div>

        <!-- Bill Info -->
        <div class="p-6 border-b">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-600">Bill Number</p>
                    <p class="font-semibold"><?php echo e($bill->bill_number); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Order Number</p>
                    <p class="font-semibold"><?php echo e($bill->order->order_number); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Table</p>
                    <p class="font-semibold"><?php echo e($bill->order->restaurantTable->table_number); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Date</p>
                    <p class="font-semibold"><?php echo e($bill->created_at->format('M d, Y h:i A')); ?></p>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold mb-4">Order Items</h3>
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Item</th>
                        <th class="text-center py-2">Qty</th>
                        <th class="text-center py-2">Status</th>
                        <th class="text-right py-2">Price</th>
                        <th class="text-right py-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $bill->order->orderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="border-b <?php echo e($item->status === 'cancelled' ? 'bg-red-50' : ''); ?>">
                            <td class="py-3"><?php echo e($item->menuItem->name); ?></td>
                            <td class="text-center py-3"><?php echo e($item->quantity); ?></td>
                            <td class="text-center py-3">
                                <?php if($item->status === 'cancelled'): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Cancelled
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <?php echo e(ucfirst($item->status)); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right py-3">₹<?php echo e(number_format($item->unit_price, 2)); ?></td>
                            <td class="text-right py-3">
                                <?php if($item->status === 'cancelled'): ?>
                                    <span class="text-red-600 font-semibold">₹0.00</span>
                                    <div class="text-xs text-red-600">(Cancelled)</div>
                                <?php else: ?>
                                    <span class="font-semibold">₹<?php echo e(number_format($item->total_price, 2)); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="p-6 bg-gray-50">
            <div class="max-w-sm ml-auto space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-semibold">₹<?php echo e(number_format($bill->subtotal, 2)); ?></span>
                </div>
                <?php if($taxAmount > 0): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600">CGST (<?php echo e($taxRate / 2); ?>%):</span>
                        <span class="font-semibold">₹<?php echo e(number_format($taxAmount / 2, 2)); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">SGST (<?php echo e($taxRate / 2); ?>%):</span>
                        <span class="font-semibold">₹<?php echo e(number_format($taxAmount / 2, 2)); ?></span>
                    </div>
                <?php endif; ?>
                <?php if($serviceCharge > 0): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Service Charge (<?php echo e($serviceChargeRate); ?>%):</span>
                        <span class="font-semibold">₹<?php echo e(number_format($serviceCharge, 2)); ?></span>
                    </div>
                <?php endif; ?>
                <?php if($bill->discount_amount > 0): ?>
                    <div class="flex justify-between text-green-600">
                        <span>Discount (<?php echo e($bill->discount_percentage); ?>%):</span>
                        <span class="font-semibold">-₹<?php echo e(number_format($bill->discount_amount, 2)); ?></span>
                    </div>
                <?php endif; ?>
                <div class="flex justify-between text-xl font-bold pt-2 border-t-2">
                    <span>Total Amount:</span>
                    <span class="text-indigo-600">₹<?php echo e(number_format($newTotal, 2)); ?></span>
                </div>
            </div>

            <?php
                $cancelledItems = $bill->order->orderItems->where('status', 'cancelled')->count();
            ?>
            <?php if($cancelledItems > 0): ?>
                <div class="mt-6 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-800">
                        <strong>Note:</strong> <?php echo e($cancelledItems); ?> item<?php echo e($cancelledItems > 1 ? 's' : ''); ?> <?php echo e($cancelledItems > 1 ? 'were' : 'was'); ?> cancelled and <?php echo e($cancelledItems > 1 ? 'are' : 'is'); ?> not included in the total amount.
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Payment Status -->
        <div class="p-6 border-t">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Payment Status</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        <?php echo e($bill->status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                        <?php echo e(ucfirst($bill->status)); ?>

                    </span>
                </div>
                <?php if($bill->payment_method): ?>
                    <div>
                        <p class="text-sm text-gray-600">Payment Method</p>
                        <p class="font-semibold"><?php echo e(strtoupper($bill->payment_method)); ?></p>
                    </div>
                <?php endif; ?>
                <?php if($bill->paid_at): ?>
                    <div>
                        <p class="text-sm text-gray-600">Paid At</p>
                        <p class="font-semibold"><?php echo e($bill->paid_at->format('M d, Y H:i')); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payment Form -->
        <?php if($bill->status !== 'paid'): ?>
            <div class="p-6 bg-gray-50 border-t">
                <form action="<?php echo e(route('bills.update', $bill)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Payment Method Toggle -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <div class="flex flex-wrap gap-2">
                                <input type="hidden" name="payment_method" id="payment_method" value="cash">
                                <button type="button" 
                                        class="payment-btn px-3 py-1.5 text-sm rounded-md font-medium transition-colors bg-green-100 text-green-800 border-2 border-green-300" 
                                        data-value="cash"
                                        onclick="selectPaymentMethod('cash', this)">
                                    Cash
                                </button>
                                <button type="button" 
                                        class="payment-btn px-3 py-1.5 text-sm rounded-md font-medium transition-colors bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200" 
                                        data-value="card"
                                        onclick="selectPaymentMethod('card', this)">
                                    Card
                                </button>
                                <button type="button" 
                                        class="payment-btn px-3 py-1.5 text-sm rounded-md font-medium transition-colors bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200" 
                                        data-value="upi"
                                        onclick="selectPaymentMethod('upi', this)">
                                    UPI
                                </button>
                                <button type="button" 
                                        class="payment-btn px-3 py-1.5 text-sm rounded-md font-medium transition-colors bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200" 
                                        data-value="other"
                                        onclick="selectPaymentMethod('other', this)">
                                    Other
                                </button>
                            </div>
                        </div>

                        <!-- Status Toggle -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <div class="flex flex-wrap gap-2">
                                <input type="hidden" name="status" id="status" value="paid">
                                <button type="button" 
                                        class="status-btn px-3 py-1.5 text-sm rounded-md font-medium transition-colors bg-green-100 text-green-800 border-2 border-green-300" 
                                        data-value="paid"
                                        onclick="selectStatus('paid', this)">
                                    Paid
                                </button>
                                <button type="button" 
                                        class="status-btn px-3 py-1.5 text-sm rounded-md font-medium transition-colors bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200" 
                                        data-value="pending"
                                        onclick="selectStatus('pending', this)">
                                    Pending
                                </button>
                                <button type="button" 
                                        class="status-btn px-3 py-1.5 text-sm rounded-md font-medium transition-colors bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200" 
                                        data-value="cancelled"
                                        onclick="selectStatus('cancelled', this)">
                                    Cancelled
                                </button>
                            </div>
                        </div>
                    </div>

                    <script>
                        function selectPaymentMethod(value, element) {
                            // Update hidden input value
                            document.getElementById('payment_method').value = value;
                            
                            // Update button styles
                            document.querySelectorAll('.payment-btn').forEach(btn => {
                                btn.classList.remove('bg-green-100', 'text-green-800', 'border-green-300');
                                btn.classList.add('bg-gray-100', 'text-gray-700', 'border-gray-200');
                            });
                            
                            // Highlight selected button
                            element.classList.remove('bg-gray-100', 'text-gray-700', 'border-gray-200');
                            element.classList.add('bg-green-100', 'text-green-800', 'border-green-300');
                        }

                        function selectStatus(value, element) {
                            // Update hidden input value
                            document.getElementById('status').value = value;
                            
                            // Update button styles
                            const statusBtns = document.querySelectorAll('.status-btn');
                            statusBtns.forEach(btn => {
                                btn.classList.remove('bg-green-100', 'text-green-800', 'border-green-300');
                                btn.classList.add('bg-gray-100', 'text-gray-700', 'border-gray-200');
                            });
                            
                            // Highlight selected button
                            element.classList.remove('bg-gray-100', 'text-gray-700', 'border-gray-200');
                            element.classList.add('bg-green-100', 'text-green-800', 'border-green-300');
                        }
                    </script>
                    
                    <div class="mt-6 border-t pt-4">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 px-4 rounded-md transition-colors text-base shadow-sm hover:shadow-md">
                            Process Payment
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Thank You Message -->
        <div class="p-6 bg-gray-50 border-t text-center">
            <p class="text-lg font-medium text-gray-700 mb-1">Thank you for dining with us!</p>
            <p class="text-sm text-gray-500">We hope to see you again soon</p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\restaurant-pos-desktop\backend\resources\views/bills/show.blade.php ENDPATH**/ ?>