<?php $__env->startSection('content'); ?>
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Student Portal Inventory</h1>
    <a href="<?php echo e(route('products.create')); ?>" class="bg-[#0369a1] hover:bg-[#075985] text-white px-4 py-2 rounded shadow text-sm font-semibold">+ Add Product</a>
</div>

<?php if (isset($component)) { $__componentOriginal934f921620666b609fa7806109faa21b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal934f921620666b609fa7806109faa21b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-form','data' => ['action' => route('products.index'),'class' => 'mb-5 grid gap-3 rounded-lg bg-white p-4 shadow md:grid-cols-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('products.index')),'class' => 'mb-5 grid gap-3 rounded-lg bg-white p-4 shadow md:grid-cols-4']); ?>
    <input name="q" value="<?php echo e(request('q')); ?>" placeholder="Product, SKU, or supplier" class="rounded border px-3 py-2 text-sm md:col-span-2">
    <select name="category" class="rounded border px-3 py-2 text-sm">
        <option value="">All categories</option>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($category); ?>" <?php if(request('category') === $category): echo 'selected'; endif; ?>><?php echo e($category); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="supplier_id" class="rounded border px-3 py-2 text-sm">
        <option value="">All suppliers</option>
        <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($supplier->id); ?>" <?php if(request('supplier_id') == $supplier->id): echo 'selected'; endif; ?>><?php echo e($supplier->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <div class="flex gap-2 md:col-span-4">
        <button class="rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white">Filter</button>
        <a href="<?php echo e(route('exports.download', ['type' => 'products', 'format' => 'csv'])); ?>" class="rounded border px-4 py-2 text-sm">Export CSV</a>
        <a href="<?php echo e(route('exports.download', ['type' => 'products', 'format' => 'pdf'])); ?>" class="rounded border px-4 py-2 text-sm">Export PDF</a>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal934f921620666b609fa7806109faa21b)): ?>
<?php $attributes = $__attributesOriginal934f921620666b609fa7806109faa21b; ?>
<?php unset($__attributesOriginal934f921620666b609fa7806109faa21b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal934f921620666b609fa7806109faa21b)): ?>
<?php $component = $__componentOriginal934f921620666b609fa7806109faa21b; ?>
<?php unset($__componentOriginal934f921620666b609fa7806109faa21b); ?>
<?php endif; ?>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-sky-950 text-white text-xs uppercase tracking-wider">
                <th class="py-3 px-4">SKU</th>
                <th class="py-3 px-4">Product Name</th>
                <th class="py-3 px-4">Brand</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4">Supplier</th>
                <th class="py-3 px-4">Qty</th>
                <th class="py-3 px-4">Unit Price</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 text-sm">
            <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="hover:bg-gray-50">
                <td class="py-3 px-4 text-amber-600 font-mono text-xs font-semibold"><?php echo e($product->sku); ?></td>
                <td class="py-3 px-4 font-semibold text-gray-800"><?php echo e($product->name); ?></td>
                <td class="py-3 px-4"><span class="bg-gray-600 text-white text-xs px-2.5 py-0.5 rounded"><?php echo e($product->brand ?? 'N/A'); ?></span></td>
                <td class="py-3 px-4"><span class="bg-sky-700 text-white text-xs px-2.5 py-0.5 rounded"><?php echo e($product->category ?? 'N/A'); ?></span></td>
                <td class="py-3 px-4"><?php echo e($product->supplier?->name ?? '—'); ?></td>
                <td class="py-3 px-4 font-bold"><?php echo e($product->qty); ?></td>
                <td class="py-3 px-4">₱<?php echo e(number_format($product->unit_price, 2)); ?></td>
                <td class="py-3 px-4">
                    <span class="px-2.5 py-1 rounded font-semibold text-xs <?php echo e($product->qty > 5 ? 'bg-emerald-100 text-emerald-800' : ($product->qty > 0 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800')); ?>">
                        <?php echo e($product->qty > 5 ? 'In Stock' : ($product->qty > 0 ? 'Low Stock' : 'Out of Stock')); ?>

                    </span>
                </td>
                <td class="py-3 px-4 text-center space-x-1">
                    <a href="<?php echo e(route('products.edit', $product->id)); ?>" class="bg-gray-100 hover:bg-gray-200 p-1.5 rounded text-xs inline-block">✏️</a>
                    <form action="<?php echo e(route('products.destroy', $product->id)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="bg-gray-100 hover:bg-gray-200 p-1.5 rounded text-xs text-red-600" onclick="return confirm('Sigurado ka bang burahin ito?')">🗑️</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="9" class="py-6 px-4 text-center text-gray-500">
                    No products found. <a href="<?php echo e(route('products.create')); ?>" class="text-sky-700 hover:underline">Add your first product</a>.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <!-- Pagination Footer -->
    <div class="px-4 py-3 bg-gray-50 border-t flex justify-between items-center text-xs text-gray-500">
        <span>
            <?php if($products->total() > 0): ?>
                Showing <?php echo e($products->firstItem()); ?>-<?php echo e($products->lastItem()); ?> of <?php echo e($products->total()); ?> products
            <?php else: ?>
                Showing 0 of 0 products
            <?php endif; ?>
        </span>
        <div>
            <?php echo e($products->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/products/index.blade.php ENDPATH**/ ?>