<section class="rounded-xl bg-white p-5 shadow">
    <div class="flex items-center justify-between"><h2 class="font-bold text-slate-800">Pending approvals</h2><a href="<?php echo e(route('purchase-orders.index', ['status' => 'pending'])); ?>" class="text-sm font-semibold text-sky-700">Review</a></div>
    <p class="mt-3 text-3xl font-bold text-amber-600"><?php echo e(number_format($pendingOrdersCount)); ?></p>
    <p class="text-xs text-slate-500">Purchase orders awaiting approval</p>
</section>
<?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/widgets/pending-orders.blade.php ENDPATH**/ ?>