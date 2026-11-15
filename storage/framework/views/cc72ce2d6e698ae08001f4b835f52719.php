<section class="rounded-xl bg-white p-5 shadow">
    <div class="mb-3 flex items-center justify-between"><h2 class="font-bold text-slate-800">Reorder alerts</h2><a href="<?php echo e(route('purchase-orders.create')); ?>" class="text-sm font-semibold text-sky-700">Create order</a></div>
    <ul class="divide-y">
        <?php $__empty_1 = true; $__currentLoopData = $reorderProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li class="flex justify-between gap-3 py-2 text-sm"><span><?php echo e($product->name); ?> <span class="text-slate-500">(<?php echo e($product->supplier?->name ?? 'No supplier'); ?>)</span></span><span class="font-semibold text-amber-700"><?php echo e($product->qty); ?> / <?php echo e($product->calculated_reorder_point); ?></span></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="py-4 text-sm text-slate-500">No products currently need reordering.</li>
        <?php endif; ?>
    </ul>
</section>
<?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/widgets/reorder-alerts.blade.php ENDPATH**/ ?>