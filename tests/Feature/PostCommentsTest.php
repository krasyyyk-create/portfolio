<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests cannot post comments', function () {
    $post = Post::factory()->create();

    $this->post(route('posts.comments.store', $post), [
        'body' => 'Great post!',
    ])->assertRedirect(route('login'));
});

test('authenticated users can comment on published posts', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->post(route('posts.comments.store', $post), [
            'body' => 'Great post!',
        ])
        ->assertRedirect(route('posts.show', $post).'#comments');

    $this->assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'user_id' => $user->id,
        'body' => 'Great post!',
    ]);
});

test('users cannot comment on unpublished posts', function () {
    $user = User::factory()->create();
    $post = Post::factory()->draft()->create();

    $this->actingAs($user)
        ->post(route('posts.comments.store', $post), [
            'body' => 'Great post!',
        ])
        ->assertNotFound();
});

test('comment authors can delete their comments', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->delete(route('posts.comments.destroy', [$post, $comment]))
        ->assertRedirect(route('posts.show', $post).'#comments');

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

test('post authors can delete comments on their posts', function () {
    $author = User::factory()->create();
    $commenter = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $commenter->id,
    ]);

    $this->actingAs($author)
        ->delete(route('posts.comments.destroy', [$post, $comment]))
        ->assertRedirect(route('posts.show', $post).'#comments');

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

test('admins can delete any comment', function () {
    $admin = User::factory()->admin()->create();
    $commenter = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $commenter->id,
    ]);

    $this->actingAs($admin)
        ->delete(route('posts.comments.destroy', [$post, $comment]))
        ->assertRedirect(route('posts.show', $post).'#comments');

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

test('users cannot delete comments they do not own', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $otherUser->id]);
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $otherUser->id,
    ]);

    $this->actingAs($user)
        ->delete(route('posts.comments.destroy', [$post, $comment]))
        ->assertForbidden();

    $this->assertDatabaseHas('comments', ['id' => $comment->id]);
});

test('published post page shows comments', function () {
    $post = Post::factory()->create();
    Comment::factory()->create([
        'post_id' => $post->id,
        'body' => 'Nice write-up.',
    ]);

    $this->get(route('posts.show', $post))
        ->assertOk()
        ->assertSee('Nice write-up.')
        ->assertSee('Comments');
});
