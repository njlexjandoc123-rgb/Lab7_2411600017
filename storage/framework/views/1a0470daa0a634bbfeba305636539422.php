<section class="rounded-xl bg-white p-5 shadow">
    <div class="mb-3 flex items-center justify-between"><h2 class="font-bold text-slate-800">Supplier performance</h2><a href="<?php echo e(route('supplier-performance.index')); ?>" class="text-sm font-semibold text-sky-700">Report</a></div>
    <ul class="divide-y">
        <?php $__empty_1 = true; $__currentLoopData = $topSuppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li class="flex justify-between py-2 text-sm"><span><?php echo e($supplier->name); ?></span><span class="font-semibold"><?php echo e($supplierPerformanceMetrics[$supplier->id]['on_time_percentage'] ?? 0); ?>% on time</span></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="py-4 text-sm text-slate-500">Add suppliers to track performance.</li>
        <?php endif; ?>
    </ul>
</section>
<?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/widgets/supplier-performance.blade.php ENDPATH**/ ?>