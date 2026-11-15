@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-wrap items-start justify-between gap-3">
    <div>
        <a href="{{ route('suppliers.index') }}" class="text-sm text-sky-700 hover:underline">← Suppliers</a>
        <h1 class="mt-2 text-3xl font-bold text-slate-800">{{ $supplier->name }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $supplier->full_contact ?: 'No contact details' }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('suppliers.edit', $supplier) }}" class="rounded bg-sky-700 px-4 py-2 text-sm font-semibold text-white">Edit</a>
        @if($supplier->trashed())
            @can('restore', App\Models\Supplier::class)
                <form method="POST" action="{{ route('suppliers.restore', $supplier->id) }}">@csrf <button class="rounded bg-emerald-700 px-4 py-2 text-sm font-semibold text-white">Restore</button></form>
            @endcan
        @endif
    </div>
</div>

<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
    @foreach([
        'Orders' => $metrics['total_orders'],
        'On-time delivery' => $metrics['on_time_percentage'].'%',
        'Order accuracy' => $metrics['accuracy_percentage'].'%',
        'Average delivery' => $metrics['average_delivery_days'].' days',
        'Average vs expected' => $metrics['average_delivery_variance_days'].' days',
    ] as $label => $value)
        <div class="rounded-lg bg-white p-5 shadow"><p class="text-xs font-semibold uppercase text-slate-500">{{ $label }}</p><p class="mt-2 text-2xl font-bold text-slate-800">{{ $value }}</p></div>
    @endforeach
</div>

<div class="mb-6 grid gap-6 lg:grid-cols-2">
    <section class="rounded-lg bg-white p-5 shadow">
        <h2 class="mb-4 text-lg font-bold">Supplier information</h2>
        <dl class="grid grid-cols-2 gap-3 text-sm">
            <dt class="text-slate-500">Payment terms</dt><dd>{{ $supplier->payment_terms }}</dd>
            <dt class="text-slate-500">Lead time</dt><dd>{{ $supplier->lead_time_days }} days</dd>
            <dt class="text-slate-500">Minimum order</dt><dd>{{ number_format((float) $supplier->minimum_order_amount, 2) }}</dd>
            <dt class="text-slate-500">Status</dt><dd>{{ $supplier->is_active ? 'Active' : 'Inactive' }}</dd>
            <dt class="text-slate-500">Address</dt><dd class="whitespace-pre-line">{{ $supplier->address ?: '—' }}</dd>
        </dl>
    </section>
    <section class="rounded-lg bg-white p-5 shadow">
        <h2 class="mb-4 text-lg font-bold">Products supplied</h2>
        <ul class="divide-y">
            @forelse($supplier->products as $product)
                <li class="flex justify-between py-2 text-sm"><a class="font-medium text-sky-700 hover:underline" href="{{ route('products.show', $product) }}">{{ $product->name }}</a><span>{{ $product->sku }} · {{ $product->qty }} in stock</span></li>
            @empty
                <li class="py-3 text-sm text-slate-500">No products assigned to this supplier.</li>
            @endforelse
        </ul>
    </section>
</div>
<section class="overflow-hidden rounded-lg bg-white shadow">
    <div class="flex items-center justify-between p-5"><h2 class="text-lg font-bold">Recent purchase orders</h2><a href="{{ route('purchase-orders.index', ['supplier_id' => $supplier->id]) }}" class="text-sm text-sky-700 hover:underline">All orders</a></div>
    <table class="min-w-full text-left text-sm">
        <thead class="bg-slate-100"><tr><th class="px-4 py-2">Reference</th><th class="px-4 py-2">Date</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Total</th></tr></thead>
        <tbody class="divide-y">
        @forelse($supplier->purchaseOrders as $order)
            <tr><td class="px-4 py-3"><a class="text-sky-700 hover:underline" href="{{ route('purchase-orders.show', $order) }}">{{ $order->reference_number }}</a></td><td class="px-4 py-3">{{ $order->order_date->format('M d, Y') }}</td><td class="px-4 py-3 capitalize">{{ $order->status }}</td><td class="px-4 py-3">{{ number_format((float) $order->total_amount, 2) }}</td></tr>
        @empty
            <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">No purchase orders yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</section>
@endsection
