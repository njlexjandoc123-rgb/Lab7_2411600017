@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Inventory Transactions</h1>
        <p class="mt-1 text-sm text-gray-500">Track stock movement and movement history.</p>
    </div>
    <a href="{{ route('transactions.create') }}" class="rounded bg-sky-700 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-800">+ Record Transaction</a>
</div>

<x-filter-form :action="route('transactions.index')" class="mb-6 grid gap-3 rounded-lg bg-white p-4 shadow md:grid-cols-6">
    <input name="q" value="{{ request('q') }}" placeholder="Product, SKU, or reference" class="w-full rounded border px-3 py-2 text-sm">
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">Product</label>
        <select name="product_id" class="w-full rounded border px-3 py-2 text-sm">
            <option value="">All products</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>{{ $product->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">Type</label>
        <select name="transaction_type" class="w-full rounded border px-3 py-2 text-sm">
            <option value="">All</option>
            <option value="stock_in" @selected(request('transaction_type') === 'stock_in')>Stock In</option>
            <option value="stock_out" @selected(request('transaction_type') === 'stock_out')>Stock Out</option>
            <option value="adjustment" @selected(request('transaction_type') === 'adjustment')>Adjustment</option>
        </select>
    </div>
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">Start Date</label>
        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded border px-3 py-2 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">End Date</label>
        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded border px-3 py-2 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">User</label>
        <select name="user_id" class="w-full rounded border px-3 py-2 text-sm">
            <option value="">All users</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white">Filter</button>
        <a href="{{ route('exports.download', ['type' => 'transactions', 'format' => 'csv'] + request()->only(['start_date', 'end_date'])) }}" class="rounded border px-3 py-2 text-sm">CSV</a>
        <a href="{{ route('exports.download', ['type' => 'transactions', 'format' => 'pdf'] + request()->only(['start_date', 'end_date'])) }}" class="rounded border px-3 py-2 text-sm">PDF</a>
    </div>
</x-filter-form>

<div class="overflow-hidden rounded-lg bg-white shadow">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-sky-950 text-white">
            <tr>
                <th class="px-4 py-3 text-left">Date</th>
                <th class="px-4 py-3 text-left">Product</th>
                <th class="px-4 py-3 text-left">Type</th>
                <th class="px-4 py-3 text-left">Qty</th>
                <th class="px-4 py-3 text-left">Reference</th>
                <th class="px-4 py-3 text-left">User</th>
                <th class="px-4 py-3 text-center">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($transactions as $transaction)
                <tr>
                    <td class="px-4 py-3">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $transaction->product?->name ?? 'Deleted product' }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $transaction->transaction_type === 'stock_out' ? 'bg-red-100 text-red-800' : ($transaction->transaction_type === 'stock_in' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800') }}">
                            {{ str_replace('_', ' ', $transaction->transaction_type) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-semibold">{{ $transaction->quantity }}</td>
                    <td class="px-4 py-3">{{ $transaction->reference_number ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $transaction->user?->name ?? 'System' }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('transactions.show', $transaction) }}" class="text-sky-700 hover:underline">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No inventory transactions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="border-t bg-gray-50 px-4 py-3 text-sm text-gray-500">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
