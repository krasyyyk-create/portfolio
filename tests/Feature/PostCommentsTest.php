<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Services\CommentImageProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

test('authenticated users can attach an image to a comment', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $post = Post::factory()->create();
    $image = UploadedFile::fake()->image('screenshot.jpg', 960, 640);

    $this->actingAs($user)
        ->post(route('posts.comments.store', $post), [
            'body' => 'Check this out.',
            'image' => $image,
        ])
        ->assertRedirect(route('posts.show', $post).'#comments');

    $comment = Comment::query()->first();

    expect($comment)->not->toBeNull()
        ->and($comment->body)->toBe('Check this out.')
        ->and($comment->image_path)->not->toBeNull()
        ->and($comment->image_width)->toBe(CommentImageProcessor::MAX_WIDTH)
        ->and($comment->image_height)->toBe(CommentImageProcessor::MAX_HEIGHT);

    Storage::disk('public')->assertExists($comment->image_path);
});

test('small comment images are stored without upscaling', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $post = Post::factory()->create();
    $image = UploadedFile::fake()->image('small.jpg', 240, 160);

    $this->actingAs($user)
        ->post(route('posts.comments.store', $post), [
            'body' => 'Small attachment.',
            'image' => $image,
        ])
        ->assertRedirect();

    $comment = Comment::query()->first();

    expect($comment->image_width)->toBe(240)
        ->and($comment->image_height)->toBe(160);
});

test('users can post an image-only comment', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $post = Post::factory()->create();
    $image = UploadedFile::fake()->image('photo.png', 300, 200);

    $this->actingAs($user)
        ->post(route('posts.comments.store', $post), [
            'image' => $image,
        ])
        ->assertRedirect(route('posts.show', $post).'#comments');

    $this->assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'user_id' => $user->id,
        'body' => '',
    ]);
});

test('invalid comment images are rejected', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->post(route('posts.comments.store', $post), [
            'body' => 'Bad file.',
            'image' => UploadedFile::fake()->create('notes.txt', 100, 'text/plain'),
        ])
        ->assertSessionHasErrors('image');
});

test('deleting a comment removes its stored image', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $post = Post::factory()->create();
    $image = UploadedFile::fake()->image('attachment.jpg', 300, 200);

    $this->actingAs($user)
        ->post(route('posts.comments.store', $post), [
            'body' => 'Temporary image.',
            'image' => $image,
        ]);

    $comment = Comment::query()->first();
    $path = $comment->image_path;

    Storage::disk('public')->assertExists($path);

    $this->actingAs($user)
        ->delete(route('posts.comments.destroy', [$post, $comment]))
        ->assertRedirect(route('posts.show', $post).'#comments');

    Storage::disk('public')->assertMissing($path);
});
