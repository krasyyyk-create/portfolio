<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostsController extends Controller
{
    public function index(): View
    {
        $activeCategory = null;

        if ($categorySlug = request('category')) {
            $activeCategory = Category::where('slug', $categorySlug)->firstOrFail();
        }

        $posts = Post::published()
            ->with(['author', 'categories'])
            ->withCount('comments')
            ->when($activeCategory, fn ($query) => $query->whereHas(
                'categories',
                fn ($categoryQuery) => $categoryQuery->where('categories.id', $activeCategory->id)
            ))
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('posts.index', [
            'posts' => $posts,
            'categories' => Category::withCount(['posts' => fn ($query) => $query->published()])->orderBy('name')->get(),
            'activeCategory' => $activeCategory,
        ]);
    }

    public function show(Post $post): View
    {
        abort_unless($post->is_published && $post->published_at?->isPast(), 404);

        return view('posts.show', [
            'post' => $post->load([
                'author',
                'categories',
                'comments' => fn ($query) => $query->with('author'),
            ]),
        ]);
    }

    public function mine(): View
    {
        $posts = auth()->user()
            ->posts()
            ->with('categories')
            ->latest()
            ->paginate(12);

        return view('posts.mine', compact('posts'));
    }

    public function create(): View
    {
        return view('posts.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = Post::validateInput($request);

        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => Post::resolveUniqueSlug($validated['slug'] ?? null, $validated['title']),
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->boolean('is_published') ? now() : null,
        ]);

        if ($request->hasFile('image')) {
            $post->storeUploadedImage($request->file('image'));
            $post->save();
        }

        $post->categories()->sync($validated['category_ids'] ?? []);

        return redirect()
            ->route('posts.mine')
            ->with('success', "Post \"{$post->title}\" created successfully.");
    }

    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        return view('posts.edit', [
            'post' => $post->load('categories'),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $validated = Post::validateInput($request, $post);

        $isPublished = $request->boolean('is_published');

        $post->fill([
            'title' => $validated['title'],
            'slug' => Post::resolveUniqueSlug($validated['slug'] ?? null, $validated['title'], $post->id),
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'is_published' => $isPublished,
            'published_at' => $isPublished
                ? ($post->published_at ?? now())
                : null,
        ]);

        if ($request->boolean('remove_image')) {
            $post->deleteStoredImage();
            $post->image_path = null;
        } elseif ($request->hasFile('image')) {
            $post->storeUploadedImage($request->file('image'));
        }

        $post->save();

        $post->categories()->sync($validated['category_ids'] ?? []);

        return redirect()
            ->route('posts.mine')
            ->with('success', "Post \"{$post->title}\" updated successfully.");
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $title = $post->title;
        $post->delete();

        return redirect()
            ->route('posts.mine')
            ->with('success', "Post \"{$title}\" deleted successfully.");
    }
}
