@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl rounded-lg bg-white p-6 shadow">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Transaction Details</h1>
            <p class="mt-1 text-sm text-gray-500">Reference: {{ $transaction->reference_number ?? 'No reference' }}</p>
        </div>
        <a href="{{ route('transactions.index') }}" class="rounded bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">Back to list</a>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        <div class="rounded border border-gray-200 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Type</p>
            <p class="mt-2 text-lg font-bold capitalize">{{ str_replace('_', ' ', $transaction->transaction_type) }}</p>
        </div>
        <div class="rounded border border-gray-200 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Quantity</p>
            <p class="mt-2 text-lg font-bold">{{ $transaction->quantity }}</p>
        </div>
        <div class="rounded border border-gray-200 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Date</p>
            <p class="mt-2 text-lg font-bold">{{ $transaction->created_at->format('Y-m-d H:i') }}</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded border border-gray-200 p-4">
            <h2 class="mb-3 text-lg font-bold text-gray-800">Product</h2>
            <p class="font-semibold text-slate-800">{{ $transaction->product?->name ?? 'Product unavailable' }}</p>
            <p class="mt-1 text-sm text-gray-600">SKU: {{ $transaction->product?->sku ?? '—' }}</p>
            <p class="mt-1 text-sm text-gray-600">Current quantity: {{ $transaction->product?->qty ?? '—' }}</p>
        </div>

        <div class="rounded border border-gray-200 p-4">
            <h2 class="mb-3 text-lg font-bold text-gray-800">User</h2>
            <p class="font-semibold text-slate-800">{{ $transaction->user?->name ?? 'System' }}</p>
            <p class="mt-1 text-sm text-gray-600">{{ $transaction->user?->email ?? 'No email available' }}</p>
        </div>
    </div>

    <div class="mt-6 rounded border border-gray-200 p-4">
        <h2 class="mb-2 text-lg font-bold text-gray-800">Notes</h2>
        <p class="text-gray-600">{{ $transaction->notes ?: 'No additional notes.' }}</p>
    </div>

    @if(auth()->user()?->email === 'admin@studentportal.test')
        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="mt-6">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700" onclick="return confirm('Delete this transaction? This will reverse the stock movement.')">
                Delete Transaction
            </button>
        </form>
    @endif
</div>
@endsection
