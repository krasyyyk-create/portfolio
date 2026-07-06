<?php

namespace App\Http\Controllers;

use App\Models\ModerationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ModerationNotificationController extends Controller
{
    public function markRead(Request $request, ModerationNotification $notification): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        $notification->markAsRead();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }
}
