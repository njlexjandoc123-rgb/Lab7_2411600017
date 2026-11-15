

<?php $__env->startSection('content'); ?>
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Categories</h1>
        <p class="text-sm text-gray-500 mt-1">Manage categories and view their products.</p>
    </div>
    <button type="submit" form="add-category-form" class="bg-[#0369a1] hover:bg-[#075985] text-white px-4 py-2 rounded shadow text-sm font-semibold">+ Add Category</button>
</div>

<?php if(session('success')): ?>
    <div class="mb-4 rounded bg-emerald-100 px-4 py-3 text-sm text-emerald-800"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="mb-4 rounded bg-red-100 px-4 py-3 text-sm text-red-800"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<form id="add-category-form" action="<?php echo e(route('categories.store')); ?>" method="POST" class="mb-6 flex flex-col gap-2 rounded-lg bg-white p-4 shadow sm:flex-row sm:items-start">
    <?php echo csrf_field(); ?>
    <div class="flex-1">
        <label for="category-name" class="mb-1 block text-xs font-semibold uppercase text-gray-600">New category name</label>
        <input id="category-name" type="text" name="name" value="<?php echo e(old('name')); ?>" maxlength="100" required placeholder="e.g. Stationery" class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <button type="submit" class="mt-5 rounded bg-sky-950 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-900 sm:mt-6">Save Category</button>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-sky-950 text-white text-xs uppercase tracking-wider">
                <th class="py-3 px-4">Category Name</th>
                <th class="py-3 px-4">Products</th>
                <th class="py-3 px-4">Items</th>
                <th class="py-3 px-4 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 text-sm">
            <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="py-3 px-4 font-semibold text-gray-800"><?php echo e($category->name); ?></td>
                <td class="py-3 px-4">
                    <ul class="space-y-1">
                        <?php $__empty_2 = true; $__currentLoopData = $category->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                        <li>
                            <a href="<?php echo e(route('products.edit', $product)); ?>" class="text-sky-700 hover:underline"><?php echo e($product->name); ?></a>
                            <span class="text-gray-500">(<?php echo e($product->sku); ?>)</span>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                        <li class="text-gray-500">No products assigned yet.</li>
                        <?php endif; ?>
                    </ul>
                </td>
                <td class="py-3 px-4"><?php echo e($category->products->count()); ?></td>
                <td class="space-x-1 whitespace-nowrap py-3 px-4 text-center">
                    <a href="<?php echo e(route('categories.edit', $category)); ?>" class="inline-block rounded bg-gray-100 px-2 py-1 text-xs hover:bg-gray-200">Edit</a>
                    <form action="<?php echo e(route('categories.destroy', $category)); ?>" method="POST" class="inline" onsubmit="return confirm('Delete this category? Its products will remain but become unassigned.');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="rounded bg-red-50 px-2 py-1 text-xs text-red-700 hover:bg-red-100">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="4" class="py-6 px-4 text-center text-gray-500">
                    No categories found. Use Add Category to create the first one.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="px-4 py-3 bg-gray-50 border-t text-xs text-gray-500">
        <?php echo e($categories->count()); ?> <?php echo e(\Illuminate\Support\Str::plural('category', $categories->count())); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/categories/index.blade.php ENDPATH**/ ?>