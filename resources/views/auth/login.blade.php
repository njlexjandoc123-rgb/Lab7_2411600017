<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-sky-950 text-slate-800">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden bg-gradient-to-br from-sky-950 via-sky-800 to-blue-600 px-4 py-10">
        <div aria-hidden="true" class="absolute -left-24 -top-24 h-80 w-80 rounded-full border border-white/10"></div>
        <div aria-hidden="true" class="absolute -bottom-40 -right-24 h-[28rem] w-[28rem] rounded-full border border-white/10"></div>
        <section class="relative w-full max-w-md rounded-2xl border border-white/60 bg-white p-7 shadow-2xl shadow-sky-950/30 sm:p-9">
            <header class="mb-8 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-sky-700 text-white shadow-md shadow-sky-700/20" aria-hidden="true">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="m2.5 9 9.5-5 9.5 5-9.5 5-9.5-5Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M6.5 11.1v5.2c3.5 2.6 7.5 2.6 11 0v-5.2M21.5 9v6"/>
                    </svg>
                </div>
                <h1 class="mt-4 text-2xl font-bold tracking-tight text-sky-950">Student Portal</h1>
                <p class="mt-1 text-sm text-slate-500">Log in to continue to your account</p>
            </header>

            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700" role="alert">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-5 rounded-lg border border-sky-200 bg-sky-50 p-3 text-sm text-sky-800" role="status">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100">
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100">
                </div>

                <div class="flex justify-end">
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-sky-700 hover:text-sky-900 hover:underline">Forgot password?</a>
                </div>

                <button type="submit" class="w-full rounded-lg bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-200">
                    Log in
                </button>
            </form>

            <p class="mt-7 text-center text-xs text-slate-400">Student Portal</p>
        </section>
    </main>
</body>
</html>
