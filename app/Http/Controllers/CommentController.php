<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    public function store(Request $request, Post $post): RedirectResponse
    {
        abort_unless($post->is_published && $post->published_at?->isPast(), 404);

        $validated = $request->validate([
            'body' => ['nullable', 'string', 'max:2000', 'required_without:image'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('comments', 'id')->where(fn ($query) => $query->where('post_id', $post->id)),
            ],
        ]);

        $body = trim($validated['body'] ?? '');

        if ($this->commentBodyIsEmpty($body) && ! $request->hasFile('image')) {
            throw ValidationException::withMessages([
                'body' => 'Add a comment or attach an image.',
            ]);
        }

        $comment = $post->comments()->make([
            'user_id' => $request->user()->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'body' => $body,
        ]);

        if ($request->hasFile('image')) {
            $comment->storeUploadedImage($request->file('image'));
        }

        $comment->save();

        $fragment = $comment->parent_id
            ? 'comment-'.$comment->parent_id
            : 'comments';

        return redirect()
            ->route('posts.show', $post)
            ->withFragment($fragment)
            ->with('success', $comment->parent_id ? 'Reply posted.' : 'Comment posted.');
    }

    public function destroy(Post $post, Comment $comment): RedirectResponse
    {
        abort_unless($comment->post_id === $post->id, 404);

        $this->authorize('delete', $comment);

        $comment->delete();

        return redirect()
            ->route('posts.show', $post)
            ->withFragment('comments')
            ->with('success', 'Comment removed.');
    }

    private function commentBodyIsEmpty(string $body): bool
    {
        return trim(strip_tags($body)) === '';
    }
}
