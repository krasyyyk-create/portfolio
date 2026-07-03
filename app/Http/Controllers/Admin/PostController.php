<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        return view('admin.posts.index', [
            'posts' => Post::with(['author', 'categories'])->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.posts.create', [
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
            ->route('admin.posts.index')
            ->with('success', "Post \"{$post->title}\" created successfully.");
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.edit', [
            'post' => $post->load('categories'),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
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
            ->route('admin.posts.index')
            ->with('success', "Post \"{$post->title}\" updated successfully.");
    }

    public function destroy(Post $post): RedirectResponse
    {
        $title = $post->title;
        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', "Post \"{$title}\" deleted successfully.");
    }
}
