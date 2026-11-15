@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Product</h1>

    <form action="{{ route('products.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">SKU</label>
            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required class="w-full px-3 py-2 border rounded text-sm focus:ring-2 focus:ring-sky-700">
        </div>

        <div class="mb-4">
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Product Name</label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-3 py-2 border rounded text-sm focus:ring-2 focus:ring-sky-700">
        </div>

        <div class="mb-4">
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Brand</label>
            <select name="brand" class="w-full px-3 py-2 border rounded text-sm focus:ring-2 focus:ring-sky-700">
                <option value="">Select a brand</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand }}" @selected(old('brand', $product->brand) === $brand)>{{ $brand }}</option>
                @endforeach
            </select>
            @error('brand') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Category</label>
            <select name="category" class="w-full px-3 py-2 border rounded text-sm focus:ring-2 focus:ring-sky-700">
                <option value="">Select a category</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(old('category', $product->category) === $category)>{{ $category }}</option>
                @endforeach
            </select>
            @error('category') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Supplier</label>
            <select name="supplier_id" class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
                <option value="">No supplier assigned</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected(old('supplier_id', $product->supplier_id) == $supplier->id)>{{ $supplier->name }}</option>
                @endforeach
            </select>
            @error('supplier_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Quantity</label>
            <input type="number" name="qty" value="{{ old('qty', $product->qty) }}" required class="w-full px-3 py-2 border rounded text-sm focus:ring-2 focus:ring-sky-700">
        </div>

        <div class="mb-4">
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Reorder Level</label>
            <input type="number" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level ?? 5) }}" required class="w-full px-3 py-2 border rounded text-sm focus:ring-2 focus:ring-sky-700">
        </div>

        <div class="mb-6">
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Unit Price</label>
            <input type="number" step="0.01" name="unit_price" value="{{ old('unit_price', $product->unit_price) }}" required class="w-full px-3 py-2 border rounded text-sm focus:ring-2 focus:ring-sky-700">
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('products.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
            <button type="submit" class="bg-[#0369a1] text-white px-5 py-2 rounded text-sm font-semibold hover:bg-[#075985]">Update Product</button>
        </div>
    </form>
</div>
@endsection