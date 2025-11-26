<?php $__env->startSection('title', 'Restaurant Tables'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Restaurant Tables</h1>
            <p class="text-gray-600">Manage table status and orders</p>
        </div>
        <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin')): ?>
        <div class="flex space-x-2">
            <button onclick="fixTableStatuses()" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                Fix Table Statuses
            </button>
            <a href="<?php echo e(route('tables.create')); ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                Add New Table
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Table Status Legend -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex items-center space-x-6">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                <span class="text-sm text-gray-700">Available</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                <span class="text-sm text-gray-700">Occupied</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                <span class="text-sm text-gray-700">Reserved</span>
            </div>
        </div>
    </div>

    <!-- Tables Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php $__currentLoopData = $tables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition hover:scale-105">
                <div class="p-6 
                    <?php if($table->effective_status == 'available'): ?> bg-gradient-to-br from-green-400 to-green-600
                    <?php elseif($table->effective_status == 'occupied'): ?> bg-gradient-to-br from-red-400 to-red-600
                    <?php else: ?> bg-gradient-to-br from-yellow-400 to-yellow-600
                    <?php endif; ?>">
                    <div class="text-center text-white">
                        <div class="text-4xl font-bold mb-2"><?php echo e($table->table_number); ?></div>
                        <div class="text-sm opacity-90">Capacity: <?php echo e($table->capacity); ?> persons</div>
                    </div>
                </div>
                
                <div class="p-4 bg-white">
                    <div class="mb-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            <?php if($table->effective_status == 'available'): ?> bg-green-100 text-green-800
                            <?php elseif($table->effective_status == 'occupied'): ?> bg-red-100 text-red-800
                            <?php else: ?> bg-yellow-100 text-yellow-800
                            <?php endif; ?>">
                            <?php echo e(ucfirst($table->effective_status)); ?>

                        </span>
                    </div>

                    <?php if($table->effective_status == 'available'): ?>
                        <a href="<?php echo e(route('pos.index', ['table' => $table->id])); ?>" 
                           class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded transition">
                            Start Order
                        </a>
                    <?php elseif($table->effective_status == 'occupied'): ?>
                        <?php
                            $currentOrder = $table->orders()->whereIn('status', ['pending', 'preparing', 'ready'])->latest()->first();
                        ?>
                        <?php if($currentOrder): ?>
                            <div class="space-y-2">
                                <a href="<?php echo e(route('orders.show', $currentOrder)); ?>" 
                                   class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition text-sm">
                                    View Order
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <form action="<?php echo e(route('tables.update', $table)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="status" value="available">
                            <button type="submit" 
                                    class="block w-full text-center bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded transition">
                                Mark Available
                            </button>
                        </form>
                    <?php endif; ?>

                    <div class="mt-4 flex justify-between items-center text-sm text-gray-500">
                        <span>Table ID: <?php echo e($table->id); ?></span>
                        <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin|manager')): ?>
                        <!-- Duplicate Edit/Delete links removed -->
                        <?php endif; ?>
                    </div>
                    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin|manager')): ?>
                    <div class="mt-3 flex space-x-2">
                        <a href="<?php echo e(route('tables.edit', $table)); ?>" 
                           class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-1 px-2 rounded text-sm transition">
                            Edit
                        </a>
                        <form action="<?php echo e(route('tables.destroy', $table)); ?>" method="POST" class="flex-1" 
                              onsubmit="return confirm('Are you sure you want to delete this table?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" 
                                    class="w-full bg-red-200 hover:bg-red-300 text-red-700 font-semibold py-1 px-2 rounded text-sm transition">
                                Delete
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($tables->isEmpty()): ?>
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No tables</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new table.</p>
            <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin')): ?>
            <div class="mt-6">
                <a href="<?php echo e(route('tables.create')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Add New Table
                </a>
            </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Fix table statuses
async function fixTableStatuses() {
    if (!confirm('This will recalculate all table statuses based on active orders. Continue?')) {
        return;
    }

    try {
        const response = await fetch('/api/tables/recalculate-statuses', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${document.querySelector('meta[name="api-token"]')?.getAttribute('content') || '<?php echo e(auth()->user()->createToken("pos-token")->plainTextToken); ?>'}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const result = await response.json();

        if (response.ok) {
            alert('Table statuses fixed successfully!');
            location.reload();
        } else {
            alert('Error fixing table statuses: ' + result.message);
        }
    } catch (error) {
        console.error('Error fixing table statuses:', error);
        alert('Error fixing table statuses');
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\restaurant-pos-desktop\backend\resources\views/tables/index.blade.php ENDPATH**/ ?>