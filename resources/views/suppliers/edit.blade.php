@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl rounded-xl bg-white p-6 shadow">
    <h1 class="mb-6 text-2xl font-bold text-slate-800">Edit {{ $supplier->name }}</h1>
    <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
        @csrf
        @method('PUT')
        @include('suppliers._form')
    </form>
</div>
@endsection
