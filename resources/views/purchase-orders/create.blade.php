@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl rounded-xl bg-white p-6 shadow">
    <h1 class="mb-6 text-2xl font-bold text-slate-800">Create purchase order</h1>
    <form method="POST" action="{{ route('purchase-orders.store') }}">
        @csrf
        @include('purchase-orders._form')
        <div class="mt-6 flex justify-between">
            <a href="{{ route('purchase-orders.index') }}" class="rounded border px-4 py-2 text-sm">Cancel</a>
            <button class="rounded bg-sky-700 px-5 py-2 text-sm font-semibold text-white">Save purchase order</button>
        </div>
    </form>
</div>
@endsection
