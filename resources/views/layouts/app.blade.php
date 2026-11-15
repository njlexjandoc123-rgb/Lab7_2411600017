<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    @php
        $lowStockProducts = \App\Models\Product::query()->lowStock()->orderBy('qty')->get();
    @endphp

    <nav class="bg-[#0369a1] text-white shadow-md">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="text-xl font-bold tracking-wide">🎓 Student Portal</span>
                </div>
                <div class="hidden items-center space-x-6 text-sm font-medium md:flex">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'border-b-2 border-white pb-1' : 'hover:text-gray-200' }}">Dashboard</a>
                    <a href="{{ url('/products') }}" class="{{ request()->is('products*') ? 'border-b-2 border-white pb-1' : 'hover:text-gray-200' }}">Products</a>
                    <a href="{{ route('transactions.index') }}" class="{{ request()->is('transactions*') ? 'border-b-2 border-white pb-1' : 'hover:text-gray-200' }}">Transactions</a>
                    <a href="{{ route('suppliers.index') }}" class="{{ request()->is('suppliers*') ? 'border-b-2 border-white pb-1' : 'hover:text-gray-200' }}">Suppliers</a>
                    <a href="{{ route('purchase-orders.index') }}" class="{{ request()->is('purchase-orders*') ? 'border-b-2 border-white pb-1' : 'hover:text-gray-200' }}">Purchase Orders</a>
                    <a href="{{ route('brands.index') }}" class="{{ request()->is('brands*') ? 'border-b-2 border-white pb-1' : 'hover:text-gray-200' }}">Brands</a>
                    <a href="{{ route('categories.index') }}" class="{{ request()->is('categories*') ? 'border-b-2 border-white pb-1' : 'hover:text-gray-200' }}">Categories</a>
                    @if(Auth::user()->hasRole('admin'))
                        <a href="{{ route('users.index') }}" class="{{ request()->is('users*') ? 'border-b-2 border-white pb-1' : 'hover:text-gray-200' }}">Users</a>
                    @endif
                    <a href="{{ route('reports.index') }}" class="{{ request()->is('reports*') ? 'border-b-2 border-white pb-1' : 'hover:text-gray-200' }}">Reports</a>
                    <a href="{{ route('adjustments.create') }}" class="{{ request()->is('adjustments*') ? 'border-b-2 border-white pb-1' : 'hover:text-gray-200' }}">Adjustments</a>

                    @auth
                        <div class="relative" id="notification-dropdown">
                            <button
                                type="button"
                                id="notification-toggle"
                                aria-label="Low-stock notifications"
                                aria-controls="notification-menu"
                                aria-expanded="false"
                                class="relative inline-flex items-center rounded-full border border-white/30 px-3 py-1.5 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/70"
                            >
                                🔔
                                @if($lowStockProducts->isNotEmpty())
                                    <span class="absolute -right-2 -top-2 rounded-full bg-red-500 px-1.5 text-[10px] font-bold text-white">{{ $lowStockProducts->count() }}</span>
                                @endif
                            </button>
                            <div id="notification-menu" hidden class="absolute right-0 z-50 mt-2 w-80 rounded-md bg-white p-3 text-gray-700 shadow-lg ring-1 ring-black/10">
                                <div class="mb-2 border-b border-gray-100 px-2 pb-2 text-sm font-semibold text-gray-800">Current low-stock products</div>
                                @forelse($lowStockProducts as $product)
                                    <a href="{{ route('products.edit', $product) }}" data-notification-product="{{ $product->sku }}" class="block rounded px-2 py-2 hover:bg-gray-100">
                                        <div class="text-sm font-semibold text-sky-700">{{ $product->name }}</div>
                                        <div class="mt-1 text-xs text-gray-600">{{ $product->sku }} · {{ $product->qty }} left · reorder at {{ $product->reorder_level }}</div>
                                    </a>
                                @empty
                                    <div class="px-2 py-3 text-sm text-gray-500">No products are currently low on stock.</div>
                                @endforelse
                            </div>
                        </div>
                    @endauth

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="cursor-pointer hover:text-gray-200">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul>
                    @foreach($errors->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
    <script>
        const notificationToggle = document.getElementById('notification-toggle');
        const notificationMenu = document.getElementById('notification-menu');
        const notificationDropdown = document.getElementById('notification-dropdown');

        if (notificationToggle && notificationMenu && notificationDropdown) {
            const closeNotifications = () => {
                notificationMenu.hidden = true;
                notificationToggle.setAttribute('aria-expanded', 'false');
            };

            notificationToggle.addEventListener('click', () => {
                const isOpen = !notificationMenu.hidden;
                notificationMenu.hidden = isOpen;
                notificationToggle.setAttribute('aria-expanded', String(!isOpen));
            });

            document.addEventListener('click', (event) => {
                if (!notificationDropdown.contains(event.target)) {
                    closeNotifications();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeNotifications();
                }
            });
        }
    </script>
</body>
</html>