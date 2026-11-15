@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-xl rounded-lg bg-white p-8 shadow">
    <h1 class="mb-6 text-2xl font-bold text-gray-800">Edit Brand</h1>
    <form action="{{ route('brands.update', $brand) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="brand-name" class="mb-1 block text-xs font-semibold uppercase text-gray-600">Brand name</label>
        <input id="brand-name" type="text" name="name" value="{{ old('name', $brand->name) }}" maxlength="100" required class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        <p class="mt-2 text-xs text-gray-500">Products assigned to this brand will keep their assignment.</p>
        <div class="mt-6 flex justify-between">
            <a href="{{ route('brands.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
            <button type="submit" class="rounded bg-[#0369a1] px-5 py-2 text-sm font-semibold text-white hover:bg-[#075985]">Save Changes</button>
        </div>
    </form>
</div>
@endsection
