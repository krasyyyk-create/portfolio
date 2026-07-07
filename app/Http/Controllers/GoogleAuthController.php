<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class GoogleAuthController extends Controller
{
    public function redirect(): SymfonyRedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::query()
            ->where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            $user->fill([
                'google_id' => $googleUser->getId(),
                'name' => $googleUser->getName() ?? $user->name,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
            $user->save();
        } else {
            $user = User::create([
                'google_id' => $googleUser->getId(),
                'name' => $googleUser->getName() ?? 'Google User',
                'email' => $googleUser->getEmail(),
                'email_verified_at' => now(),
                'role' => UserRole::User,
            ]);
        }

        Auth::login($user, remember: true);

        request()->session()->regenerate();

        return redirect()->intended($this->redirectPath());
    }

    private function redirectPath(): string
    {
        return auth()->user()->isAdmin()
            ? route('admin.dashboard')
            : route('home');
    }
}
