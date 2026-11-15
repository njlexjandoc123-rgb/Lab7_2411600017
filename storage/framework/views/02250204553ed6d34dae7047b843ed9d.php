

<?php $__env->startSection('content'); ?>
<div class="mx-auto max-w-5xl rounded-xl bg-white p-6 shadow">
    <h1 class="mb-6 text-2xl font-bold text-slate-800">Create purchase order</h1>
    <form method="POST" action="<?php echo e(route('purchase-orders.store')); ?>">
        <?php echo csrf_field(); ?>
        <?php echo $__env->make('purchase-orders._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="mt-6 flex justify-between">
            <a href="<?php echo e(route('purchase-orders.index')); ?>" class="rounded border px-4 py-2 text-sm">Cancel</a>
            <button class="rounded bg-sky-700 px-5 py-2 text-sm font-semibold text-white">Save purchase order</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/purchase-orders/create.blade.php ENDPATH**/ ?>