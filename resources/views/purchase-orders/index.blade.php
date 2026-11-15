@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="text-3xl font-bold text-slate-800">Purchase orders</h1><p class="mt-1 text-sm text-slate-500">Track drafts, approvals, delivery, and receipt.</p></div>
    <div class="flex gap-2">
        <a href="{{ route('exports.download', ['type' => 'purchase-orders', 'format' => 'csv']) }}" class="rounded border px-3 py-2 text-sm">Export CSV</a>
        <a href="{{ route('exports.download', ['type' => 'purchase-orders', 'format' => 'pdf']) }}" class="rounded border px-3 py-2 text-sm">Export PDF</a>
        <a href="{{ route('purchase-orders.create') }}" class="rounded bg-sky-700 px-4 py-2 text-sm font-semibold text-white">+ New order</a>
    </div>
</div>

<x-filter-form :action="route('purchase-orders.index')" class="mb-5 grid gap-3 rounded-lg bg-white p-4 shadow md:grid-cols-6">
    <input name="q" value="{{ request('q') }}" placeholder="Reference or supplier" class="rounded border px-3 py-2 text-sm md:col-span-2">
    <select name="status" class="rounded border px-3 py-2 text-sm"><option value="">All statuses</option>@foreach(App\Models\PurchaseOrder::STATUSES as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>
    <select name="supplier_id" class="rounded border px-3 py-2 text-sm"><option value="">All suppliers</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>@endforeach</select>
    <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded border px-3 py-2 text-sm">
    <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded border px-3 py-2 text-sm">
    <button class="rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white md:col-span-6">Filter orders</button>
</x-filter-form>

<div class="overflow-x-auto rounded-lg bg-white shadow">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-sky-950 text-white"><tr><th class="px-4 py-3">Reference</th><th class="px-4 py-3">Supplier</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Created by</th><th class="px-4 py-3">Items</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Status</th></tr></thead>
        <tbody class="divide-y">
        @forelse($orders as $order)
            <tr>
                <td class="px-4 py-3 font-semibold"><a class="text-sky-700 hover:underline" href="{{ route('purchase-orders.show', $order) }}">{{ $order->reference_number }}</a></td>
                <td class="px-4 py-3">{{ $order->supplier?->name ?? 'Archived supplier' }}</td>
                <td class="px-4 py-3">{{ $order->order_date->format('M d, Y') }}</td>
                <td class="px-4 py-3">{{ $order->createdBy?->name ?? 'System' }}</td>
                <td class="px-4 py-3">{{ $order->items->count() }}</td>
                <td class="px-4 py-3">{{ number_format((float) $order->total_amount, 2) }}</td>
                <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ match($order->status) { 'received' => 'bg-emerald-100 text-emerald-800', 'rejected', 'cancelled' => 'bg-red-100 text-red-800', 'pending' => 'bg-amber-100 text-amber-800', default => 'bg-slate-100 text-slate-700' } }}">{{ ucfirst($order->status) }}</span></td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">No purchase orders match these filters.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="border-t bg-slate-50 p-4">{{ $orders->links() }}</div>
</div>
@endsection
