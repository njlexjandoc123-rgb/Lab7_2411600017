<section class="rounded-xl bg-white p-5 shadow">
    <div class="mb-3 flex items-center justify-between"><h2 class="font-bold text-slate-800">Reorder alerts</h2><a href="{{ route('purchase-orders.create') }}" class="text-sm font-semibold text-sky-700">Create order</a></div>
    <ul class="divide-y">
        @forelse($reorderProducts as $product)
            <li class="flex justify-between gap-3 py-2 text-sm"><span>{{ $product->name }} <span class="text-slate-500">({{ $product->supplier?->name ?? 'No supplier' }})</span></span><span class="font-semibold text-amber-700">{{ $product->qty }} / {{ $product->calculated_reorder_point }}</span></li>
        @empty
            <li class="py-4 text-sm text-slate-500">No products currently need reordering.</li>
        @endforelse
    </ul>
</section>
