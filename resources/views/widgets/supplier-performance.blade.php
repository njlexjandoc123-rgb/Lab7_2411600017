<section class="rounded-xl bg-white p-5 shadow">
    <div class="mb-3 flex items-center justify-between"><h2 class="font-bold text-slate-800">Supplier performance</h2><a href="{{ route('supplier-performance.index') }}" class="text-sm font-semibold text-sky-700">Report</a></div>
    <ul class="divide-y">
        @forelse($topSuppliers as $supplier)
            <li class="flex justify-between py-2 text-sm"><span>{{ $supplier->name }}</span><span class="font-semibold">{{ $supplierPerformanceMetrics[$supplier->id]['on_time_percentage'] ?? 0 }}% on time</span></li>
        @empty
            <li class="py-4 text-sm text-slate-500">Add suppliers to track performance.</li>
        @endforelse
    </ul>
</section>
