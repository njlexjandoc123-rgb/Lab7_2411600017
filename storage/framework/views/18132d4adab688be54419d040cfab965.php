

<?php $__env->startSection('content'); ?>
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="text-3xl font-bold text-slate-800">Purchase orders</h1><p class="mt-1 text-sm text-slate-500">Track drafts, approvals, delivery, and receipt.</p></div>
    <div class="flex gap-2">
        <a href="<?php echo e(route('exports.download', ['type' => 'purchase-orders', 'format' => 'csv'])); ?>" class="rounded border px-3 py-2 text-sm">Export CSV</a>
        <a href="<?php echo e(route('exports.download', ['type' => 'purchase-orders', 'format' => 'pdf'])); ?>" class="rounded border px-3 py-2 text-sm">Export PDF</a>
        <a href="<?php echo e(route('purchase-orders.create')); ?>" class="rounded bg-sky-700 px-4 py-2 text-sm font-semibold text-white">+ New order</a>
    </div>
</div>

<?php if (isset($component)) { $__componentOriginal934f921620666b609fa7806109faa21b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal934f921620666b609fa7806109faa21b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-form','data' => ['action' => route('purchase-orders.index'),'class' => 'mb-5 grid gap-3 rounded-lg bg-white p-4 shadow md:grid-cols-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('purchase-orders.index')),'class' => 'mb-5 grid gap-3 rounded-lg bg-white p-4 shadow md:grid-cols-6']); ?>
    <input name="q" value="<?php echo e(request('q')); ?>" placeholder="Reference or supplier" class="rounded border px-3 py-2 text-sm md:col-span-2">
    <select name="status" class="rounded border px-3 py-2 text-sm"><option value="">All statuses</option><?php $__currentLoopData = App\Models\PurchaseOrder::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($status); ?>" <?php if(request('status') === $status): echo 'selected'; endif; ?>><?php echo e(ucfirst($status)); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
    <select name="supplier_id" class="rounded border px-3 py-2 text-sm"><option value="">All suppliers</option><?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($supplier->id); ?>" <?php if(request('supplier_id') == $supplier->id): echo 'selected'; endif; ?>><?php echo e($supplier->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
    <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" class="rounded border px-3 py-2 text-sm">
    <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" class="rounded border px-3 py-2 text-sm">
    <button class="rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white md:col-span-6">Filter orders</button>
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

<div class="overflow-x-auto rounded-lg bg-white shadow">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-sky-950 text-white"><tr><th class="px-4 py-3">Reference</th><th class="px-4 py-3">Supplier</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Created by</th><th class="px-4 py-3">Items</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Status</th></tr></thead>
        <tbody class="divide-y">
        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="px-4 py-3 font-semibold"><a class="text-sky-700 hover:underline" href="<?php echo e(route('purchase-orders.show', $order)); ?>"><?php echo e($order->reference_number); ?></a></td>
                <td class="px-4 py-3"><?php echo e($order->supplier?->name ?? 'Archived supplier'); ?></td>
                <td class="px-4 py-3"><?php echo e($order->order_date->format('M d, Y')); ?></td>
                <td class="px-4 py-3"><?php echo e($order->createdBy?->name ?? 'System'); ?></td>
                <td class="px-4 py-3"><?php echo e($order->items->count()); ?></td>
                <td class="px-4 py-3"><?php echo e(number_format((float) $order->total_amount, 2)); ?></td>
                <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e(match($order->status) { 'received' => 'bg-emerald-100 text-emerald-800', 'rejected', 'cancelled' => 'bg-red-100 text-red-800', 'pending' => 'bg-amber-100 text-amber-800', default => 'bg-slate-100 text-slate-700' }); ?>"><?php echo e(ucfirst($order->status)); ?></span></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">No purchase orders match these filters.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <div class="border-t bg-slate-50 p-4"><?php echo e($orders->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/purchase-orders/index.blade.php ENDPATH**/ ?>