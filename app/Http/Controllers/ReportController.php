<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    public function storePost(Request $request, Post $post): RedirectResponse
    {
        abort_unless($post->is_published && $post->published_at?->isPast(), 404);

        return $this->store($request, $post, route('posts.show', $post));
    }

    public function storeComment(Request $request, Post $post, Comment $comment): RedirectResponse
    {
        abort_unless($post->is_published && $post->published_at?->isPast(), 404);
        abort_unless($comment->post_id === $post->id && ! $comment->is_hidden, 404);

        $fragment = $comment->parent_id
            ? 'comment-'.$comment->parent_id
            : 'comment-'.$comment->id;

        return $this->store($request, $comment, route('posts.show', $post).'#'.$fragment);
    }

    public function storeProfile(Request $request, User $user): RedirectResponse
    {
        return $this->store($request, $user, route('users.show', $user));
    }

    private function store(Request $request, Post|Comment|User $reportable, string $redirectTo): RedirectResponse
    {
        $user = $request->user();

        if ($reportable instanceof Post && $reportable->user_id === $user->id) {
            throw ValidationException::withMessages([
                'reason' => 'You cannot report your own post.',
            ]);
        }

        if ($reportable instanceof Comment && $reportable->user_id === $user->id) {
            throw ValidationException::withMessages([
                'reason' => 'You cannot report your own comment.',
            ]);
        }

        if ($reportable instanceof User && $reportable->is($user)) {
            throw ValidationException::withMessages([
                'reason' => 'You cannot report your own profile.',
            ]);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $alreadyReported = Report::query()
            ->where('user_id', $user->id)
            ->where('reportable_type', $reportable::class)
            ->where('reportable_id', $reportable->id)
            ->where('status', ReportStatus::Pending)
            ->exists();

        if ($alreadyReported) {
            throw ValidationException::withMessages([
                'reason' => 'You have already reported this content.',
            ]);
        }

        Report::create([
            'user_id' => $user->id,
            'reportable_type' => $reportable::class,
            'reportable_id' => $reportable->id,
            'reason' => $validated['reason'],
            'status' => ReportStatus::Pending,
        ]);

        return redirect($redirectTo)->with('success', 'Thank you. Your report has been submitted for review.');
    }
}
