<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium">Company name *</label>
        <input name="name" required maxlength="255" value="<?php echo e(old('name', $supplier->name)); ?>" class="w-full rounded border px-3 py-2">
        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Contact person</label>
        <input name="contact_person" maxlength="255" value="<?php echo e(old('contact_person', $supplier->contact_person)); ?>" class="w-full rounded border px-3 py-2">
        <?php $__errorArgs = ['contact_person'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Email</label>
        <input type="email" name="email" maxlength="255" value="<?php echo e(old('email', $supplier->email)); ?>" class="w-full rounded border px-3 py-2">
        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Phone</label>
        <input name="phone" maxlength="40" value="<?php echo e(old('phone', $supplier->phone)); ?>" class="w-full rounded border px-3 py-2">
        <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Payment terms *</label>
        <input name="payment_terms" required maxlength="100" value="<?php echo e(old('payment_terms', $supplier->payment_terms ?? 'COD')); ?>" placeholder="Net 30, COD" class="w-full rounded border px-3 py-2">
        <?php $__errorArgs = ['payment_terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Lead time (days) *</label>
        <input type="number" name="lead_time_days" required min="0" max="3650" value="<?php echo e(old('lead_time_days', $supplier->lead_time_days ?? 7)); ?>" class="w-full rounded border px-3 py-2">
        <?php $__errorArgs = ['lead_time_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Minimum order amount *</label>
        <input type="number" name="minimum_order_amount" required min="0" step="0.01" value="<?php echo e(old('minimum_order_amount', $supplier->minimum_order_amount ?? 0)); ?>" class="w-full rounded border px-3 py-2">
        <?php $__errorArgs = ['minimum_order_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Status</label>
        <input type="hidden" name="is_active" value="0">
        <label class="flex items-center gap-2 rounded border px-3 py-2">
            <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $supplier->is_active ?? true)): echo 'checked'; endif; ?>>
            <span>Active supplier</span>
        </label>
    </div>
    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium">Physical address</label>
        <textarea name="address" rows="3" maxlength="2000" class="w-full rounded border px-3 py-2"><?php echo e(old('address', $supplier->address)); ?></textarea>
        <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>
<div class="mt-6 flex justify-between">
    <a href="<?php echo e(route('suppliers.index')); ?>" class="rounded border px-4 py-2 text-sm">Cancel</a>
    <button class="rounded bg-sky-700 px-5 py-2 text-sm font-semibold text-white hover:bg-sky-800">Save supplier</button>
</div>
<?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/suppliers/_form.blade.php ENDPATH**/ ?>