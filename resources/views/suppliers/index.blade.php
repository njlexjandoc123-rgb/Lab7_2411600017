@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-3xl font-bold text-slate-800">Suppliers</h1>
        <p class="mt-1 text-sm text-slate-500">Manage supplier contacts, terms, and performance.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('supplier-performance.index') }}" class="rounded border border-sky-700 px-4 py-2 text-sm font-semibold text-sky-800">Performance</a>
        <a href="{{ route('suppliers.create') }}" class="rounded bg-sky-700 px-4 py-2 text-sm font-semibold text-white">+ Add Supplier</a>
    </div>
</div>

<x-filter-form :action="route('suppliers.index')" class="mb-5 grid gap-3 rounded-lg bg-white p-4 shadow md:grid-cols-5">
    <input name="q" value="{{ request('q') }}" placeholder="Name, contact, email" class="rounded border px-3 py-2 text-sm md:col-span-2">
    <select name="status" class="rounded border px-3 py-2 text-sm">
        <option value="">All statuses</option>
        <option value="active" @selected(request('status') === 'active')>Active</option>
        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        <option value="archived" @selected(request('status') === 'archived')>Archived</option>
    </select>
    <select name="sort" class="rounded border px-3 py-2 text-sm">
        <option value="name" @selected(request('sort', 'name') === 'name')>Sort: name</option>
        <option value="lead_time_days" @selected(request('sort') === 'lead_time_days')>Sort: lead time</option>
        <option value="created_at" @selected(request('sort') === 'created_at')>Sort: newest</option>
    </select>
    <button class="rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white">Filter</button>
</x-filter-form>

<div class="overflow-x-auto rounded-lg bg-white shadow">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-sky-950 text-white"><tr>
            <th class="px-4 py-3">Supplier</th><th class="px-4 py-3">Contact</th><th class="px-4 py-3">Lead time</th>
            <th class="px-4 py-3">Products / Orders</th><th class="px-4 py-3">On-time</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Actions</th>
        </tr></thead>
        <tbody class="divide-y">
        @forelse($suppliers as $supplier)
            <tr>
                <td class="px-4 py-3"><a class="font-semibold text-sky-800 hover:underline" href="{{ route('suppliers.show', $supplier) }}">{{ $supplier->name }}</a><div class="text-xs text-slate-500">{{ $supplier->email }}</div></td>
                <td class="px-4 py-3">{{ $supplier->contact_person ?: '—' }}<div class="text-xs text-slate-500">{{ $supplier->phone }}</div></td>
                <td class="px-4 py-3">{{ $supplier->lead_time_days }} days</td>
                <td class="px-4 py-3">{{ $supplier->products_count }} / {{ $supplier->purchase_orders_count }}</td>
                <td class="px-4 py-3">{{ $metrics[$supplier->id]['on_time_percentage'] ?? 0 }}%</td>
                <td class="px-4 py-3"><span class="rounded-full px-2 py-1 text-xs font-semibold {{ $supplier->trashed() ? 'bg-red-100 text-red-800' : ($supplier->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600') }}">{{ $supplier->trashed() ? 'Archived' : ($supplier->is_active ? 'Active' : 'Inactive') }}</span></td>
                <td class="whitespace-nowrap px-4 py-3">
                    @if($supplier->trashed())
                        @can('restore', App\Models\Supplier::class)
                            <form class="inline" method="POST" action="{{ route('suppliers.restore', $supplier->id) }}">@csrf<button class="text-emerald-700 hover:underline">Restore</button></form>
                        @endcan
                    @else
                        <a class="mr-2 text-sky-700 hover:underline" href="{{ route('suppliers.show', $supplier) }}">View</a>
                        <a class="mr-2 text-slate-700 hover:underline" href="{{ route('suppliers.edit', $supplier) }}">Edit</a>
                        <form class="inline" method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Archive this supplier?')">
                            @csrf @method('DELETE')
                            <button class="text-red-700 hover:underline">Archive</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">No suppliers match your filters.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="border-t bg-slate-50 p-4">{{ $suppliers->links() }}</div>
</div>
<div class="mt-4 flex gap-2">
    <a href="{{ route('exports.download', ['type' => 'suppliers', 'format' => 'csv']) }}" class="rounded border px-3 py-2 text-sm">Export suppliers CSV</a>
    <a href="{{ route('exports.download', ['type' => 'suppliers', 'format' => 'pdf']) }}" class="rounded border px-3 py-2 text-sm">Export suppliers PDF</a>
</div>
@endsection
