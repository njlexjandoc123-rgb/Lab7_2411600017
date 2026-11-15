@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Student Portal Inventory</h1>
    <a href="{{ route('products.create') }}" class="bg-[#0369a1] hover:bg-[#075985] text-white px-4 py-2 rounded shadow text-sm font-semibold">+ Add Product</a>
</div>

<x-filter-form :action="route('products.index')" class="mb-5 grid gap-3 rounded-lg bg-white p-4 shadow md:grid-cols-4">
    <input name="q" value="{{ request('q') }}" placeholder="Product, SKU, or supplier" class="rounded border px-3 py-2 text-sm md:col-span-2">
    <select name="category" class="rounded border px-3 py-2 text-sm">
        <option value="">All categories</option>
        @foreach($categories as $category)<option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>@endforeach
    </select>
    <select name="supplier_id" class="rounded border px-3 py-2 text-sm">
        <option value="">All suppliers</option>
        @foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>@endforeach
    </select>
    <div class="flex gap-2 md:col-span-4">
        <button class="rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white">Filter</button>
        <a href="{{ route('exports.download', ['type' => 'products', 'format' => 'csv']) }}" class="rounded border px-4 py-2 text-sm">Export CSV</a>
        <a href="{{ route('exports.download', ['type' => 'products', 'format' => 'pdf']) }}" class="rounded border px-4 py-2 text-sm">Export PDF</a>
    </div>
</x-filter-form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-sky-950 text-white text-xs uppercase tracking-wider">
                <th class="py-3 px-4">SKU</th>
                <th class="py-3 px-4">Product Name</th>
                <th class="py-3 px-4">Brand</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4">Supplier</th>
                <th class="py-3 px-4">Qty</th>
                <th class="py-3 px-4">Unit Price</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 text-sm">
            @forelse($products as $product)
            <tr class="hover:bg-gray-50">
                <td class="py-3 px-4 text-amber-600 font-mono text-xs font-semibold">{{ $product->sku }}</td>
                <td class="py-3 px-4 font-semibold text-gray-800">{{ $product->name }}</td>
                <td class="py-3 px-4"><span class="bg-gray-600 text-white text-xs px-2.5 py-0.5 rounded">{{ $product->brand ?? 'N/A' }}</span></td>
                <td class="py-3 px-4"><span class="bg-sky-700 text-white text-xs px-2.5 py-0.5 rounded">{{ $product->category ?? 'N/A' }}</span></td>
                <td class="py-3 px-4">{{ $product->supplier?->name ?? '—' }}</td>
                <td class="py-3 px-4 font-bold">{{ $product->qty }}</td>
                <td class="py-3 px-4">₱{{ number_format($product->unit_price, 2) }}</td>
                <td class="py-3 px-4">
                    <span class="px-2.5 py-1 rounded font-semibold text-xs {{ $product->qty > 5 ? 'bg-emerald-100 text-emerald-800' : ($product->qty > 0 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                        {{ $product->qty > 5 ? 'In Stock' : ($product->qty > 0 ? 'Low Stock' : 'Out of Stock') }}
                    </span>
                </td>
                <td class="py-3 px-4 text-center space-x-1">
                    <a href="{{ route('products.edit', $product->id) }}" class="bg-gray-100 hover:bg-gray-200 p-1.5 rounded text-xs inline-block">✏️</a>
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-gray-100 hover:bg-gray-200 p-1.5 rounded text-xs text-red-600" onclick="return confirm('Sigurado ka bang burahin ito?')">🗑️</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="py-6 px-4 text-center text-gray-500">
                    No products found. <a href="{{ route('products.create') }}" class="text-sky-700 hover:underline">Add your first product</a>.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <!-- Pagination Footer -->
    <div class="px-4 py-3 bg-gray-50 border-t flex justify-between items-center text-xs text-gray-500">
        <span>
            @if($products->total() > 0)
                Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }} products
            @else
                Showing 0 of 0 products
            @endif
        </span>
        <div>
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection