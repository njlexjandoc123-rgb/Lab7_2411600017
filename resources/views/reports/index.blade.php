@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Inventory Reports</h1>
        <p class="mt-1 text-sm text-gray-500">Review stock movement, low stock items, and inventory value.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('reports.export', request()->all()) }}" class="rounded bg-sky-700 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-800">Export CSV</a>
        <a href="{{ route('exports.download', ['type' => 'transactions', 'format' => 'pdf'] + request()->only(['start_date', 'end_date'])) }}" class="rounded border px-4 py-2 text-sm">Export PDF</a>
    </div>
</div>

<form method="GET" class="mb-6 grid gap-3 rounded-lg bg-white p-4 shadow md:grid-cols-4">
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">Start Date</label>
        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded border px-3 py-2 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">End Date</label>
        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded border px-3 py-2 text-sm">
    </div>
    <div class="flex items-end md:col-span-2">
        <button type="submit" class="w-full rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">Generate Report</button>
    </div>
</form>

<div class="mb-6 grid gap-4 md:grid-cols-3">
    <div class="rounded-lg bg-white p-5 shadow">
        <p class="text-xs font-semibold uppercase text-slate-500">Movement Report</p>
        <p class="mt-3 text-2xl font-bold text-slate-800">{{ $transactions->count() }}</p>
    </div>
    <div class="rounded-lg bg-white p-5 shadow">
        <p class="text-xs font-semibold uppercase text-slate-500">Low Stock Count</p>
        <p class="mt-3 text-2xl font-bold text-amber-600">{{ $lowStockProducts->count() }}</p>
    </div>
    <div class="rounded-lg bg-white p-5 shadow">
        <p class="text-xs font-semibold uppercase text-slate-500">Inventory Value</p>
        <p class="mt-3 text-2xl font-bold text-emerald-600">₱{{ number_format($inventoryValue, 2) }}</p>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-2">
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <div class="bg-sky-950 px-4 py-3 text-sm font-semibold text-white">Stock Movement Report</div>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Product</th>
                    <th class="px-4 py-3 text-left">Type</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($transactions as $transaction)
                    <tr>
                        <td class="px-4 py-3">{{ $transaction->created_at->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">{{ $transaction->product?->name ?? 'Product deleted' }}</td>
                        <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $transaction->transaction_type) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">No transactions found for the selected range.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="overflow-hidden rounded-lg bg-white shadow">
        <div class="bg-amber-500 px-4 py-3 text-sm font-semibold text-white">Low Stock Report</div>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th class="px-4 py-3 text-left">Product</th>
                    <th class="px-4 py-3 text-left">Qty</th>
                    <th class="px-4 py-3 text-left">Reorder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($lowStockProducts as $product)
                    <tr>
                        <td class="px-4 py-3">{{ $product->name }}</td>
                        <td class="px-4 py-3">{{ $product->qty }}</td>
                        <td class="px-4 py-3">{{ $product->reorder_level }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">No low stock products.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
