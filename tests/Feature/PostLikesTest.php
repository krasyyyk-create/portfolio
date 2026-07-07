<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests cannot like posts', function () {
    $post = Post::factory()->create();

    $this->post(route('posts.like.toggle', $post))
        ->assertRedirect(route('login'));
});

test('authenticated users can like a published post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->postJson(route('posts.like.toggle', $post))
        ->assertOk()
        ->assertJson([
            'liked' => true,
            'count' => 1,
        ]);

    $this->assertDatabaseHas('post_likes', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
});

test('authenticated users can unlike a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $post->likes()->attach($user->id);

    $this->actingAs($user)
        ->postJson(route('posts.like.toggle', $post))
        ->assertOk()
        ->assertJson([
            'liked' => false,
            'count' => 0,
        ]);

    $this->assertDatabaseMissing('post_likes', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
});

test('users cannot like unpublished posts', function () {
    $user = User::factory()->create();
    $post = Post::factory()->draft()->create();

    $this->actingAs($user)
        ->postJson(route('posts.like.toggle', $post))
        ->assertNotFound();
});

test('post authors can like their own posts', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->postJson(route('posts.like.toggle', $post))
        ->assertOk()
        ->assertJson([
            'liked' => true,
            'count' => 1,
        ]);
});

test('posts index shows like counts', function () {
    $post = Post::factory()->create();
    $liker = User::factory()->create();
    $post->likes()->attach($liker->id);

    $this->get(route('posts.index'))
        ->assertOk()
        ->assertSee('1', false);
});

test('posts index sorts by likes descending', function () {
    $popular = Post::factory()->create(['title' => 'Popular Post']);
    $quiet = Post::factory()->create(['title' => 'Quiet Post']);

    $likers = User::factory()->count(3)->create();
    foreach ($likers as $liker) {
        $popular->likes()->attach($liker->id);
    }

    $response = $this->get(route('posts.index'));

    $response->assertOk();
    expect(strpos($response->content(), 'Popular Post'))
        ->toBeLessThan(strpos($response->content(), 'Quiet Post'));
});
