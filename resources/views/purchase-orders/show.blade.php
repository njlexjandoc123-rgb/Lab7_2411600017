@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <a href="{{ route('purchase-orders.index') }}" class="text-sm text-sky-700 hover:underline">← Purchase orders</a>
        <h1 class="mt-2 text-3xl font-bold text-slate-800">{{ $purchaseOrder->reference_number }}</h1>
        <p class="mt-1 text-sm text-slate-500">Supplier: {{ $purchaseOrder->supplier?->name ?? 'Archived supplier' }} · {{ $purchaseOrder->order_date->format('M d, Y') }}</p>
    </div>
    <span class="rounded-full bg-slate-100 px-3 py-1.5 text-sm font-semibold capitalize">{{ $purchaseOrder->status }}</span>
</div>

<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-lg bg-white p-4 shadow"><p class="text-xs uppercase text-slate-500">Subtotal</p><p class="mt-2 text-xl font-bold">{{ number_format((float) $purchaseOrder->subtotal, 2) }}</p></div>
    <div class="rounded-lg bg-white p-4 shadow"><p class="text-xs uppercase text-slate-500">Tax</p><p class="mt-2 text-xl font-bold">{{ number_format((float) $purchaseOrder->tax_amount, 2) }}</p></div>
    <div class="rounded-lg bg-white p-4 shadow"><p class="text-xs uppercase text-slate-500">Total</p><p class="mt-2 text-xl font-bold">{{ number_format((float) $purchaseOrder->total_amount, 2) }}</p></div>
    <div class="rounded-lg bg-white p-4 shadow"><p class="text-xs uppercase text-slate-500">Expected delivery</p><p class="mt-2 text-xl font-bold">{{ $purchaseOrder->expected_delivery_date?->format('M d, Y') ?? 'Not set' }}</p></div>
</div>

<section class="mb-6 overflow-x-auto rounded-lg bg-white shadow">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-sky-950 text-white"><tr><th class="px-4 py-3">Product</th><th class="px-4 py-3">SKU</th><th class="px-4 py-3">Ordered</th><th class="px-4 py-3">Received</th><th class="px-4 py-3">Outstanding</th><th class="px-4 py-3">Unit price</th><th class="px-4 py-3">Line total</th></tr></thead>
        <tbody class="divide-y">
        @foreach($purchaseOrder->items as $item)
            <tr><td class="px-4 py-3">{{ $item->product?->name ?? 'Deleted product' }}</td><td class="px-4 py-3">{{ $item->product?->sku ?? '—' }}</td><td class="px-4 py-3">{{ $item->quantity }}</td><td class="px-4 py-3">{{ $item->received_quantity }}</td><td class="px-4 py-3">{{ $item->outstanding_quantity }}</td><td class="px-4 py-3">{{ number_format((float) $item->unit_price, 2) }}</td><td class="px-4 py-3">{{ number_format($item->line_total, 2) }}</td></tr>
        @endforeach
        </tbody>
    </table>
</section>

<section class="mb-6 rounded-lg bg-white p-5 shadow">
    <h2 class="mb-3 text-lg font-bold">Approval history</h2>
    <p class="text-sm text-slate-700">Created by {{ $purchaseOrder->createdBy?->name ?? 'System' }} on {{ $purchaseOrder->created_at->format('M d, Y H:i') }}</p>
    @if($purchaseOrder->approved_at)<p class="mt-1 text-sm text-slate-700">Approved by {{ $purchaseOrder->approvedBy?->name ?? 'User removed' }} on {{ $purchaseOrder->approved_at->format('M d, Y H:i') }}</p>@endif
    @if($purchaseOrder->rejection_reason)<p class="mt-2 rounded bg-red-50 p-3 text-sm text-red-800">Rejection reason: {{ $purchaseOrder->rejection_reason }}</p>@endif
    @if($purchaseOrder->notes)<p class="mt-3 whitespace-pre-line text-sm text-slate-600">{{ $purchaseOrder->notes }}</p>@endif
</section>

<div class="flex flex-wrap gap-3">
    @can('update', $purchaseOrder)
        <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="rounded border px-4 py-2 text-sm font-semibold">Edit draft</a>
    @endcan
    @can('delete', $purchaseOrder)
        <form method="POST" action="{{ route('purchase-orders.destroy', $purchaseOrder) }}" onsubmit="return confirm('Archive this draft?')">@csrf @method('DELETE')<button class="rounded border border-red-300 px-4 py-2 text-sm text-red-700">Archive</button></form>
    @endcan
    @can('approve', $purchaseOrder)
        <form method="POST" action="{{ route('purchase-orders.approve', $purchaseOrder) }}">@csrf<button class="rounded bg-emerald-700 px-4 py-2 text-sm font-semibold text-white">Approve</button></form>
        <form method="POST" action="{{ route('purchase-orders.reject', $purchaseOrder) }}" class="flex gap-2">@csrf<input name="reason" required maxlength="2000" placeholder="Rejection reason" class="rounded border px-3 py-2 text-sm"><button class="rounded bg-red-700 px-4 py-2 text-sm font-semibold text-white">Reject</button></form>
    @endcan
    @can('send', $purchaseOrder)
        <form method="POST" action="{{ route('purchase-orders.send', $purchaseOrder) }}">@csrf<button class="rounded bg-sky-700 px-4 py-2 text-sm font-semibold text-white">Mark sent</button></form>
    @endcan
</div>

@can('receive', $purchaseOrder)
<form method="POST" action="{{ route('purchase-orders.receive', $purchaseOrder) }}" class="mt-6 rounded-lg bg-white p-5 shadow">
    @csrf
    <h2 class="mb-3 text-lg font-bold">Receive items</h2>
    <div class="space-y-3">
        @foreach($purchaseOrder->items as $item)
            <div class="grid items-center gap-2 text-sm md:grid-cols-[2fr_1fr_1fr]">
                <label for="received-{{ $item->id }}">{{ $item->product?->name ?? 'Deleted product' }} ({{ $item->product?->sku }})</label>
                <span>Outstanding: {{ $item->outstanding_quantity }}</span>
                <input id="received-{{ $item->id }}" type="number" name="items[{{ $item->id }}][quantity]" min="0" max="{{ $item->outstanding_quantity }}" value="0" class="rounded border px-3 py-2">
            </div>
        @endforeach
    </div>
    <div class="mt-3 flex flex-wrap gap-3">
        <input name="notes" maxlength="500" placeholder="Receipt notes (optional)" class="min-w-60 flex-1 rounded border px-3 py-2 text-sm">
        <button class="rounded bg-emerald-700 px-4 py-2 text-sm font-semibold text-white">Record receipt</button>
    </div>
</form>
@endcan
@endsection
