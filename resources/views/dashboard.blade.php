@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Inventory Dashboard</h1>
        <p class="mt-1 text-sm text-gray-500">A live overview of your student portal inventory.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('adjustments.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-emerald-700">+ Adjustment</a>
        <a href="{{ route('products.create') }}" class="rounded-lg bg-sky-700 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-sky-800">+ Add Product</a>
    </div>
</div>

<div class="mb-2 flex justify-end">
    <p id="last-updated" class="text-xs text-gray-500">Last updated: --</p>
</div>

<div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
    <a href="{{ route('products.index') }}" class="flex items-center justify-between rounded-xl bg-sky-700 p-6 text-white shadow transition hover:bg-sky-800">
        <div>
            <p class="text-xs font-semibold uppercase">Total Products</p>
            <p id="total-products" class="mt-1 text-3xl font-bold">{{ number_format($totalProducts) }}</p>
        </div>
        <span class="text-3xl" aria-hidden="true">📦</span>
    </a>
    <a href="{{ route('products.index') }}" class="flex items-center justify-between rounded-xl bg-amber-500 p-6 text-white shadow transition hover:bg-amber-600">
        <div>
            <p class="text-xs font-semibold uppercase">Low Stock</p>
            <p id="low-stock-count" class="mt-1 text-3xl font-bold">{{ number_format($lowStockItems) }}</p>
        </div>
        <span class="text-3xl" aria-hidden="true">⚠️</span>
    </a>
    <a href="{{ route('products.index') }}" class="flex items-center justify-between rounded-xl bg-red-600 p-6 text-white shadow transition hover:bg-red-700">
        <div>
            <p class="text-xs font-semibold uppercase">Out of Stock</p>
            <p id="out-of-stock-count" class="mt-1 text-3xl font-bold">{{ number_format($outOfStockItems) }}</p>
        </div>
        <span class="text-3xl" aria-hidden="true">✕</span>
    </a>
    <div class="flex items-center justify-between rounded-xl bg-sky-950 p-6 text-white shadow">
        <div>
            <p class="text-xs font-semibold uppercase">Inventory Value</p>
            <p id="inventory-value" class="mt-1 text-3xl font-bold">₱{{ number_format((float) $totalInventoryValue, 2) }}</p>
        </div>
        <span class="text-3xl" aria-hidden="true">💰</span>
    </div>
</div>

<div class="mb-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
    <section class="rounded-xl bg-white p-6 shadow">
        <div class="mb-4 flex items-center justify-between border-b border-slate-200 pb-3">
            <h2 class="font-bold text-slate-800">Recent Products</h2>
            <a href="{{ route('products.index') }}" class="rounded-lg bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-800 hover:bg-sky-100">View All</a>
        </div>
        <ul class="divide-y divide-slate-100">
            @forelse($recentProducts as $product)
            <li class="flex items-center justify-between gap-4 py-3">
                <div class="min-w-0">
                    <a href="{{ route('products.edit', $product) }}" class="block truncate text-sm font-semibold text-slate-800 hover:text-sky-700">{{ $product->name }}</a>
                    <p class="mt-1 text-xs text-slate-500">{{ $product->sku }} · {{ $product->category ?: 'Uncategorized' }}</p>
                </div>
                <span class="shrink-0 rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ number_format($product->qty) }} pcs</span>
            </li>
            @empty
            <li class="py-6 text-center text-sm text-slate-500">No products yet. Add a product to get started.</li>
            @endforelse
        </ul>
    </section>

    <section class="rounded-xl bg-white p-6 shadow">
        <div class="mb-4 flex items-center justify-between border-b border-slate-200 pb-3">
            <h2 class="font-bold text-slate-800">Low Stock Alerts</h2>
            <span id="low-stock-label" class="rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800">{{ number_format($lowStockItems) }} items</span>
        </div>
        <ul id="low-stock-list" class="divide-y divide-slate-100">
            @forelse($lowStockProducts as $product)
            <li class="flex items-center justify-between gap-4 py-3">
                <div class="min-w-0">
                    <a href="{{ route('products.edit', $product) }}" class="block truncate text-sm font-semibold text-slate-800 hover:text-sky-700">{{ $product->name }}</a>
                    <p class="mt-1 text-xs text-slate-500">{{ $product->sku }}</p>
                </div>
                <span class="shrink-0 rounded-lg bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-900">{{ number_format($product->qty) }} left</span>
            </li>
            @empty
            <li class="py-6 text-center text-sm text-slate-500">No low-stock products.</li>
            @endforelse
        </ul>
    </section>
</div>

<section class="mb-8">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div><h2 class="text-xl font-bold text-slate-800">Procurement</h2><p class="text-sm text-slate-500">Supplier replenishment and purchase-order workflow.</p></div>
        <div class="flex gap-2">
            <a href="{{ route('suppliers.index') }}" class="rounded border px-3 py-2 text-sm font-semibold">Suppliers</a>
            <a href="{{ route('purchase-orders.index') }}" class="rounded border px-3 py-2 text-sm font-semibold">Purchase orders</a>
        </div>
    </div>
    <div class="grid gap-4 lg:grid-cols-2">
        @include('widgets.pending-orders')
        @include('widgets.recent-orders')
        @include('widgets.supplier-performance')
        @include('widgets.reorder-alerts')
    </div>
</section>

<div class="mb-8 rounded-xl bg-white p-6 shadow">
    <div class="mb-4 flex items-center justify-between border-b border-slate-200 pb-3">
        <h2 class="font-bold text-slate-800">Recent Transactions</h2>
        <a href="{{ route('transactions.index') }}" class="text-sm font-semibold text-sky-700 hover:text-sky-800">View all</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-slate-100 text-slate-700">
                <tr>
                    <th class="px-3 py-2">Product</th>
                    <th class="px-3 py-2">Type</th>
                    <th class="px-3 py-2">Qty</th>
                    <th class="px-3 py-2">User</th>
                    <th class="px-3 py-2">Date</th>
                </tr>
            </thead>
            <tbody id="recent-transactions" class="divide-y divide-slate-100">
                @forelse(App\Models\InventoryTransaction::query()->with(['product','user'])->latest('created_at')->take(5)->get() as $transaction)
                    <tr>
                        <td class="px-3 py-2">{{ $transaction->product?->name ?? 'N/A' }}</td>
                        <td class="px-3 py-2 capitalize">{{ str_replace('_', ' ', $transaction->transaction_type) }}</td>
                        <td class="px-3 py-2">{{ $transaction->quantity }}</td>
                        <td class="px-3 py-2">{{ $transaction->user?->name ?? 'System' }}</td>
                        <td class="px-3 py-2">{{ $transaction->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-3 py-4 text-center text-slate-500">No recent transactions.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
    <section class="overflow-hidden rounded-xl bg-white shadow">
        <div class="flex items-center justify-between border-b border-slate-200 p-6">
            <h2 class="font-bold text-slate-800">Brands</h2>
            <a href="{{ route('brands.index') }}" class="rounded-lg bg-sky-700 px-2.5 py-1 text-xs font-semibold text-white hover:bg-sky-800">{{ number_format($totalBrands) }} total</a>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-sky-950 text-xs uppercase text-white">
                <tr>
                    <th class="px-6 py-3">Brand Name</th>
                    <th class="px-6 py-3 text-right">Products</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($brands as $brand)
                <tr>
                    <td class="px-6 py-3 font-medium text-slate-800">{{ $brand->name }}</td>
                    <td class="px-6 py-3 text-right">{{ number_format($brand->products_count) }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="px-6 py-6 text-center text-slate-500">No brands yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="overflow-hidden rounded-xl bg-white shadow">
        <div class="flex items-center justify-between border-b border-slate-200 p-6">
            <h2 class="font-bold text-slate-800">Categories</h2>
            <a href="{{ route('categories.index') }}" class="rounded-lg bg-sky-700 px-2.5 py-1 text-xs font-semibold text-white hover:bg-sky-800">{{ number_format($totalCategories) }} total</a>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-sky-950 text-xs uppercase text-white">
                <tr>
                    <th class="px-6 py-3">Category Name</th>
                    <th class="px-6 py-3 text-right">Products</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($categories as $category)
                <tr>
                    <td class="px-6 py-3 font-medium text-slate-800">{{ $category->name }}</td>
                    <td class="px-6 py-3 text-right">{{ number_format($category->products_count) }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="px-6 py-6 text-center text-slate-500">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>

<script>
    const totalProductsEl = document.getElementById('total-products');
    const lowStockCountEl = document.getElementById('low-stock-count');
    const outOfStockCountEl = document.getElementById('out-of-stock-count');
    const inventoryValueEl = document.getElementById('inventory-value');
    const lowStockListEl = document.getElementById('low-stock-list');
    const recentTransactionsEl = document.getElementById('recent-transactions');
    const lastUpdatedEl = document.getElementById('last-updated');
    const lowStockLabelEl = document.getElementById('low-stock-label');

    async function refreshDashboard() {
        try {
            const [statsRes, lowRes, recentRes] = await Promise.all([
                fetch('{{ route('api.dashboard.stats') }}'),
                fetch('{{ route('api.products.low-stock') }}'),
                fetch('{{ route('api.transactions.recent') }}'),
            ]);

            if (!statsRes.ok || !lowRes.ok || !recentRes.ok) {
                throw new Error('Unable to load dashboard data.');
            }

            const stats = await statsRes.json();
            const lowStockItems = await lowRes.json();
            const recentTransactions = await recentRes.json();

            if (totalProductsEl) totalProductsEl.textContent = Number(stats.total_products).toLocaleString();
            if (lowStockCountEl) lowStockCountEl.textContent = Number(stats.low_stock_items).toLocaleString();
            if (outOfStockCountEl) outOfStockCountEl.textContent = Number(stats.out_of_stock_items).toLocaleString();
            if (inventoryValueEl) inventoryValueEl.textContent = '₱' + Number(stats.inventory_value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            if (lastUpdatedEl) lastUpdatedEl.textContent = 'Last updated: ' + new Date(stats.last_updated).toLocaleString();
            if (lowStockLabelEl) lowStockLabelEl.textContent = Number(stats.low_stock_items).toLocaleString() + ' items';

            if (lowStockListEl) {
                if (!lowStockItems.length) {
                    lowStockListEl.innerHTML = '<li class="py-6 text-center text-sm text-slate-500">No low-stock products.</li>';
                } else {
                    lowStockListEl.innerHTML = lowStockItems.map(item => `
                        <li class="flex items-center justify-between gap-4 py-3">
                            <div class="min-w-0">
                                <a href="/products/${item.id}/edit" class="block truncate text-sm font-semibold text-slate-800 hover:text-sky-700">${item.name}</a>
                                <p class="mt-1 text-xs text-slate-500">${item.sku}</p>
                            </div>
                            <span class="shrink-0 rounded-lg bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-900">${Number(item.qty).toLocaleString()} left</span>
                        </li>
                    `).join('');
                }
            }

            if (recentTransactionsEl) {
                if (!recentTransactions.length) {
                    recentTransactionsEl.innerHTML = '<tr><td colspan="5" class="px-3 py-4 text-center text-slate-500">No recent transactions.</td></tr>';
                } else {
                    recentTransactionsEl.innerHTML = recentTransactions.map(item => `
                        <tr>
                            <td class="px-3 py-2">${item.product || 'N/A'}</td>
                            <td class="px-3 py-2 capitalize">${item.type ? item.type.replace('_', ' ') : ''}</td>
                            <td class="px-3 py-2">${item.quantity}</td>
                            <td class="px-3 py-2">${item.user || 'System'}</td>
                            <td class="px-3 py-2">${new Date(item.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</td>
                        </tr>
                    `).join('');
                }
            }
        } catch (error) {
            console.error(error);
        }
    }

    refreshDashboard();
    setInterval(refreshDashboard, 30000);
</script>
@endsection
