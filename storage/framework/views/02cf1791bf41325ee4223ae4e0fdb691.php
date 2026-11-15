

<?php $__env->startSection('content'); ?>
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Users</h1>
        <p class="text-sm text-gray-500 mt-1">Manage registered accounts in the student portal.</p>
    </div>
    <button type="submit" form="add-user-form" class="bg-[#0369a1] hover:bg-[#075985] text-white px-4 py-2 rounded shadow text-sm font-semibold">+ Add User</button>
</div>

<?php if(session('success')): ?>
    <div class="mb-4 rounded bg-emerald-100 px-4 py-3 text-sm text-emerald-800"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="mb-4 rounded bg-red-100 px-4 py-3 text-sm text-red-800"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<form id="add-user-form" action="<?php echo e(route('users.store')); ?>" method="POST" class="mb-6 grid grid-cols-1 gap-4 rounded-lg bg-white p-4 shadow md:grid-cols-2">
    <?php echo csrf_field(); ?>
    <div>
        <label for="user-name" class="mb-1 block text-xs font-semibold uppercase text-gray-600">Name</label>
        <input id="user-name" type="text" name="name" value="<?php echo e(old('name')); ?>" maxlength="255" required class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
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
        <label for="user-email" class="mb-1 block text-xs font-semibold uppercase text-gray-600">Email</label>
        <input id="user-email" type="email" name="email" value="<?php echo e(old('email')); ?>" maxlength="255" required class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
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
        <label for="user-password" class="mb-1 block text-xs font-semibold uppercase text-gray-600">Password</label>
        <input id="user-password" type="password" name="password" minlength="8" required autocomplete="new-password" class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label for="user-password-confirmation" class="mb-1 block text-xs font-semibold uppercase text-gray-600">Confirm password</label>
        <input id="user-password-confirmation" type="password" name="password_confirmation" minlength="8" required autocomplete="new-password" class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
    </div>
    <div>
        <label for="user-role" class="mb-1 block text-xs font-semibold uppercase text-gray-600">Role</label>
        <select id="user-role" name="role" required class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
            <?php $__currentLoopData = ['staff' => 'Staff', 'warehouse' => 'Warehouse staff', 'manager' => 'Manager', 'admin' => 'Administrator']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($value); ?>" <?php if(old('role', 'staff') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-sky-950 text-white text-xs uppercase tracking-wider">
                <th class="py-3 px-4">Name</th>
                <th class="py-3 px-4">Email</th>
                <th class="py-3 px-4">Role</th>
                <th class="py-3 px-4">Registered</th>
                <th class="py-3 px-4 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 text-sm">
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="py-3 px-4 font-semibold text-gray-800"><?php echo e($user->name); ?></td>
                <td class="py-3 px-4"><?php echo e($user->email); ?></td>
                <td class="py-3 px-4 capitalize"><?php echo e($user->role); ?></td>
                <td class="py-3 px-4"><?php echo e($user->created_at?->format('M d, Y') ?? '—'); ?></td>
                <td class="space-x-1 whitespace-nowrap py-3 px-4 text-center">
                    <a href="<?php echo e(route('users.edit', $user)); ?>" class="inline-block rounded bg-gray-100 px-2 py-1 text-xs hover:bg-gray-200">Edit</a>
                    <?php if(auth()->id() !== $user->id): ?>
                    <form action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" class="inline" onsubmit="return confirm('Delete this user account?');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="rounded bg-red-50 px-2 py-1 text-xs text-red-700 hover:bg-red-100">Delete</button>
                    </form>
                    <?php else: ?>
                    <span class="text-xs text-gray-400">Current user</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="5" class="py-6 px-4 text-center text-gray-500">
                    No users found.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="px-4 py-3 bg-gray-50 border-t flex justify-between items-center text-xs text-gray-500">
        <span>
            <?php if($users->total() > 0): ?>
                Showing <?php echo e($users->firstItem()); ?>-<?php echo e($users->lastItem()); ?> of <?php echo e($users->total()); ?> users
            <?php else: ?>
                Showing 0 of 0 users
            <?php endif; ?>
        </span>
        <?php echo e($users->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/users/index.blade.php ENDPATH**/ ?>