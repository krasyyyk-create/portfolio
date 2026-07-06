<?php

use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admins can pin posts with a duration and unpin them', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $post = Post::factory()->create(['is_pinned' => false]);

    $this->actingAs($admin)
        ->post(route('admin.posts.toggle-pin', $post), [
            'pin_duration_days' => 7,
        ])
        ->assertRedirect(route('admin.posts.index'));

    $post->refresh();
    expect($post->is_pinned)->toBeTrue();
    expect($post->pinned_at)->not->toBeNull();
    expect($post->pinned_until->isAfter(now()->addDays(6)))->toBeTrue();
    expect($post->pinned_until->isBefore(now()->addDays(8)))->toBeTrue();

    $this->actingAs($admin)
        ->post(route('admin.posts.toggle-pin', $post))
        ->assertRedirect(route('admin.posts.index'));

    $post->refresh();
    expect($post->is_pinned)->toBeFalse();
    expect($post->pinned_at)->toBeNull();
    expect($post->pinned_until)->toBeNull();
});

test('pin duration cannot exceed one month', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $post = Post::factory()->create();

    $this->actingAs($admin)
        ->post(route('admin.posts.toggle-pin', $post), [
            'pin_duration_days' => 31,
        ])
        ->assertSessionHasErrors('pin_duration_days');
});

test('non-admins cannot pin posts', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->post(route('admin.posts.toggle-pin', $post), [
            'pin_duration_days' => 7,
        ])
        ->assertForbidden();
});

test('pinned posts appear first on the public posts index', function () {
    $olderPinned = Post::factory()->pinned()->create([
        'title' => 'Pinned Architecture Guide',
        'published_at' => now()->subDays(10),
    ]);
    $newerRegular = Post::factory()->create([
        'title' => 'Latest Release Notes',
        'published_at' => now()->subDay(),
    ]);

    $response = $this->get(route('posts.index'));

    $response->assertOk();
    expect($response->content())->toContain('Pinned Architecture Guide');
    expect($response->content())->toContain('Latest Release Notes');
    expect(strpos($response->content(), 'Pinned Architecture Guide'))
        ->toBeLessThan(strpos($response->content(), 'Latest Release Notes'));
});

test('expired pins no longer appear first or show pinned badge', function () {
    Post::factory()->pinExpired()->create([
        'title' => 'Expired Pin Post',
        'published_at' => now()->subDays(10),
    ]);
    Post::factory()->create([
        'title' => 'Fresh Regular Post',
        'published_at' => now()->subDay(),
    ]);

    $response = $this->get(route('posts.index'));

    $response->assertOk();
    expect(strpos($response->content(), 'Fresh Regular Post'))
        ->toBeLessThan(strpos($response->content(), 'Expired Pin Post'));
    $response->assertDontSee('pinned', false);
});

test('pinned badge is shown on public post cards', function () {
    Post::factory()->pinned()->create(['title' => 'Featured Post']);

    $this->get(route('posts.index'))
        ->assertOk()
        ->assertSee('pinned', false);
});
