<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(): View
    {
        return view('admin.chat.index');
    }

    public function messages(Request $request): JsonResponse
    {
        $afterId = $request->integer('after');

        $messages = AdminChatMessage::query()
            ->with('sender')
            ->when($afterId > 0, fn ($query) => $query->where('id', '>', $afterId))
            ->orderBy('created_at')
            ->when($afterId > 0, fn ($query) => $query->limit(100), fn ($query) => $query->limit(200))
            ->get()
            ->map(fn (AdminChatMessage $message) => $message->toChatArray());

        return response()->json(['messages' => $messages]);
    }

    public function storeMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'body' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:5120'],
        ]);

        $body = trim((string) ($validated['body'] ?? ''));

        if ($body === '' && ! $request->hasFile('image')) {
            return response()->json([
                'message' => 'Enter a message or attach an image.',
                'errors' => ['body' => ['Enter a message or attach an image.']],
            ], 422);
        }

        $body = $body === '' ? null : $body;

        $message = AdminChatMessage::create([
            'user_id' => auth()->id(),
            'body' => $body,
        ]);

        if ($request->hasFile('image')) {
            $message->storeUploadedImage($request->file('image'));
            $message->save();
        }

        $message->load('sender');

        return response()->json([
            'message' => $message->toChatArray(),
        ], 201);
    }
}
