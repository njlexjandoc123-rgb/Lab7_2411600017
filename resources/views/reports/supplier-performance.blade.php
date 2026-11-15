@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="text-3xl font-bold text-slate-800">Supplier performance</h1><p class="mt-1 text-sm text-slate-500">Delivery timeliness, order accuracy, and spend.</p></div>
    <div class="flex gap-2">
        <a href="{{ route('exports.download', ['type' => 'supplier-performance', 'format' => 'csv']) }}" class="rounded border px-3 py-2 text-sm">Export CSV</a>
        <a href="{{ route('exports.download', ['type' => 'supplier-performance', 'format' => 'pdf']) }}" class="rounded border px-3 py-2 text-sm">Export PDF</a>
    </div>
</div>
<div class="mb-6 grid gap-4 md:grid-cols-2">
    <section class="rounded-lg bg-white p-5 shadow">
        <h2 class="mb-3 font-bold">Top suppliers by on-time delivery</h2>
        @forelse($rankedSuppliers->take(5) as $supplier)
            <div class="mb-2 flex items-center justify-between text-sm"><span>{{ $supplier->name }}</span><span class="font-semibold">{{ $metrics[$supplier->id]['on_time_percentage'] ?? 0 }}%</span></div>
        @empty
            <p class="text-sm text-slate-500">No supplier delivery history yet.</p>
        @endforelse
    </section>
    <section class="rounded-lg bg-white p-5 shadow">
        <h2 class="mb-3 font-bold">Lowest on-time delivery</h2>
        @forelse($rankedSuppliers->reverse()->take(5) as $supplier)
            <div class="mb-2 flex items-center justify-between text-sm"><span>{{ $supplier->name }}</span><span class="font-semibold">{{ $metrics[$supplier->id]['on_time_percentage'] ?? 0 }}%</span></div>
        @empty
            <p class="text-sm text-slate-500">No supplier delivery history yet.</p>
        @endforelse
    </section>
</div>
<section class="mb-6 rounded-lg bg-white p-5 shadow">
    <h2 class="mb-4 font-bold">Monthly delivery trend</h2>
    <div class="grid h-44 grid-cols-6 items-end gap-3">
        @foreach($deliveryTrend as $month)
            <div class="flex h-full flex-col items-center justify-end gap-2">
                <span class="text-xs font-semibold">{{ $month['deliveries'] }} / {{ $month['on_time_percentage'] }}%</span>
                <div class="w-full rounded-t bg-sky-600" style="height: {{ $month['deliveries'] ? max(12, $month['on_time_percentage']) : 4 }}%"></div>
                <span class="text-xs text-slate-500">{{ \Carbon\Carbon::createFromFormat('Y-m', $month['month'])->format('M') }}</span>
            </div>
        @endforeach
    </div>
    <p class="mt-2 text-xs text-slate-500">Each bar shows the on-time delivery percentage for the month; counts are displayed above.</p>
</section>
<div class="overflow-x-auto rounded-lg bg-white shadow">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-sky-950 text-white"><tr><th class="px-4 py-3">Supplier</th><th class="px-4 py-3">Orders</th><th class="px-4 py-3">Delivered</th><th class="px-4 py-3">On time</th><th class="px-4 py-3">Accuracy</th><th class="px-4 py-3">Avg. days</th><th class="px-4 py-3">Vs expected</th><th class="px-4 py-3">Order value</th></tr></thead>
        <tbody class="divide-y">
        @forelse($suppliers as $supplier)
            @php($metric = $metrics[$supplier->id])
            <tr><td class="px-4 py-3 font-medium">{{ $supplier->name }}</td><td class="px-4 py-3">{{ $metric['total_orders'] }}</td><td class="px-4 py-3">{{ $metric['delivered_orders'] }}</td><td class="px-4 py-3">{{ $metric['on_time_percentage'] }}%</td><td class="px-4 py-3">{{ $metric['accuracy_percentage'] }}%</td><td class="px-4 py-3">{{ $metric['average_delivery_days'] }}</td><td class="px-4 py-3">{{ $metric['average_delivery_variance_days'] }} days</td><td class="px-4 py-3">{{ number_format((float) $metric['total_value'], 2) }}</td></tr>
        @empty
            <tr><td colspan="8" class="px-4 py-8 text-center text-slate-500">No active suppliers found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
