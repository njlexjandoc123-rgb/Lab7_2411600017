

<?php $__env->startSection('content'); ?>
<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <a href="<?php echo e(route('purchase-orders.index')); ?>" class="text-sm text-sky-700 hover:underline">← Purchase orders</a>
        <h1 class="mt-2 text-3xl font-bold text-slate-800"><?php echo e($purchaseOrder->reference_number); ?></h1>
        <p class="mt-1 text-sm text-slate-500">Supplier: <?php echo e($purchaseOrder->supplier?->name ?? 'Archived supplier'); ?> · <?php echo e($purchaseOrder->order_date->format('M d, Y')); ?></p>
    </div>
    <span class="rounded-full bg-slate-100 px-3 py-1.5 text-sm font-semibold capitalize"><?php echo e($purchaseOrder->status); ?></span>
</div>

<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-lg bg-white p-4 shadow"><p class="text-xs uppercase text-slate-500">Subtotal</p><p class="mt-2 text-xl font-bold"><?php echo e(number_format((float) $purchaseOrder->subtotal, 2)); ?></p></div>
    <div class="rounded-lg bg-white p-4 shadow"><p class="text-xs uppercase text-slate-500">Tax</p><p class="mt-2 text-xl font-bold"><?php echo e(number_format((float) $purchaseOrder->tax_amount, 2)); ?></p></div>
    <div class="rounded-lg bg-white p-4 shadow"><p class="text-xs uppercase text-slate-500">Total</p><p class="mt-2 text-xl font-bold"><?php echo e(number_format((float) $purchaseOrder->total_amount, 2)); ?></p></div>
    <div class="rounded-lg bg-white p-4 shadow"><p class="text-xs uppercase text-slate-500">Expected delivery</p><p class="mt-2 text-xl font-bold"><?php echo e($purchaseOrder->expected_delivery_date?->format('M d, Y') ?? 'Not set'); ?></p></div>
</div>

<section class="mb-6 overflow-x-auto rounded-lg bg-white shadow">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-sky-950 text-white"><tr><th class="px-4 py-3">Product</th><th class="px-4 py-3">SKU</th><th class="px-4 py-3">Ordered</th><th class="px-4 py-3">Received</th><th class="px-4 py-3">Outstanding</th><th class="px-4 py-3">Unit price</th><th class="px-4 py-3">Line total</th></tr></thead>
        <tbody class="divide-y">
        <?php $__currentLoopData = $purchaseOrder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr><td class="px-4 py-3"><?php echo e($item->product?->name ?? 'Deleted product'); ?></td><td class="px-4 py-3"><?php echo e($item->product?->sku ?? '—'); ?></td><td class="px-4 py-3"><?php echo e($item->quantity); ?></td><td class="px-4 py-3"><?php echo e($item->received_quantity); ?></td><td class="px-4 py-3"><?php echo e($item->outstanding_quantity); ?></td><td class="px-4 py-3"><?php echo e(number_format((float) $item->unit_price, 2)); ?></td><td class="px-4 py-3"><?php echo e(number_format($item->line_total, 2)); ?></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</section>

<section class="mb-6 rounded-lg bg-white p-5 shadow">
    <h2 class="mb-3 text-lg font-bold">Approval history</h2>
    <p class="text-sm text-slate-700">Created by <?php echo e($purchaseOrder->createdBy?->name ?? 'System'); ?> on <?php echo e($purchaseOrder->created_at->format('M d, Y H:i')); ?></p>
    <?php if($purchaseOrder->approved_at): ?><p class="mt-1 text-sm text-slate-700">Approved by <?php echo e($purchaseOrder->approvedBy?->name ?? 'User removed'); ?> on <?php echo e($purchaseOrder->approved_at->format('M d, Y H:i')); ?></p><?php endif; ?>
    <?php if($purchaseOrder->rejection_reason): ?><p class="mt-2 rounded bg-red-50 p-3 text-sm text-red-800">Rejection reason: <?php echo e($purchaseOrder->rejection_reason); ?></p><?php endif; ?>
    <?php if($purchaseOrder->notes): ?><p class="mt-3 whitespace-pre-line text-sm text-slate-600"><?php echo e($purchaseOrder->notes); ?></p><?php endif; ?>
</section>

<div class="flex flex-wrap gap-3">
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $purchaseOrder)): ?>
        <a href="<?php echo e(route('purchase-orders.edit', $purchaseOrder)); ?>" class="rounded border px-4 py-2 text-sm font-semibold">Edit draft</a>
    <?php endif; ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $purchaseOrder)): ?>
        <form method="POST" action="<?php echo e(route('purchase-orders.destroy', $purchaseOrder)); ?>" onsubmit="return confirm('Archive this draft?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="rounded border border-red-300 px-4 py-2 text-sm text-red-700">Archive</button></form>
    <?php endif; ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('approve', $purchaseOrder)): ?>
        <form method="POST" action="<?php echo e(route('purchase-orders.approve', $purchaseOrder)); ?>"><?php echo csrf_field(); ?><button class="rounded bg-emerald-700 px-4 py-2 text-sm font-semibold text-white">Approve</button></form>
        <form method="POST" action="<?php echo e(route('purchase-orders.reject', $purchaseOrder)); ?>" class="flex gap-2"><?php echo csrf_field(); ?><input name="reason" required maxlength="2000" placeholder="Rejection reason" class="rounded border px-3 py-2 text-sm"><button class="rounded bg-red-700 px-4 py-2 text-sm font-semibold text-white">Reject</button></form>
    <?php endif; ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('send', $purchaseOrder)): ?>
        <form method="POST" action="<?php echo e(route('purchase-orders.send', $purchaseOrder)); ?>"><?php echo csrf_field(); ?><button class="rounded bg-sky-700 px-4 py-2 text-sm font-semibold text-white">Mark sent</button></form>
    <?php endif; ?>
</div>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('receive', $purchaseOrder)): ?>
<form method="POST" action="<?php echo e(route('purchase-orders.receive', $purchaseOrder)); ?>" class="mt-6 rounded-lg bg-white p-5 shadow">
    <?php echo csrf_field(); ?>
    <h2 class="mb-3 text-lg font-bold">Receive items</h2>
    <div class="space-y-3">
        <?php $__currentLoopData = $purchaseOrder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="grid items-center gap-2 text-sm md:grid-cols-[2fr_1fr_1fr]">
                <label for="received-<?php echo e($item->id); ?>"><?php echo e($item->product?->name ?? 'Deleted product'); ?> (<?php echo e($item->product?->sku); ?>)</label>
                <span>Outstanding: <?php echo e($item->outstanding_quantity); ?></span>
                <input id="received-<?php echo e($item->id); ?>" type="number" name="items[<?php echo e($item->id); ?>][quantity]" min="0" max="<?php echo e($item->outstanding_quantity); ?>" value="0" class="rounded border px-3 py-2">
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="mt-3 flex flex-wrap gap-3">
        <input name="notes" maxlength="500" placeholder="Receipt notes (optional)" class="min-w-60 flex-1 rounded border px-3 py-2 text-sm">
        <button class="rounded bg-emerald-700 px-4 py-2 text-sm font-semibold text-white">Record receipt</button>
    </div>
</form>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/purchase-orders/show.blade.php ENDPATH**/ ?>