

<?php $__env->startSection('content'); ?>
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-3xl font-bold text-slate-800">Suppliers</h1>
        <p class="mt-1 text-sm text-slate-500">Manage supplier contacts, terms, and performance.</p>
    </div>
    <div class="flex gap-2">
        <a href="<?php echo e(route('supplier-performance.index')); ?>" class="rounded border border-sky-700 px-4 py-2 text-sm font-semibold text-sky-800">Performance</a>
        <a href="<?php echo e(route('suppliers.create')); ?>" class="rounded bg-sky-700 px-4 py-2 text-sm font-semibold text-white">+ Add Supplier</a>
    </div>
</div>

<?php if (isset($component)) { $__componentOriginal934f921620666b609fa7806109faa21b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal934f921620666b609fa7806109faa21b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-form','data' => ['action' => route('suppliers.index'),'class' => 'mb-5 grid gap-3 rounded-lg bg-white p-4 shadow md:grid-cols-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('suppliers.index')),'class' => 'mb-5 grid gap-3 rounded-lg bg-white p-4 shadow md:grid-cols-5']); ?>
    <input name="q" value="<?php echo e(request('q')); ?>" placeholder="Name, contact, email" class="rounded border px-3 py-2 text-sm md:col-span-2">
    <select name="status" class="rounded border px-3 py-2 text-sm">
        <option value="">All statuses</option>
        <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>Active</option>
        <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>Inactive</option>
        <option value="archived" <?php if(request('status') === 'archived'): echo 'selected'; endif; ?>>Archived</option>
    </select>
    <select name="sort" class="rounded border px-3 py-2 text-sm">
        <option value="name" <?php if(request('sort', 'name') === 'name'): echo 'selected'; endif; ?>>Sort: name</option>
        <option value="lead_time_days" <?php if(request('sort') === 'lead_time_days'): echo 'selected'; endif; ?>>Sort: lead time</option>
        <option value="created_at" <?php if(request('sort') === 'created_at'): echo 'selected'; endif; ?>>Sort: newest</option>
    </select>
    <button class="rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white">Filter</button>
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
        <thead class="bg-sky-950 text-white"><tr>
            <th class="px-4 py-3">Supplier</th><th class="px-4 py-3">Contact</th><th class="px-4 py-3">Lead time</th>
            <th class="px-4 py-3">Products / Orders</th><th class="px-4 py-3">On-time</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Actions</th>
        </tr></thead>
        <tbody class="divide-y">
        <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="px-4 py-3"><a class="font-semibold text-sky-800 hover:underline" href="<?php echo e(route('suppliers.show', $supplier)); ?>"><?php echo e($supplier->name); ?></a><div class="text-xs text-slate-500"><?php echo e($supplier->email); ?></div></td>
                <td class="px-4 py-3"><?php echo e($supplier->contact_person ?: '—'); ?><div class="text-xs text-slate-500"><?php echo e($supplier->phone); ?></div></td>
                <td class="px-4 py-3"><?php echo e($supplier->lead_time_days); ?> days</td>
                <td class="px-4 py-3"><?php echo e($supplier->products_count); ?> / <?php echo e($supplier->purchase_orders_count); ?></td>
                <td class="px-4 py-3"><?php echo e($metrics[$supplier->id]['on_time_percentage'] ?? 0); ?>%</td>
                <td class="px-4 py-3"><span class="rounded-full px-2 py-1 text-xs font-semibold <?php echo e($supplier->trashed() ? 'bg-red-100 text-red-800' : ($supplier->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600')); ?>"><?php echo e($supplier->trashed() ? 'Archived' : ($supplier->is_active ? 'Active' : 'Inactive')); ?></span></td>
                <td class="whitespace-nowrap px-4 py-3">
                    <?php if($supplier->trashed()): ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('restore', App\Models\Supplier::class)): ?>
                            <form class="inline" method="POST" action="<?php echo e(route('suppliers.restore', $supplier->id)); ?>"><?php echo csrf_field(); ?><button class="text-emerald-700 hover:underline">Restore</button></form>
                        <?php endif; ?>
                    <?php else: ?>
                        <a class="mr-2 text-sky-700 hover:underline" href="<?php echo e(route('suppliers.show', $supplier)); ?>">View</a>
                        <a class="mr-2 text-slate-700 hover:underline" href="<?php echo e(route('suppliers.edit', $supplier)); ?>">Edit</a>
                        <form class="inline" method="POST" action="<?php echo e(route('suppliers.destroy', $supplier)); ?>" onsubmit="return confirm('Archive this supplier?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="text-red-700 hover:underline">Archive</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">No suppliers match your filters.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <div class="border-t bg-slate-50 p-4"><?php echo e($suppliers->links()); ?></div>
</div>
<div class="mt-4 flex gap-2">
    <a href="<?php echo e(route('exports.download', ['type' => 'suppliers', 'format' => 'csv'])); ?>" class="rounded border px-3 py-2 text-sm">Export suppliers CSV</a>
    <a href="<?php echo e(route('exports.download', ['type' => 'suppliers', 'format' => 'pdf'])); ?>" class="rounded border px-3 py-2 text-sm">Export suppliers PDF</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/suppliers/index.blade.php ENDPATH**/ ?>