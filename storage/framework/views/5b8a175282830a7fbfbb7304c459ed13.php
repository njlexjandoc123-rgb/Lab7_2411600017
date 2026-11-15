

<?php $__env->startSection('content'); ?>
<div class="mx-auto max-w-xl rounded-lg bg-white p-8 shadow">
    <h1 class="mb-6 text-2xl font-bold text-gray-800">Stock Adjustment</h1>

    <form action="<?php echo e(route('adjustments.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div class="mb-4">
            <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">Product</label>
            <select name="product_id" id="product_id" required class="w-full rounded border px-3 py-2 text-sm">
                <option value="">Select product</option>
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($product->id); ?>" data-qty="<?php echo e($product->qty); ?>" <?php if(old('product_id') == $product->id): echo 'selected'; endif; ?>><?php echo e($product->name); ?> (Current: <?php echo e($product->qty); ?>)</option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="mb-4 rounded bg-slate-100 p-3 text-sm">
            <span class="font-semibold text-slate-700">Current system quantity:</span>
            <span id="current_qty" class="font-bold text-sky-700">0</span>
        </div>

        <div class="mb-4">
            <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">Physical Count</label>
            <input type="number" name="physical_count" id="physical_count" min="0" value="<?php echo e(old('physical_count')); ?>" required class="w-full rounded border px-3 py-2 text-sm">
        </div>

        <div class="mb-4">
            <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">Reason</label>
            <select name="reason" required class="w-full rounded border px-3 py-2 text-sm">
                <option value="">Select reason</option>
                <option value="Cycle count">Cycle count</option>
                <option value="Damaged goods">Damaged goods</option>
                <option value="Theft or loss">Theft or loss</option>
                <option value="System error">System error</option>
            </select>
        </div>

        <div class="mb-6">
            <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">Notes</label>
            <textarea name="notes" rows="3" class="w-full rounded border px-3 py-2 text-sm"><?php echo e(old('notes')); ?></textarea>
        </div>

        <div class="flex items-center justify-between">
            <a href="<?php echo e(route('dashboard')); ?>" class="text-sm text-gray-600 hover:underline">Cancel</a>
            <button type="submit" class="rounded bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Submit Adjustment</button>
        </div>
    </form>
</div>

<script>
    const productSelect = document.getElementById('product_id');
    const currentQty = document.getElementById('current_qty');
    const physicalCount = document.getElementById('physical_count');

    function updateCurrentQty() {
        const selected = productSelect.options[productSelect.selectedIndex];
        const qty = selected && selected.dataset.qty ? Number(selected.dataset.qty) : 0;
        currentQty.textContent = qty;
        if (! physicalCount.value) {
            physicalCount.value = qty;
        }
    }

    productSelect.addEventListener('change', updateCurrentQty);
    updateCurrentQty();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/adjustments/create.blade.php ENDPATH**/ ?>