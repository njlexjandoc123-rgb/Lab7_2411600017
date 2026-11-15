<section class="rounded-xl bg-white p-5 shadow">
    <div class="mb-3 flex items-center justify-between"><h2 class="font-bold text-slate-800">Recent purchase orders</h2><a href="{{ route('purchase-orders.index') }}" class="text-sm font-semibold text-sky-700">View all</a></div>
    <ul class="divide-y">
        @forelse($recentPurchaseOrders as $order)
            <li class="flex justify-between gap-3 py-2 text-sm"><a class="font-medium text-sky-700 hover:underline" href="{{ route('purchase-orders.show', $order) }}">{{ $order->reference_number }}</a><span class="text-slate-500">{{ $order->supplier?->name ?? 'Supplier' }} · {{ ucfirst($order->status) }}</span></li>
        @empty
            <li class="py-4 text-sm text-slate-500">No purchase orders yet.</li>
        @endforelse
    </ul>
</section>
