<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admins can grant admin access to other users', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $user = User::factory()->create(['role' => UserRole::User]);

    $this->actingAs($admin)
        ->patch(route('admin.users.update-role', $user), [
            'role' => UserRole::Admin->value,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($user->refresh()->role)->toBe(UserRole::Admin);
});

test('admins can revoke admin access from other users', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $otherAdmin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->patch(route('admin.users.update-role', $otherAdmin), [
            'role' => UserRole::User->value,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($otherAdmin->refresh()->role)->toBe(UserRole::User);
});

test('admins cannot remove their own admin role', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->patch(route('admin.users.update-role', $admin), [
            'role' => UserRole::User->value,
        ])
        ->assertRedirect()
        ->assertSessionHasErrors('role');

    expect($admin->refresh()->role)->toBe(UserRole::Admin);
});

test('non-admins cannot change user roles', function () {
    $user = User::factory()->create(['role' => UserRole::User]);
    $target = User::factory()->create(['role' => UserRole::User]);

    $this->actingAs($user)
        ->patch(route('admin.users.update-role', $target), [
            'role' => UserRole::Admin->value,
        ])
        ->assertForbidden();

    expect($target->refresh()->role)->toBe(UserRole::User);
});

test('admins can grant admin from the edit page', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $user = User::factory()->create(['role' => UserRole::User]);

    $this->actingAs($admin)
        ->get(route('admin.users.edit', $user))
        ->assertOk()
        ->assertSee('make admin');

    $this->actingAs($admin)
        ->patch(route('admin.users.update-role', $user), [
            'role' => UserRole::Admin->value,
        ])
        ->assertRedirect(route('admin.users.edit', $user))
        ->assertSessionHas('success');

    expect($user->refresh()->role)->toBe(UserRole::Admin);
});
