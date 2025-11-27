<?php $__env->startSection('title', 'Salary Advance Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Salary Advance Details</h1>
                <p class="text-gray-600">View salary advance information</p>
            </div>
            <a href="<?php echo e(route('staff-salary-advances.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded transition">
                Back to Advances
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="space-y-6">
            <!-- Staff Member -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Staff Member</label>
                <p class="text-base text-gray-900"><?php echo e($advance->employee->name); ?></p>
            </div>

            <!-- Amount -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Amount</label>
                <p class="text-2xl font-bold text-indigo-600">₹<?php echo e(number_format($advance->amount, 2)); ?></p>
            </div>

            <!-- Advance Date -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Advance Date</label>
                <p class="text-base text-gray-900"><?php echo e($advance->advance_date->format('M d, Y')); ?></p>
            </div>

            <!-- Payment Method -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Payment Method</label>
                <p class="text-base text-gray-900"><?php echo e(ucfirst(str_replace('_', ' ', $advance->payment_method))); ?></p>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Approved
                </span>
            </div>

            <?php if($advance->notes): ?>
            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Notes</label>
                <p class="text-base text-gray-900 whitespace-pre-line"><?php echo e($advance->notes); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Advance Information -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">Advance Information</h3>
        <div class="text-sm text-blue-800 space-y-1">
            <p><strong>Advance ID:</strong> #<?php echo e($advance->id); ?></p>
            <p><strong>Created:</strong> <?php echo e($advance->created_at->format('M d, Y h:i A')); ?></p>
            <p><strong>Last Updated:</strong> <?php echo e($advance->updated_at->format('M d, Y h:i A')); ?></p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\restaurant-pos-desktop\backend\resources\views/staff/salary-advances/show.blade.php ENDPATH**/ ?>