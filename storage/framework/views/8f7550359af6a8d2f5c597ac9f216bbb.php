<section class="rounded-xl bg-white p-5 shadow">
    <div class="mb-3 flex items-center justify-between"><h2 class="font-bold text-slate-800">Recent purchase orders</h2><a href="<?php echo e(route('purchase-orders.index')); ?>" class="text-sm font-semibold text-sky-700">View all</a></div>
    <ul class="divide-y">
        <?php $__empty_1 = true; $__currentLoopData = $recentPurchaseOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li class="flex justify-between gap-3 py-2 text-sm"><a class="font-medium text-sky-700 hover:underline" href="<?php echo e(route('purchase-orders.show', $order)); ?>"><?php echo e($order->reference_number); ?></a><span class="text-slate-500"><?php echo e($order->supplier?->name ?? 'Supplier'); ?> · <?php echo e(ucfirst($order->status)); ?></span></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="py-4 text-sm text-slate-500">No purchase orders yet.</li>
        <?php endif; ?>
    </ul>
</section>
<?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/widgets/recent-orders.blade.php ENDPATH**/ ?>