@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">{{ $product->name }}</h1>
        <p class="mt-1 text-sm text-gray-500">SKU: {{ $product->sku }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('products.index') }}" class="rounded bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">Back</a>
        <a href="{{ route('products.edit', $product) }}" class="rounded bg-sky-700 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-800">Edit</a>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="rounded-lg bg-white p-6 shadow">
        <p class="text-xs font-semibold uppercase text-gray-500">Status</p>
        <span class="mt-3 inline-block rounded-full px-3 py-1 text-xs font-bold {{ $product->stockStatusClass() }}">
            {{ $product->stock_status }}
        </span>
        <div class="mt-5 space-y-3 text-sm text-gray-600">
            <div class="flex justify-between"><span>Brand</span><strong>{{ $product->brand ?? 'N/A' }}</strong></div>
            <div class="flex justify-between"><span>Category</span><strong>{{ $product->category ?? 'N/A' }}</strong></div>
            <div class="flex justify-between"><span>Current Qty</span><strong>{{ $product->qty }}</strong></div>
            <div class="flex justify-between"><span>Reorder Level</span><strong>{{ $product->reorder_level ?? 5 }}</strong></div>
            <div class="flex justify-between"><span>Unit Price</span><strong>₱{{ number_format($product->unit_price, 2) }}</strong></div>
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow lg:col-span-2">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">Transaction History</h2>
            <a href="{{ route('transactions.create') }}" class="rounded bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">New Transaction</a>
        </div>

        <form method="GET" class="mb-4 grid gap-3 md:grid-cols-3">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">Start date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">End date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded border px-3 py-2 text-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full rounded bg-sky-700 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-800">Filter</button>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-slate-100 text-left">
                    <tr>
                        <th class="px-3 py-2">Date</th>
                        <th class="px-3 py-2">Type</th>
                        <th class="px-3 py-2">Qty</th>
                        <th class="px-3 py-2">Reference</th>
                        <th class="px-3 py-2">User</th>
                        <th class="px-3 py-2">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="px-3 py-2">{{ $transaction->created_at->format('Y-m-d') }}</td>
                            <td class="px-3 py-2 capitalize">{{ str_replace('_', ' ', $transaction->transaction_type) }}</td>
                            <td class="px-3 py-2 font-semibold">{{ $transaction->quantity }}</td>
                            <td class="px-3 py-2">{{ $transaction->reference_number ?? '—' }}</td>
                            <td class="px-3 py-2">{{ $transaction->user?->name ?? 'System' }}</td>
                            <td class="px-3 py-2 font-semibold">{{ $transaction->running_balance ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-6 text-center text-gray-500">No transaction history found for this date range.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
