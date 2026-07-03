<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:2048'],
            'remove_avatar' => ['sometimes', 'boolean'],
        ]);

        $user->name = $validated['name'];

        if ($request->boolean('remove_avatar')) {
            $user->deleteStoredAvatar();
            $user->avatar_path = null;
        } elseif ($request->hasFile('avatar')) {
            $user->storeUploadedAvatar($request->file('avatar'));
        }

        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    public function accountEdit(): View
    {
        return view('profile.account', [
            'user' => auth()->user(),
        ]);
    }

    public function accountUpdate(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => ['required', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('account.edit')
            ->with('success', 'Account info updated successfully.');
    }
}
