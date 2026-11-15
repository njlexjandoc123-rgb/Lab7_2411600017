@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-xl rounded-lg bg-white p-8 shadow">
    <h1 class="mb-6 text-2xl font-bold text-gray-800">Edit User</h1>
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="user-name" class="mb-1 block text-xs font-semibold uppercase text-gray-600">Name</label>
            <input id="user-name" type="text" name="name" value="{{ old('name', $user->name) }}" maxlength="255" required class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label for="user-email" class="mb-1 block text-xs font-semibold uppercase text-gray-600">Email</label>
            <input id="user-email" type="email" name="email" value="{{ old('email', $user->email) }}" maxlength="255" required class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label for="user-role" class="mb-1 block text-xs font-semibold uppercase text-gray-600">Role</label>
            <select id="user-role" name="role" required class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
                @foreach(['staff' => 'Staff', 'warehouse' => 'Warehouse staff', 'manager' => 'Manager', 'admin' => 'Administrator'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('role') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label for="user-password" class="mb-1 block text-xs font-semibold uppercase text-gray-600">New password (optional)</label>
            <input id="user-password" type="password" name="password" minlength="8" autocomplete="new-password" class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="mb-6">
            <label for="user-password-confirmation" class="mb-1 block text-xs font-semibold uppercase text-gray-600">Confirm new password</label>
            <input id="user-password-confirmation" type="password" name="password_confirmation" minlength="8" autocomplete="new-password" class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
        </div>
        <div class="flex justify-between">
            <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
            <button type="submit" class="rounded bg-[#0369a1] px-5 py-2 text-sm font-semibold text-white hover:bg-[#075985]">Save Changes</button>
        </div>
    </form>
</div>
@endsection
