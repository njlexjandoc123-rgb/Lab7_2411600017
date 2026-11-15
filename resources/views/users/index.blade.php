@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Users</h1>
        <p class="text-sm text-gray-500 mt-1">Manage registered accounts in the student portal.</p>
    </div>
    <button type="submit" form="add-user-form" class="bg-[#0369a1] hover:bg-[#075985] text-white px-4 py-2 rounded shadow text-sm font-semibold">+ Add User</button>
</div>

@if(session('success'))
    <div class="mb-4 rounded bg-emerald-100 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 rounded bg-red-100 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
@endif

<form id="add-user-form" action="{{ route('users.store') }}" method="POST" class="mb-6 grid grid-cols-1 gap-4 rounded-lg bg-white p-4 shadow md:grid-cols-2">
    @csrf
    <div>
        <label for="user-name" class="mb-1 block text-xs font-semibold uppercase text-gray-600">Name</label>
        <input id="user-name" type="text" name="name" value="{{ old('name') }}" maxlength="255" required class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="user-email" class="mb-1 block text-xs font-semibold uppercase text-gray-600">Email</label>
        <input id="user-email" type="email" name="email" value="{{ old('email') }}" maxlength="255" required class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="user-password" class="mb-1 block text-xs font-semibold uppercase text-gray-600">Password</label>
        <input id="user-password" type="password" name="password" minlength="8" required autocomplete="new-password" class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="user-password-confirmation" class="mb-1 block text-xs font-semibold uppercase text-gray-600">Confirm password</label>
        <input id="user-password-confirmation" type="password" name="password_confirmation" minlength="8" required autocomplete="new-password" class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
    </div>
    <div>
        <label for="user-role" class="mb-1 block text-xs font-semibold uppercase text-gray-600">Role</label>
        <select id="user-role" name="role" required class="w-full rounded border px-3 py-2 text-sm focus:ring-2 focus:ring-sky-700">
            @foreach(['staff' => 'Staff', 'warehouse' => 'Warehouse staff', 'manager' => 'Manager', 'admin' => 'Administrator'] as $value => $label)
                <option value="{{ $value }}" @selected(old('role', 'staff') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-sky-950 text-white text-xs uppercase tracking-wider">
                <th class="py-3 px-4">Name</th>
                <th class="py-3 px-4">Email</th>
                <th class="py-3 px-4">Role</th>
                <th class="py-3 px-4">Registered</th>
                <th class="py-3 px-4 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 text-sm">
            @forelse($users as $user)
            <tr>
                <td class="py-3 px-4 font-semibold text-gray-800">{{ $user->name }}</td>
                <td class="py-3 px-4">{{ $user->email }}</td>
                <td class="py-3 px-4 capitalize">{{ $user->role }}</td>
                <td class="py-3 px-4">{{ $user->created_at?->format('M d, Y') ?? '—' }}</td>
                <td class="space-x-1 whitespace-nowrap py-3 px-4 text-center">
                    <a href="{{ route('users.edit', $user) }}" class="inline-block rounded bg-gray-100 px-2 py-1 text-xs hover:bg-gray-200">Edit</a>
                    @if(auth()->id() !== $user->id)
                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Delete this user account?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded bg-red-50 px-2 py-1 text-xs text-red-700 hover:bg-red-100">Delete</button>
                    </form>
                    @else
                    <span class="text-xs text-gray-400">Current user</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-6 px-4 text-center text-gray-500">
                    No users found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 bg-gray-50 border-t flex justify-between items-center text-xs text-gray-500">
        <span>
            @if($users->total() > 0)
                Showing {{ $users->firstItem() }}-{{ $users->lastItem() }} of {{ $users->total() }} users
            @else
                Showing 0 of 0 users
            @endif
        </span>
        {{ $users->links() }}
    </div>
</div>
@endsection
