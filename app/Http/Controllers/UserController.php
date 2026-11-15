<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function edit(User $user)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'manager', 'warehouse', 'staff'])],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if ($user->hasRole('admin') && $validated['role'] !== 'admin'
            && User::query()->where('role', 'admin')->count() <= 1) {
            return redirect()->route('users.index')->with('error', 'You cannot remove the last administrator.');
        }
        $user->role = $validated['role'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);
        if (Auth::id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'You cannot delete the account you are currently using.');
        }

        if ($user->hasRole('admin') && User::query()->where('role', 'admin')->count() <= 1) {
            return redirect()->route('users.index')->with('error', 'You cannot remove the last administrator.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
