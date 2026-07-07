<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostLikeController extends Controller
{
    public function toggle(Request $request, Post $post): JsonResponse|RedirectResponse
    {
        abort_unless($post->is_published && $post->published_at?->isPast(), 404);

        $user = $request->user();
        $liked = $post->likes()->where('user_id', $user->id)->exists();

        if ($liked) {
            $post->likes()->detach($user->id);
            $liked = false;
        } else {
            $post->likes()->attach($user->id);
            $liked = true;
        }

        $count = $post->likes()->count();

        if ($request->expectsJson()) {
            return response()->json([
                'liked' => $liked,
                'count' => $count,
            ]);
        }

        return back();
    }
}
