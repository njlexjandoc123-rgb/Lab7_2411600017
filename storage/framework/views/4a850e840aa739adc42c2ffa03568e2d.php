<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Student Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">
    <main class="flex min-h-screen items-center justify-center bg-gradient-to-br from-sky-950 via-sky-800 to-blue-600 px-4 py-10">
        <section class="w-full max-w-md rounded-3xl bg-white p-8 shadow-2xl shadow-sky-950/30 sm:p-10">
            <a href="<?php echo e(route('login')); ?>" class="mb-8 inline-flex items-center gap-3 text-xl font-bold text-sky-950">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-100 text-sky-800" aria-hidden="true">🎓</span>
                Student Portal
            </a>

            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-sky-700">Account recovery</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Set a new password</h1>
            <p class="mt-3 text-sm leading-6 text-slate-500">Choose a new password for your account.</p>

            <?php if($errors->any()): ?>
                <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p><?php echo e($error); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('password.store')); ?>" class="mt-7 space-y-5">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="token" value="<?php echo e($request->route('token')); ?>">

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email address</label>
                    <input id="email" type="email" name="email" value="<?php echo e(old('email', $request->email)); ?>" required autofocus autocomplete="username"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-sky-600 focus:bg-white focus:ring-4 focus:ring-sky-100">
                </div>
                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">New password</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-sky-600 focus:bg-white focus:ring-4 focus:ring-sky-100">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">Confirm new password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-sky-600 focus:bg-white focus:ring-4 focus:ring-sky-100">
                </div>
                <button type="submit" class="w-full rounded-xl bg-sky-700 px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-sky-700/20 transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-200">
                    Reset password
                </button>
            </form>
        </section>
    </main>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/auth/reset-password.blade.php ENDPATH**/ ?>