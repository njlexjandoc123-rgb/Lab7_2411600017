<?php
    $purchaseOrder = $purchaseOrder ?? null;
    $editing = isset($purchaseOrder);
    $selectedSupplier = old('supplier_id', $purchaseOrder->supplier_id ?? '');
    $formItems = old('items', $editing
        ? $purchaseOrder->items->map(fn ($item) => ['product_id' => $item->product_id, 'quantity' => $item->quantity, 'unit_price' => $item->unit_price])->all()
        : [['product_id' => '', 'quantity' => 1, 'unit_price' => '']]);
    $allProducts = $suppliers->flatMap->products;
?>

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium">Supplier *</label>
        <select id="supplier-select" name="supplier_id" required class="w-full rounded border px-3 py-2">
            <option value="">Select an active supplier</option>
            <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($supplier->id); ?>" <?php if((string) $selectedSupplier === (string) $supplier->id): echo 'selected'; endif; ?>><?php echo e($supplier->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['supplier_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <?php if(!$editing): ?>
        <div>
            <label class="mb-1 block text-sm font-medium">Initial status *</label>
            <select name="status" required class="w-full rounded border px-3 py-2">
                <option value="draft" <?php if(old('status', 'draft') === 'draft'): echo 'selected'; endif; ?>>Draft</option>
                <option value="pending" <?php if(old('status') === 'pending'): echo 'selected'; endif; ?>>Submit for approval</option>
            </select>
        </div>
    <?php endif; ?>
    <div>
        <label class="mb-1 block text-sm font-medium">Order date *</label>
        <input type="date" name="order_date" required value="<?php echo e(old('order_date', $purchaseOrder?->order_date?->format('Y-m-d') ?? today()->toDateString())); ?>" class="w-full rounded border px-3 py-2">
        <?php $__errorArgs = ['order_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Expected delivery date</label>
        <input type="date" name="expected_delivery_date" value="<?php echo e(old('expected_delivery_date', $purchaseOrder?->expected_delivery_date?->format('Y-m-d'))); ?>" class="w-full rounded border px-3 py-2">
        <?php $__errorArgs = ['expected_delivery_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>

<div class="mt-6 rounded-lg border">
    <div class="flex items-center justify-between border-b bg-slate-50 p-3">
        <h2 class="font-semibold">Line items</h2>
        <button type="button" id="add-order-item" class="rounded border px-3 py-1.5 text-sm">+ Add item</button>
    </div>
    <div id="order-items" class="space-y-3 p-3">
        <?php $__currentLoopData = $formItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="order-item grid gap-2 md:grid-cols-[minmax(0,2fr)_1fr_1fr_auto]">
                <select name="items[<?php echo e($index); ?>][product_id]" required class="product-select rounded border px-3 py-2">
                    <option value="">Select product</option>
                    <?php $__currentLoopData = $allProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($product->id); ?>" data-supplier="<?php echo e($product->supplier_id); ?>" data-price="<?php echo e($product->unit_price); ?>" <?php if((string) ($item['product_id'] ?? '') === (string) $product->id): echo 'selected'; endif; ?>><?php echo e($product->name); ?> (<?php echo e($product->sku); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <input type="number" name="items[<?php echo e($index); ?>][quantity]" required min="1" value="<?php echo e($item['quantity'] ?? 1); ?>" placeholder="Quantity" class="rounded border px-3 py-2">
                <input type="number" name="items[<?php echo e($index); ?>][unit_price]" required min="0" step="0.01" value="<?php echo e($item['unit_price'] ?? ''); ?>" placeholder="Unit price" class="price-input rounded border px-3 py-2">
                <button type="button" class="remove-item rounded border px-3 py-2 text-red-700">Remove</button>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <p class="px-3 pb-3 text-xs text-slate-500">The server uses the saved product price when calculating totals.</p>
    <?php $__errorArgs = ['items'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="px-3 pb-3 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

<div class="mt-4 grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium">Tax amount</label>
        <input type="number" name="tax_amount" min="0" step="0.01" value="<?php echo e(old('tax_amount', $purchaseOrder->tax_amount ?? 0)); ?>" class="w-full rounded border px-3 py-2">
        <?php $__errorArgs = ['tax_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Notes / terms</label>
        <textarea name="notes" rows="2" maxlength="5000" class="w-full rounded border px-3 py-2"><?php echo e(old('notes', $purchaseOrder->notes ?? '')); ?></textarea>
    </div>
</div>

<template id="order-item-template">
    <div class="order-item grid gap-2 md:grid-cols-[minmax(0,2fr)_1fr_1fr_auto]">
        <select required class="product-select rounded border px-3 py-2">
            <option value="">Select product</option>
            <?php $__currentLoopData = $allProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($product->id); ?>" data-supplier="<?php echo e($product->supplier_id); ?>" data-price="<?php echo e($product->unit_price); ?>"><?php echo e($product->name); ?> (<?php echo e($product->sku); ?>)</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <input type="number" required min="1" value="1" placeholder="Quantity" class="rounded border px-3 py-2">
        <input type="number" required min="0" step="0.01" placeholder="Unit price" class="price-input rounded border px-3 py-2">
        <button type="button" class="remove-item rounded border px-3 py-2 text-red-700">Remove</button>
    </div>
</template>

<script>
(() => {
    const supplier = document.getElementById('supplier-select');
    const container = document.getElementById('order-items');
    const template = document.getElementById('order-item-template');
    if (!supplier || !container || !template) return;

    const configureRow = (row, index) => {
        const product = row.querySelector('.product-select');
        const quantity = row.querySelector('input[type="number"]:not(.price-input)');
        const price = row.querySelector('.price-input');
        product.name = `items[${index}][product_id]`;
        quantity.name = `items[${index}][quantity]`;
        price.name = `items[${index}][unit_price]`;
        Array.from(product.options).forEach(option => {
            if (option.value) option.hidden = option.dataset.supplier !== supplier.value;
        });
        if (product.selectedOptions[0]?.dataset.supplier !== supplier.value) product.value = '';
        product.addEventListener('change', () => {
            price.value = product.selectedOptions[0]?.dataset.price || '';
        });
        row.querySelector('.remove-item').addEventListener('click', () => {
            if (container.querySelectorAll('.order-item').length > 1) row.remove();
            Array.from(container.querySelectorAll('.order-item')).forEach((item, position) => configureNames(item, position));
        });
    };
    const configureNames = (row, index) => {
        row.querySelector('.product-select').name = `items[${index}][product_id]`;
        row.querySelector('input[type="number"]:not(.price-input)').name = `items[${index}][quantity]`;
        row.querySelector('.price-input').name = `items[${index}][unit_price]`;
    };

    Array.from(container.querySelectorAll('.order-item')).forEach((row, index) => configureRow(row, index));
    supplier.addEventListener('change', () => {
        Array.from(container.querySelectorAll('.order-item')).forEach(row => {
            const product = row.querySelector('.product-select');
            Array.from(product.options).forEach(option => {
                if (option.value) option.hidden = option.dataset.supplier !== supplier.value;
            });
            product.value = '';
            row.querySelector('.price-input').value = '';
        });
    });
    document.getElementById('add-order-item').addEventListener('click', () => {
        const row = template.content.firstElementChild.cloneNode(true);
        container.appendChild(row);
        configureRow(row, container.querySelectorAll('.order-item').length - 1);
    });
})();
</script>
<?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/purchase-orders/_form.blade.php ENDPATH**/ ?>