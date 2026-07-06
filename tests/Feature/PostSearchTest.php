<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('posts index can be searched by title', function () {
    $matchingPost = Post::factory()->create(['title' => 'Kubernetes Edge Routing']);
    Post::factory()->create(['title' => 'Unrelated Database Notes']);

    $this->get(route('posts.index', ['q' => 'kubernetes']))
        ->assertOk()
        ->assertSee('Kubernetes Edge Routing')
        ->assertDontSee('Unrelated Database Notes');
});

test('posts index can be searched by creator name', function () {
    $author = User::factory()->create(['name' => 'Ada Lovelace']);
    $otherAuthor = User::factory()->create(['name' => 'Grace Hopper']);

    Post::factory()->create(['user_id' => $author->id, 'title' => 'Analytical Engine Notes']);
    Post::factory()->create(['user_id' => $otherAuthor->id, 'title' => 'Compiler Design']);

    $this->get(route('posts.index', ['q' => 'ada']))
        ->assertOk()
        ->assertSee('Analytical Engine Notes')
        ->assertDontSee('Compiler Design');
});

test('posts index can be searched by category name', function () {
    $category = Category::factory()->create(['name' => 'Cloud Architecture']);
    $otherCategory = Category::factory()->create(['name' => 'Frontend']);

    $matchingPost = Post::factory()->create(['title' => 'Multi-region Deployments']);
    $otherPost = Post::factory()->create(['title' => 'CSS Grid Patterns']);

    $matchingPost->categories()->attach($category);
    $otherPost->categories()->attach($otherCategory);

    $this->get(route('posts.index', ['q' => 'cloud']))
        ->assertOk()
        ->assertSee('Multi-region Deployments')
        ->assertDontSee('CSS Grid Patterns');
});

test('post search can be combined with category filter', function () {
    $category = Category::factory()->create(['name' => 'DevOps', 'slug' => 'devops']);
    $otherCategory = Category::factory()->create(['name' => 'Security', 'slug' => 'security']);

    $matchingPost = Post::factory()->create(['title' => 'Pipeline Automation']);
    $wrongCategoryPost = Post::factory()->create(['title' => 'Pipeline Security']);
    $otherPost = Post::factory()->create(['title' => 'Vault Setup']);

    $matchingPost->categories()->attach($category);
    $wrongCategoryPost->categories()->attach($otherCategory);
    $otherPost->categories()->attach($category);

    $this->get(route('posts.index', ['q' => 'pipeline', 'category' => 'devops']))
        ->assertOk()
        ->assertSee('Pipeline Automation')
        ->assertDontSee('Pipeline Security')
        ->assertDontSee('Vault Setup');
});

test('draft posts are excluded from search results', function () {
    Post::factory()->create(['title' => 'Published Kubernetes Guide']);
    Post::factory()->draft()->create(['title' => 'Draft Kubernetes Guide']);

    $this->get(route('posts.index', ['q' => 'kubernetes']))
        ->assertOk()
        ->assertSee('Published Kubernetes Guide')
        ->assertDontSee('Draft Kubernetes Guide');
});
