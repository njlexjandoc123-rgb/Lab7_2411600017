

<?php $__env->startSection('content'); ?>
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Inventory Transactions</h1>
        <p class="mt-1 text-sm text-gray-500">Track stock movement and movement history.</p>
    </div>
    <a href="<?php echo e(route('transactions.create')); ?>" class="rounded bg-sky-700 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-800">+ Record Transaction</a>
</div>

<?php if (isset($component)) { $__componentOriginal934f921620666b609fa7806109faa21b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal934f921620666b609fa7806109faa21b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-form','data' => ['action' => route('transactions.index'),'class' => 'mb-6 grid gap-3 rounded-lg bg-white p-4 shadow md:grid-cols-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('transactions.index')),'class' => 'mb-6 grid gap-3 rounded-lg bg-white p-4 shadow md:grid-cols-6']); ?>
    <input name="q" value="<?php echo e(request('q')); ?>" placeholder="Product, SKU, or reference" class="w-full rounded border px-3 py-2 text-sm">
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">Product</label>
        <select name="product_id" class="w-full rounded border px-3 py-2 text-sm">
            <option value="">All products</option>
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($product->id); ?>" <?php if(request('product_id') == $product->id): echo 'selected'; endif; ?>><?php echo e($product->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">Type</label>
        <select name="transaction_type" class="w-full rounded border px-3 py-2 text-sm">
            <option value="">All</option>
            <option value="stock_in" <?php if(request('transaction_type') === 'stock_in'): echo 'selected'; endif; ?>>Stock In</option>
            <option value="stock_out" <?php if(request('transaction_type') === 'stock_out'): echo 'selected'; endif; ?>>Stock Out</option>
            <option value="adjustment" <?php if(request('transaction_type') === 'adjustment'): echo 'selected'; endif; ?>>Adjustment</option>
        </select>
    </div>
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">Start Date</label>
        <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" class="w-full rounded border px-3 py-2 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">End Date</label>
        <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" class="w-full rounded border px-3 py-2 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">User</label>
        <select name="user_id" class="w-full rounded border px-3 py-2 text-sm">
            <option value="">All users</option>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($user->id); ?>" <?php if(request('user_id') == $user->id): echo 'selected'; endif; ?>><?php echo e($user->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white">Filter</button>
        <a href="<?php echo e(route('exports.download', ['type' => 'transactions', 'format' => 'csv'] + request()->only(['start_date', 'end_date']))); ?>" class="rounded border px-3 py-2 text-sm">CSV</a>
        <a href="<?php echo e(route('exports.download', ['type' => 'transactions', 'format' => 'pdf'] + request()->only(['start_date', 'end_date']))); ?>" class="rounded border px-3 py-2 text-sm">PDF</a>
    </div>
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

<div class="overflow-hidden rounded-lg bg-white shadow">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-sky-950 text-white">
            <tr>
                <th class="px-4 py-3 text-left">Date</th>
                <th class="px-4 py-3 text-left">Product</th>
                <th class="px-4 py-3 text-left">Type</th>
                <th class="px-4 py-3 text-left">Qty</th>
                <th class="px-4 py-3 text-left">Reference</th>
                <th class="px-4 py-3 text-left">User</th>
                <th class="px-4 py-3 text-center">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="px-4 py-3"><?php echo e($transaction->created_at->format('Y-m-d H:i')); ?></td>
                    <td class="px-4 py-3 font-medium text-slate-800"><?php echo e($transaction->product?->name ?? 'Deleted product'); ?></td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($transaction->transaction_type === 'stock_out' ? 'bg-red-100 text-red-800' : ($transaction->transaction_type === 'stock_in' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800')); ?>">
                            <?php echo e(str_replace('_', ' ', $transaction->transaction_type)); ?>

                        </span>
                    </td>
                    <td class="px-4 py-3 font-semibold"><?php echo e($transaction->quantity); ?></td>
                    <td class="px-4 py-3"><?php echo e($transaction->reference_number ?? '—'); ?></td>
                    <td class="px-4 py-3"><?php echo e($transaction->user?->name ?? 'System'); ?></td>
                    <td class="px-4 py-3 text-center">
                        <a href="<?php echo e(route('transactions.show', $transaction)); ?>" class="text-sky-700 hover:underline">View</a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No inventory transactions found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="border-t bg-gray-50 px-4 py-3 text-sm text-gray-500">
        <?php echo e($transactions->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/transactions/index.blade.php ENDPATH**/ ?>