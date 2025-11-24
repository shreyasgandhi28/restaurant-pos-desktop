<?php $__env->startSection('title', 'Generate Bill'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Generate Bill</h1>
        <p class="text-gray-600">Order #<?php echo e($order->order_number); ?> - Table <?php echo e($order->restaurantTable->table_number); ?></p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Items Preview -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Order Items</h2>
                </div>
                <div class="p-6">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Item</th>
                                <th class="text-center py-2">Qty</th>
                                <th class="text-right py-2">Price</th>
                                <th class="text-right py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $order->orderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="border-b">
                                    <td class="py-3"><?php echo e($item->menuItem->name); ?></td>
                                    <td class="text-center py-3"><?php echo e($item->quantity); ?></td>
                                    <td class="text-right py-3">₹<?php echo e(number_format($item->unit_price, 2)); ?></td>
                                    <td class="text-right py-3">₹<?php echo e(number_format($item->total_price, 2)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Bill Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow sticky top-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Bill Details</h2>
                </div>
                
                <form action="<?php echo e(route('bills.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="order_id" value="<?php echo e($order->id); ?>">
                    
                    <div class="p-6 space-y-4">
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-semibold" id="subtotal">₹<?php echo e(number_format($order->subtotal, 2)); ?></span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span>Service Charge (<?php echo e($settings['service_charge_rate']); ?>%):</span>
                                <span>₹<span id="service-charge"><?php echo e(number_format($order->subtotal * ($settings['service_charge_rate'] / 100), 2)); ?></span></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tax (<?php echo e($settings['tax_rate']); ?>%):</span>
                                <span class="font-semibold" id="tax">₹<?php echo e(number_format($order->subtotal * ($settings['tax_rate'] / 100), 2)); ?></span>
                            </div>
                        </div>

                        <!-- Discount -->
                        <div class="border-t pt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Discount (%)</label>
                            <input type="number" name="discount_percentage" id="discountPercentage" 
                                   min="0" max="100" step="0.01" value="0"
                                   class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                   onchange="calculateTotal()">
                            
                            <div class="flex justify-between text-sm mt-2 text-green-600">
                                <span>Discount Amount:</span>
                                <span class="font-semibold" id="discountAmount">₹0.00</span>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total Amount:</span>
                                <span class="text-indigo-600" id="totalAmount">₹<?php echo e(number_format($order->total, 2)); ?></span>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded">
                                Generate Bill
                            </button>
                            <a href="<?php echo e(route('orders.show', $order)); ?>" 
                               class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-4 rounded mt-2">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const subtotal = <?php echo e($order->subtotal); ?>;
const taxRate = <?php echo e($settings['tax_rate'] / 100); ?>;
const serviceChargeRate = <?php echo e($settings['service_charge_rate'] / 100); ?>;
const serviceCharge = <?php echo e($order->service_charge); ?>;

function calculateTotal() {
    const discountPercentage = parseFloat(document.getElementById('discountPercentage').value) || 0;
    const tax = subtotal * taxRate;
    const discountAmount = subtotal * (discountPercentage / 100);
    const total = subtotal + tax + serviceCharge - discountAmount;
    
    document.getElementById('discountAmount').textContent = '₹' + discountAmount.toFixed(2);
    document.getElementById('totalAmount').textContent = '₹' + total.toFixed(2);
}

// Initialize
calculateTotal();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\restaurant-pos-desktop\backend\resources\views\bills\create.blade.php ENDPATH**/ ?>