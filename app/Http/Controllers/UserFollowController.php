<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserFollowController extends Controller
{
    public function toggle(Request $request, User $user): JsonResponse|RedirectResponse
    {
        $follower = $request->user();

        abort_if($follower->id === $user->id, 403);

        $following = $follower->following()->where('users.id', $user->id)->exists();

        if ($following) {
            $follower->following()->detach($user->id);
            $following = false;
        } else {
            $follower->following()->attach($user->id);
            $following = true;
        }

        $count = $user->followers()->count();

        if ($request->expectsJson()) {
            return response()->json([
                'following' => $following,
                'count' => $count,
            ]);
        }

        return back();
    }
}
