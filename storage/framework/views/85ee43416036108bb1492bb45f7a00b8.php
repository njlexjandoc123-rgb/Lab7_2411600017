<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Student Portal</title>
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
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Forgot password?</h1>
            <p class="mt-3 text-sm leading-6 text-slate-500">Enter your account email and we’ll send you a link to reset your password.</p>

            <?php if(session('status')): ?>
                <div class="mt-6 rounded-xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-800" role="status">
                    <?php echo e(session('status')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p><?php echo e($error); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('password.email')); ?>" class="mt-7 space-y-5">
                <?php echo csrf_field(); ?>
                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email address</label>
                    <input id="email" type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus autocomplete="username"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-sky-600 focus:bg-white focus:ring-4 focus:ring-sky-100">
                </div>
                <button type="submit" class="w-full rounded-xl bg-sky-700 px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-sky-700/20 transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-200">
                    Send password reset link
                </button>
            </form>

            <a href="<?php echo e(route('login')); ?>" class="mt-6 block text-center text-sm font-semibold text-sky-700 hover:text-sky-900 hover:underline">Back to login</a>
        </section>
    </main>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Lab7_2411600017_Jandoc\resources\views/auth/forgot-password.blade.php ENDPATH**/ ?>