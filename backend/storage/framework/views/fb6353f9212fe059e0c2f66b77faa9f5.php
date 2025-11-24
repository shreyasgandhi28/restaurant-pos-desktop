<?php $__env->startSection('title', 'Menu Items'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Menu Items</h1>
            <p class="text-gray-600">Manage your restaurant menu</p>
        </div>
        <a href="<?php echo e(route('menu-items.create')); ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
            Add Menu Item
        </a>
    </div>

    <!-- Category Filter with Search -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex items-center justify-between gap-4">
            <!-- Category Buttons - Left -->
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap gap-2">
                    <a href="<?php echo e(route('menu-items.index')); ?>" 
                       class="px-4 py-2 text-sm font-medium rounded-md <?php echo e(!request('category') ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?> transition-colors whitespace-nowrap">
                        All Items
                    </a>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('menu-items.index', ['category' => $category->id])); ?>" 
                           class="px-4 py-2 text-sm font-medium rounded-md <?php echo e(request('category') == $category->id ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?> transition-colors whitespace-nowrap">
                            <?php echo e($category->name); ?>

                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            
            <!-- Search Box - Right -->
            <div class="flex-shrink-0">
                <input type="search" 
                       id="searchInput" 
                       placeholder="Search menu items..." 
                       class="w-64 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
    </div>

    <!-- Menu Items Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php $__currentLoopData = $menuItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="h-48 bg-gray-200 relative">
                    <?php if($item->image): ?>
                        <img src="<?php echo e(asset('storage/' . $item->image)); ?>" alt="<?php echo e($item->name); ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-400 to-purple-500">
                            <svg class="h-20 w-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                    <?php endif; ?>
                    <?php if($item->is_featured): ?>
                        <span class="absolute top-2 right-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded">
                            Featured
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="p-4">
                    <div class="mb-2">
                        <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded">
                            <?php echo e($item->category->name); ?>

                        </span>
                    </div>
                    
                    <h3 class="text-lg font-semibold text-gray-900 mb-1"><?php echo e($item->name); ?></h3>
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2"><?php echo e($item->description); ?></p>
                    
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-2xl font-bold text-indigo-600">₹<?php echo e(number_format($item->price, 2)); ?></span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            <?php echo e($item->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                            <?php echo e($item->is_available ? 'Available' : 'Unavailable'); ?>

                        </span>
                    </div>

                    <div class="flex space-x-2">
                        <a href="<?php echo e(route('menu-items.edit', $item)); ?>" 
                           class="flex-1 text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded transition">
                            Edit
                        </a>
                        <form action="<?php echo e(route('menu-items.destroy', $item)); ?>" method="POST" class="flex-1" 
                              onsubmit="return confirm('Are you sure you want to delete this item?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded transition">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($menuItems->isEmpty()): ?>
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No menu items</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new menu item.</p>
            <div class="mt-6">
                <a href="<?php echo e(route('menu-items.create')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Add Menu Item
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if($menuItems->hasPages()): ?>
        <div class="mt-6">
            <?php echo e($menuItems->links()); ?>

        </div>
    <?php endif; ?>
</div>

<script>
// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const menuItems = document.querySelectorAll('.grid > div');
    let visibleCount = 0;
    
    menuItems.forEach(item => {
        const name = item.querySelector('h3')?.textContent.toLowerCase() || '';
        const description = item.querySelector('.line-clamp-2')?.textContent.toLowerCase() || '';
        
        if (name.includes(searchTerm) || description.includes(searchTerm)) {
            item.style.display = 'block';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
    
    // Show/hide empty state message
    const emptyState = document.querySelector('.grid').nextElementSibling;
    if (visibleCount === 0 && searchTerm !== '') {
        if (!document.getElementById('noResultsMessage')) {
            const noResults = document.createElement('div');
            noResults.id = 'noResultsMessage';
            noResults.className = 'bg-white rounded-lg shadow p-12 text-center mt-6';
            noResults.innerHTML = `
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No results found</h3>
                <p class="mt-1 text-sm text-gray-500">Try searching with different keywords.</p>
            `;
            document.querySelector('.grid').parentNode.appendChild(noResults);
        }
    } else {
        const noResults = document.getElementById('noResultsMessage');
        if (noResults) {
            noResults.remove();
        }
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\restaurant-pos-desktop\backend\resources\views\menu-items\index.blade.php ENDPATH**/ ?>