@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl rounded-xl bg-white p-6 shadow">
    <h1 class="mb-2 text-2xl font-bold text-slate-800">Edit draft {{ $purchaseOrder->reference_number }}</h1>
    <p class="mb-6 text-sm text-slate-500">Unit prices are taken from the product record and saved as a historical snapshot.</p>
    <form method="POST" action="{{ route('purchase-orders.update', $purchaseOrder) }}">
        @csrf
        @method('PUT')
        @include('purchase-orders._form')
        <div class="mt-6 flex justify-between">
            <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="rounded border px-4 py-2 text-sm">Cancel</a>
            <button class="rounded bg-sky-700 px-5 py-2 text-sm font-semibold text-white">Update draft</button>
        </div>
    </form>
</div>
@endsection
