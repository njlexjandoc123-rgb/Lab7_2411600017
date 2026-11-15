

<?php $__env->startSection('content'); ?>
<div class="mx-auto max-w-3xl rounded-xl bg-white p-6 shadow">
    <h1 class="mb-6 text-2xl font-bold text-slate-800">Add supplier</h1>
    <form method="POST" action="<?php echo e(route('suppliers.store')); ?>">
        <?php echo csrf_field(); ?>
        <?php echo $__env->make('suppliers._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/suppliers/create.blade.php ENDPATH**/ ?>