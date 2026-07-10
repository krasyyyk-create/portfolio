<?php

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin posts index can be searched by title', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Post::factory()->create(['title' => 'Kubernetes Edge Routing']);
    Post::factory()->create(['title' => 'Unrelated Database Notes']);

    $this->actingAs($admin)
        ->get(route('admin.posts.index', ['q' => 'kubernetes']))
        ->assertOk()
        ->assertSee('Kubernetes Edge Routing')
        ->assertDontSee('Unrelated Database Notes');
});

test('admin posts index can be searched by author name', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $author = User::factory()->create(['name' => 'Ada Lovelace']);
    $otherAuthor = User::factory()->create(['name' => 'Grace Hopper']);

    Post::factory()->create(['user_id' => $author->id, 'title' => 'Analytical Engine Notes']);
    Post::factory()->create(['user_id' => $otherAuthor->id, 'title' => 'Compiler Design']);

    $this->actingAs($admin)
        ->get(route('admin.posts.index', ['q' => 'ada']))
        ->assertOk()
        ->assertSee('Analytical Engine Notes')
        ->assertDontSee('Compiler Design');
});

test('admin posts index can be searched by category name', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $category = Category::factory()->create(['name' => 'Cloud Architecture']);
    $otherCategory = Category::factory()->create(['name' => 'Frontend']);

    $matchingPost = Post::factory()->create(['title' => 'Multi-region Deployments']);
    $otherPost = Post::factory()->create(['title' => 'CSS Grid Patterns']);

    $matchingPost->categories()->attach($category);
    $otherPost->categories()->attach($otherCategory);

    $this->actingAs($admin)
        ->get(route('admin.posts.index', ['q' => 'cloud']))
        ->assertOk()
        ->assertSee('Multi-region Deployments')
        ->assertDontSee('CSS Grid Patterns');
});

test('admin post search includes draft posts', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Post::factory()->create(['title' => 'Published Kubernetes Guide']);
    Post::factory()->draft()->create(['title' => 'Draft Kubernetes Guide']);

    $this->actingAs($admin)
        ->get(route('admin.posts.index', ['q' => 'kubernetes']))
        ->assertOk()
        ->assertSee('Published Kubernetes Guide')
        ->assertSee('Draft Kubernetes Guide');
});

test('non-admins cannot access admin post search', function () {
    $user = User::factory()->create(['role' => UserRole::User]);

    Post::factory()->create(['title' => 'Kubernetes Edge Routing']);

    $this->actingAs($user)
        ->get(route('admin.posts.index', ['q' => 'kubernetes']))
        ->assertForbidden();
});
