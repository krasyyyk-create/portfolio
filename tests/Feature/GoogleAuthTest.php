<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

uses(RefreshDatabase::class);

test('google redirect route redirects guests to google', function () {
    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('redirect')
        ->once()
        ->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

    Socialite::shouldReceive('driver')
        ->with('google')
        ->once()
        ->andReturn($provider);

    $response = $this->get(route('auth.google.redirect'));

    $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
});

test('google callback creates a new user and logs them in', function () {
    $googleUser = (new SocialiteUser)->setRaw([])->map([
        'id' => 'google-123',
        'name' => 'Google Operator',
        'email' => 'google@example.com',
    ]);

    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('user')
        ->once()
        ->andReturn($googleUser);

    Socialite::shouldReceive('driver')
        ->with('google')
        ->once()
        ->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('home'));
    $this->assertAuthenticated();

    $user = User::query()->where('email', 'google@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->google_id)->toBe('google-123')
        ->and($user->name)->toBe('Google Operator')
        ->and($user->role)->toBe(UserRole::User)
        ->and($user->password)->toBeNull();
});

test('google callback logs in existing user matched by email and links google id', function () {
    $existingUser = User::factory()->create([
        'email' => 'existing@example.com',
        'google_id' => null,
    ]);

    $googleUser = (new SocialiteUser)->setRaw([])->map([
        'id' => 'google-456',
        'name' => 'Updated Name',
        'email' => 'existing@example.com',
    ]);

    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('user')
        ->once()
        ->andReturn($googleUser);

    Socialite::shouldReceive('driver')
        ->with('google')
        ->once()
        ->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('home'));
    $this->assertAuthenticatedAs($existingUser->fresh());

    expect($existingUser->fresh()->google_id)->toBe('google-456');
});
