<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests cannot follow users', function () {
    $user = User::factory()->create();

    $this->post(route('users.follow.toggle', $user))
        ->assertRedirect(route('login'));
});

test('authenticated users can follow another user', function () {
    $follower = User::factory()->create();
    $author = User::factory()->create();

    $this->actingAs($follower)
        ->postJson(route('users.follow.toggle', $author))
        ->assertOk()
        ->assertJson([
            'following' => true,
            'count' => 1,
        ]);

    $this->assertDatabaseHas('user_follows', [
        'follower_id' => $follower->id,
        'following_id' => $author->id,
    ]);
});

test('authenticated users can unfollow another user', function () {
    $follower = User::factory()->create();
    $author = User::factory()->create();
    $follower->following()->attach($author->id);

    $this->actingAs($follower)
        ->postJson(route('users.follow.toggle', $author))
        ->assertOk()
        ->assertJson([
            'following' => false,
            'count' => 0,
        ]);

    $this->assertDatabaseMissing('user_follows', [
        'follower_id' => $follower->id,
        'following_id' => $author->id,
    ]);
});

test('users cannot follow themselves', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson(route('users.follow.toggle', $user))
        ->assertForbidden();

    $this->assertDatabaseMissing('user_follows', [
        'follower_id' => $user->id,
        'following_id' => $user->id,
    ]);
});

test('following feed shows only posts from followed users', function () {
    $follower = User::factory()->create();
    $followedAuthor = User::factory()->create();
    $otherAuthor = User::factory()->create();

    $followedPost = Post::factory()->create([
        'user_id' => $followedAuthor->id,
        'title' => 'Followed Author Post',
    ]);
    Post::factory()->create([
        'user_id' => $otherAuthor->id,
        'title' => 'Other Author Post',
    ]);

    $follower->following()->attach($followedAuthor->id);

    $this->actingAs($follower)
        ->get(route('posts.index', ['feed' => 'following']))
        ->assertOk()
        ->assertSee('Followed Author Post')
        ->assertDontSee('Other Author Post');
});

test('following feed is empty when not following anyone', function () {
    $follower = User::factory()->create();
    Post::factory()->create(['title' => 'Some Published Post']);

    $this->actingAs($follower)
        ->get(route('posts.index', ['feed' => 'following']))
        ->assertOk()
        ->assertDontSee('Some Published Post')
        ->assertSee('No posts from people you follow yet');
});

test('guests are redirected to login when viewing following feed', function () {
    $this->get(route('posts.index', ['feed' => 'following']))
        ->assertRedirect(route('login'));
});

test('following feed excludes unpublished posts from followed users', function () {
    $follower = User::factory()->create();
    $author = User::factory()->create();

    Post::factory()->draft()->create([
        'user_id' => $author->id,
        'title' => 'Draft From Followed User',
    ]);

    $follower->following()->attach($author->id);

    $this->actingAs($follower)
        ->get(route('posts.index', ['feed' => 'following']))
        ->assertOk()
        ->assertDontSee('Draft From Followed User');
});
