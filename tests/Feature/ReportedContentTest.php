<?php

use App\Enums\ReportResolution;
use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Models\Comment;
use App\Models\ModerationNotification;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated users can report a post', function () {
    $reporter = User::factory()->create();
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);

    $this->actingAs($reporter)
        ->post(route('posts.report', $post), [
            'reason' => 'This post contains inappropriate content.',
        ])
        ->assertRedirect(route('posts.show', $post));

    $this->assertDatabaseHas('reports', [
        'user_id' => $reporter->id,
        'reportable_type' => Post::class,
        'reportable_id' => $post->id,
        'status' => ReportStatus::Pending->value,
    ]);
});

test('users cannot report their own posts', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('posts.report', $post), [
            'reason' => 'This should not be allowed at all.',
        ])
        ->assertSessionHasErrors('reason');
});

test('admins can draft reported posts and notify the author', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $author = User::factory()->create();
    $reporter = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);

    $report = Report::create([
        'user_id' => $reporter->id,
        'reportable_type' => Post::class,
        'reportable_id' => $post->id,
        'reason' => 'Spam content in the post body.',
        'status' => ReportStatus::Pending,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.reported.draft', $report), [
            'moderation_reason' => 'Violates community guidelines.',
        ])
        ->assertRedirect(route('admin.reported.index', ['type' => 'all']));

    $post->refresh();
    expect($post->is_published)->toBeFalse();

    $this->assertDatabaseHas('moderation_notifications', [
        'user_id' => $author->id,
        'report_id' => $report->id,
        'content_type' => 'post',
        'reason' => 'Violates community guidelines.',
    ]);
});

test('admins can delete reported comments', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $post = Post::factory()->create();
    $comment = Comment::factory()->create(['post_id' => $post->id]);
    $reporter = User::factory()->create();

    $report = Report::create([
        'user_id' => $reporter->id,
        'reportable_type' => Comment::class,
        'reportable_id' => $comment->id,
        'reason' => 'Harassing language in this comment.',
        'status' => ReportStatus::Pending,
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.reported.destroy', $report))
        ->assertRedirect(route('admin.reported.index', ['type' => 'all']));

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

test('drafting a reported comment hides it and notifies the author', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $author = User::factory()->create();
    $reporter = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $author->id,
        'body' => 'Offensive reply here.',
    ]);

    $report = Report::create([
        'user_id' => $reporter->id,
        'reportable_type' => Comment::class,
        'reportable_id' => $comment->id,
        'reason' => 'Offensive language used in comment.',
        'status' => ReportStatus::Pending,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.reported.draft', $report))
        ->assertRedirect(route('admin.reported.index', ['type' => 'all']));

    $comment->refresh();
    expect($comment->is_hidden)->toBeTrue();

    expect(ModerationNotification::where('user_id', $author->id)->count())->toBe(1);
});

test('authors see moderation notice popup on site pages', function () {
    $author = User::factory()->create();
    $reporter = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);

    $report = Report::create([
        'user_id' => $reporter->id,
        'reportable_type' => Post::class,
        'reportable_id' => $post->id,
        'reason' => 'Spam.',
        'status' => ReportStatus::Pending,
    ]);

    ModerationNotification::create([
        'user_id' => $author->id,
        'report_id' => $report->id,
        'content_type' => 'post',
        'content_label' => $post->title,
        'reason' => 'Violates community guidelines.',
    ]);

    $this->actingAs($author)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Content moderation notice', false)
        ->assertSee('Violates community guidelines.', false);
});

test('admins can dismiss reports without changing content', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $author = User::factory()->create();
    $reporter = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);

    $report = Report::create([
        'user_id' => $reporter->id,
        'reportable_type' => Post::class,
        'reportable_id' => $post->id,
        'reason' => 'False alarm report.',
        'status' => ReportStatus::Pending,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.reported.dismiss', $report))
        ->assertRedirect(route('admin.reported.index', ['type' => 'all']));

    $report->refresh();
    expect($report->status)->toBe(ReportStatus::Resolved);
    expect($report->resolution)->toBe(ReportResolution::NoAction);

    $post->refresh();
    expect($post->is_published)->toBeTrue();
});

test('non-admins cannot access reported content admin page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.reported.index'))
        ->assertForbidden();
});

test('authenticated users can report another users profile', function () {
    $reporter = User::factory()->create();
    $reported = User::factory()->create(['bio' => 'Suspicious profile bio']);

    $this->actingAs($reporter)
        ->post(route('users.report', $reported), [
            'reason' => 'This profile is impersonating someone else.',
        ])
        ->assertRedirect(route('users.show', $reported));

    $this->assertDatabaseHas('reports', [
        'user_id' => $reporter->id,
        'reportable_type' => User::class,
        'reportable_id' => $reported->id,
        'status' => ReportStatus::Pending->value,
    ]);
});

test('users cannot report their own profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('users.report', $user), [
            'reason' => 'This should not be allowed at all.',
        ])
        ->assertSessionHasErrors('reason');
});

test('admins can filter reported content by posts', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $reporter = User::factory()->create();
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);
    $comment = Comment::factory()->create(['post_id' => $post->id, 'body' => 'Offensive comment text']);

    Report::create([
        'user_id' => $reporter->id,
        'reportable_type' => Post::class,
        'reportable_id' => $post->id,
        'reason' => 'Spam post content here.',
        'status' => ReportStatus::Pending,
    ]);

    Report::create([
        'user_id' => $reporter->id,
        'reportable_type' => Comment::class,
        'reportable_id' => $comment->id,
        'reason' => 'Offensive comment content here.',
        'status' => ReportStatus::Pending,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.reported.index', ['type' => 'posts']))
        ->assertOk()
        ->assertSee('Spam post content here.', false)
        ->assertDontSee('Offensive comment content here.', false);
});

test('admins can filter reported content by comments', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $reporter = User::factory()->create();
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);
    $comment = Comment::factory()->create(['post_id' => $post->id, 'body' => 'Offensive comment text']);

    Report::create([
        'user_id' => $reporter->id,
        'reportable_type' => Post::class,
        'reportable_id' => $post->id,
        'reason' => 'Spam post content here.',
        'status' => ReportStatus::Pending,
    ]);

    Report::create([
        'user_id' => $reporter->id,
        'reportable_type' => Comment::class,
        'reportable_id' => $comment->id,
        'reason' => 'Offensive comment content here.',
        'status' => ReportStatus::Pending,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.reported.index', ['type' => 'comments']))
        ->assertOk()
        ->assertSee('Offensive comment content here.', false)
        ->assertDontSee('Spam post content here.', false);
});

test('admins can filter reported content by profiles', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $reporter = User::factory()->create();
    $author = User::factory()->create();
    $reportedUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);

    Report::create([
        'user_id' => $reporter->id,
        'reportable_type' => Post::class,
        'reportable_id' => $post->id,
        'reason' => 'Spam post content here.',
        'status' => ReportStatus::Pending,
    ]);

    Report::create([
        'user_id' => $reporter->id,
        'reportable_type' => User::class,
        'reportable_id' => $reportedUser->id,
        'reason' => 'Offensive profile content here.',
        'status' => ReportStatus::Pending,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.reported.index', ['type' => 'profiles']))
        ->assertOk()
        ->assertSee('Offensive profile content here.', false)
        ->assertDontSee('Spam post content here.', false);
});

test('admins can clear reported profiles and notify the user', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $reported = User::factory()->create([
        'bio' => 'Offensive bio text',
        'avatar_path' => 'avatars/test.jpg',
        'banner_path' => 'banners/test.jpg',
    ]);
    $reporter = User::factory()->create();

    $report = Report::create([
        'user_id' => $reporter->id,
        'reportable_type' => User::class,
        'reportable_id' => $reported->id,
        'reason' => 'Inappropriate profile content.',
        'status' => ReportStatus::Pending,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.reported.draft', $report), [
            'moderation_reason' => 'Profile violates community guidelines.',
            'type' => 'profiles',
        ])
        ->assertRedirect(route('admin.reported.index', ['type' => 'profiles']));

    $reported->refresh();
    expect($reported->bio)->toBeNull();
    expect($reported->avatar_path)->toBeNull();
    expect($reported->banner_path)->toBeNull();

    $this->assertDatabaseHas('moderation_notifications', [
        'user_id' => $reported->id,
        'report_id' => $report->id,
        'content_type' => 'profile',
        'reason' => 'Profile violates community guidelines.',
    ]);
});
