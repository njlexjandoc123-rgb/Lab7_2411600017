@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl rounded-lg bg-white p-8 shadow">
    <h1 class="mb-6 text-2xl font-bold text-gray-800">Record Inventory Transaction</h1>

    <form action="{{ route('transactions.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">Product</label>
            <select name="product_id" required class="w-full rounded border px-3 py-2 text-sm">
                <option value="">Select product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }} ({{ $product->qty }} in stock)</option>
                @endforeach
            </select>
            @error('product_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">Transaction Type</label>
            <select name="transaction_type" id="transaction_type" required class="w-full rounded border px-3 py-2 text-sm">
                <option value="">Select type</option>
                @foreach($transactionTypes as $key => $label)
                    <option value="{{ $key }}" @selected(old('transaction_type') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            @error('transaction_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">Quantity</label>
            <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" min="1" required class="w-full rounded border px-3 py-2 text-sm">
            @error('quantity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">Reference Number</label>
            <input type="text" name="reference_number" value="{{ old('reference_number') }}" class="w-full rounded border px-3 py-2 text-sm">
        </div>

        <div class="mb-6">
            <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">Notes</label>
            <textarea name="notes" rows="3" class="w-full rounded border px-3 py-2 text-sm">{{ old('notes') }}</textarea>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('transactions.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
            <button type="submit" class="rounded bg-sky-700 px-5 py-2 text-sm font-semibold text-white hover:bg-sky-800">Save Transaction</button>
        </div>
    </form>
</div>
@endsection
