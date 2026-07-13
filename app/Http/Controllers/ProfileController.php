<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(User $user): View
    {
        $recentLikes = $user->likedPosts()
            ->published()
            ->with(['author', 'categories'])
            ->withCount(['comments', 'likes'])
            ->orderByPivot('created_at', 'desc')
            ->limit(12)
            ->get();

        $user->loadCount(['followers', 'following']);

        return view('profile.show', [
            'user' => $user,
            'recentLikes' => $recentLikes,
            'isOwner' => auth()->id() === $user->id,
            'isFollowing' => $user->isFollowedBy(auth()->user()),
        ]);
    }

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
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:2048'],
            'remove_avatar' => ['sometimes', 'boolean'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'],
            'remove_banner' => ['sometimes', 'boolean'],
        ]);

        $user->name = $validated['name'];
        $user->bio = $validated['bio'] ?? null;

        if ($request->boolean('remove_avatar')) {
            $user->deleteStoredAvatar();
            $user->avatar_path = null;
        } elseif ($request->hasFile('avatar')) {
            $user->storeUploadedAvatar($request->file('avatar'));
        }

        if ($request->boolean('remove_banner')) {
            $user->deleteStoredBanner();
            $user->banner_path = null;
        } elseif ($request->hasFile('banner')) {
            $user->storeUploadedBanner($request->file('banner'));
        }

        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    public function accountUpdate(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => $user->hasPassword() ? ['required', 'current_password'] : ['nullable'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Account info updated successfully.');
    }
}
