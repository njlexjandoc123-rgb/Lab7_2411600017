@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Categories</h1>
        <p class="text-sm text-gray-500 mt-1">Manage categories and view their products.</p>
    </div>
    <button type="submit" form="add-category-form" class="bg-[#0369a1] hover:bg-[#075985] text-white px-4 py-2 rounded shadow text-sm font-semibold">+ Add Category</button>
</div>

@if(session('success'))
    <div class="mb-4 rounded bg-emerald-100 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 rounded bg-red-100 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
@endif

<form id="add-category-form" action="{{ route('categories.store') }}" method="POST" class="mb-6 flex flex-col gap-2 rounded-lg bg-white p-4 shadow sm:flex-row sm:items-start">
    @csrf
    <div class="flex-1">
        <label for="category-name" class="mb-1 block text-xs font-semibold uppercase text-gray-600">New category name</label>
        <input id="category-name" type="text" name="name" value="{{ old('name') }}" maxlength="100" required placeholder="e.g. Stationery" class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <button type="submit" class="mt-5 rounded bg-sky-950 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-900 sm:mt-6">Save Category</button>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-sky-950 text-white text-xs uppercase tracking-wider">
                <th class="py-3 px-4">Category Name</th>
                <th class="py-3 px-4">Products</th>
                <th class="py-3 px-4">Items</th>
                <th class="py-3 px-4 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 text-sm">
            @forelse($categories as $category)
            <tr>
                <td class="py-3 px-4 font-semibold text-gray-800">{{ $category->name }}</td>
                <td class="py-3 px-4">
                    <ul class="space-y-1">
                        @forelse($category->products as $product)
                        <li>
                            <a href="{{ route('products.edit', $product) }}" class="text-sky-700 hover:underline">{{ $product->name }}</a>
                            <span class="text-gray-500">({{ $product->sku }})</span>
                        </li>
                        @empty
                        <li class="text-gray-500">No products assigned yet.</li>
                        @endforelse
                    </ul>
                </td>
                <td class="py-3 px-4">{{ $category->products->count() }}</td>
                <td class="space-x-1 whitespace-nowrap py-3 px-4 text-center">
                    <a href="{{ route('categories.edit', $category) }}" class="inline-block rounded bg-gray-100 px-2 py-1 text-xs hover:bg-gray-200">Edit</a>
                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category? Its products will remain but become unassigned.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded bg-red-50 px-2 py-1 text-xs text-red-700 hover:bg-red-100">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="py-6 px-4 text-center text-gray-500">
                    No categories found. Use Add Category to create the first one.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 bg-gray-50 border-t text-xs text-gray-500">
        {{ $categories->count() }} {{ \Illuminate\Support\Str::plural('category', $categories->count()) }}
    </div>
</div>
@endsection
