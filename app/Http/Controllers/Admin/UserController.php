<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::latest()->paginate(15),
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User {$user->name} updated successfully.");
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::enum(UserRole::class)],
        ]);

        $role = $validated['role'] instanceof UserRole
            ? $validated['role']
            : UserRole::from($validated['role']);

        if ($redirect = $this->guardAgainstSelfDemotion($user, $role)) {
            return $redirect;
        }

        $user->update(['role' => $role]);

        $message = match ($role) {
            UserRole::Admin => "{$user->name} is now an admin.",
            UserRole::User => "Admin access removed from {$user->name}.",
        };

        return back()->with('success', $message);
    }

    private function guardAgainstSelfDemotion(User $user, UserRole $role): ?RedirectResponse
    {
        if ($user->id === auth()->id() && $role !== UserRole::Admin) {
            return back()->withErrors(['role' => 'You cannot remove your own admin role.']);
        }

        return null;
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User {$name} deleted successfully.");
    }
}
