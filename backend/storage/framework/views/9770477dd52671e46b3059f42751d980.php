<?php $__env->startSection('title', 'Orders'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Orders</h1>
        <p class="text-gray-600">View and manage all orders</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-3 mb-6">
        <form method="GET" action="<?php echo e(route('orders.index')); ?>" class="flex flex-wrap items-center gap-3">
            <!-- Search -->
            <div class="flex-1 min-w-64">
                <div class="relative">
                    <input type="search"
                           name="search"
                           value="<?php echo e(request('search')); ?>"
                           placeholder="Search orders..."
                           class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <!-- Payment Status Filter -->
            <div class="w-32">
                <select name="payment_status"
                        class="w-full h-10 py-2 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="all" <?php echo e(request('payment_status') === 'all' || !request('payment_status') ? 'selected' : ''); ?>>All Payments</option>
                    <option value="paid" <?php echo e(request('payment_status') === 'paid' ? 'selected' : ''); ?>>Paid</option>
                    <option value="pending" <?php echo e(request('payment_status') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                    <option value="unpaid" <?php echo e(request('payment_status') === 'unpaid' ? 'selected' : ''); ?>>Unpaid</option>
                    <option value="cancelled" <?php echo e(request('payment_status') === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                </select>
            </div>

            <!-- Table Filter -->
            <div class="w-24">
                <input type="text"
                       name="table"
                       value="<?php echo e(request('table')); ?>"
                       placeholder="Table"
                       class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Server Filter -->
            <div class="w-32">
                <input type="text"
                       name="server"
                       value="<?php echo e(request('server')); ?>"
                       placeholder="Server"
                       class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Filter Button -->
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md text-sm transition-colors">
                Filter
            </button>

            <!-- Clear Filters -->
            <?php if(request()->hasAny(['search', 'status', 'payment_status', 'table', 'server'])): ?>
                <a href="<?php echo e(route('orders.index')); ?>" class="text-gray-600 hover:text-gray-800 font-medium py-2 px-3 text-sm">
                    Clear
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bill #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Table</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Server</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Paid</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?php echo e($order->order_number); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo e($order->bill ? $order->bill->bill_number : '-'); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo e($order->restaurantTable->table_number); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo e($order->user->name); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                                $paymentStatus = $order->bill ? $order->bill->status : 'unpaid';
                                $statusClass = [
                                    'paid' => 'bg-green-100 text-green-800',
                                    'partially_paid' => 'bg-blue-100 text-blue-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'unpaid' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                    'refunded' => 'bg-purple-100 text-purple-800'
                                ][$paymentStatus] ?? 'bg-gray-100 text-gray-800';
                                $statusDisplay = [
                                    'paid' => 'Paid',
                                    'pending' => 'Pending',
                                    'cancelled' => 'Cancelled'
                                ][$paymentStatus] ?? ucfirst($paymentStatus);
                            ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($statusClass); ?>">
                                <?php echo e($statusDisplay); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">
                                <?php if($order->bill): ?>
                                    ₹<?php echo e(number_format($order->bill->amount_paid, 2)); ?>

                                    <?php if($order->bill->status !== 'paid' && $order->bill->total_amount > 0): ?>
                                        <span class="text-xs text-gray-500">/ ₹<?php echo e(number_format($order->bill->total_amount, 2)); ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    ₹0.00
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500"><?php echo e($order->created_at->format('M d, Y h:i A')); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex justify-end">
                                <?php if (isset($component)) { $__componentOriginal4e890f501286ae82ea79f41860114a8d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4e890f501286ae82ea79f41860114a8d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.bill.generate-button','data' => ['order' => $order]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bill.generate-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['order' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4e890f501286ae82ea79f41860114a8d)): ?>
<?php $attributes = $__attributesOriginal4e890f501286ae82ea79f41860114a8d; ?>
<?php unset($__attributesOriginal4e890f501286ae82ea79f41860114a8d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4e890f501286ae82ea79f41860114a8d)): ?>
<?php $component = $__componentOriginal4e890f501286ae82ea79f41860114a8d; ?>
<?php unset($__componentOriginal4e890f501286ae82ea79f41860114a8d); ?>
<?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No orders</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new order from a table.</p>
                            <div class="mt-6">
                                <a href="<?php echo e(route('tables.index')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Go to Tables
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if($orders->hasPages()): ?>
        <div class="mt-6">
            <?php echo e($orders->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\restaurant-pos-desktop\backend\resources\views\orders\index.blade.php ENDPATH**/ ?>