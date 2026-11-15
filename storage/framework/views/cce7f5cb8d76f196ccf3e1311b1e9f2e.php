

<?php $__env->startSection('content'); ?>
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="text-3xl font-bold text-slate-800">Supplier performance</h1><p class="mt-1 text-sm text-slate-500">Delivery timeliness, order accuracy, and spend.</p></div>
    <div class="flex gap-2">
        <a href="<?php echo e(route('exports.download', ['type' => 'supplier-performance', 'format' => 'csv'])); ?>" class="rounded border px-3 py-2 text-sm">Export CSV</a>
        <a href="<?php echo e(route('exports.download', ['type' => 'supplier-performance', 'format' => 'pdf'])); ?>" class="rounded border px-3 py-2 text-sm">Export PDF</a>
    </div>
</div>
<div class="mb-6 grid gap-4 md:grid-cols-2">
    <section class="rounded-lg bg-white p-5 shadow">
        <h2 class="mb-3 font-bold">Top suppliers by on-time delivery</h2>
        <?php $__empty_1 = true; $__currentLoopData = $rankedSuppliers->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="mb-2 flex items-center justify-between text-sm"><span><?php echo e($supplier->name); ?></span><span class="font-semibold"><?php echo e($metrics[$supplier->id]['on_time_percentage'] ?? 0); ?>%</span></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-slate-500">No supplier delivery history yet.</p>
        <?php endif; ?>
    </section>
    <section class="rounded-lg bg-white p-5 shadow">
        <h2 class="mb-3 font-bold">Lowest on-time delivery</h2>
        <?php $__empty_1 = true; $__currentLoopData = $rankedSuppliers->reverse()->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="mb-2 flex items-center justify-between text-sm"><span><?php echo e($supplier->name); ?></span><span class="font-semibold"><?php echo e($metrics[$supplier->id]['on_time_percentage'] ?? 0); ?>%</span></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-slate-500">No supplier delivery history yet.</p>
        <?php endif; ?>
    </section>
</div>
<section class="mb-6 rounded-lg bg-white p-5 shadow">
    <h2 class="mb-4 font-bold">Monthly delivery trend</h2>
    <div class="grid h-44 grid-cols-6 items-end gap-3">
        <?php $__currentLoopData = $deliveryTrend; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex h-full flex-col items-center justify-end gap-2">
                <span class="text-xs font-semibold"><?php echo e($month['deliveries']); ?> / <?php echo e($month['on_time_percentage']); ?>%</span>
                <div class="w-full rounded-t bg-sky-600" style="height: <?php echo e($month['deliveries'] ? max(12, $month['on_time_percentage']) : 4); ?>%"></div>
                <span class="text-xs text-slate-500"><?php echo e(\Carbon\Carbon::createFromFormat('Y-m', $month['month'])->format('M')); ?></span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <p class="mt-2 text-xs text-slate-500">Each bar shows the on-time delivery percentage for the month; counts are displayed above.</p>
</section>
<div class="overflow-x-auto rounded-lg bg-white shadow">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-sky-950 text-white"><tr><th class="px-4 py-3">Supplier</th><th class="px-4 py-3">Orders</th><th class="px-4 py-3">Delivered</th><th class="px-4 py-3">On time</th><th class="px-4 py-3">Accuracy</th><th class="px-4 py-3">Avg. days</th><th class="px-4 py-3">Vs expected</th><th class="px-4 py-3">Order value</th></tr></thead>
        <tbody class="divide-y">
        <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php ($metric = $metrics[$supplier->id]); ?>
            <tr><td class="px-4 py-3 font-medium"><?php echo e($supplier->name); ?></td><td class="px-4 py-3"><?php echo e($metric['total_orders']); ?></td><td class="px-4 py-3"><?php echo e($metric['delivered_orders']); ?></td><td class="px-4 py-3"><?php echo e($metric['on_time_percentage']); ?>%</td><td class="px-4 py-3"><?php echo e($metric['accuracy_percentage']); ?>%</td><td class="px-4 py-3"><?php echo e($metric['average_delivery_days']); ?></td><td class="px-4 py-3"><?php echo e($metric['average_delivery_variance_days']); ?> days</td><td class="px-4 py-3"><?php echo e(number_format((float) $metric['total_value'], 2)); ?></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="8" class="px-4 py-8 text-center text-slate-500">No active suppliers found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/reports/supplier-performance.blade.php ENDPATH**/ ?>