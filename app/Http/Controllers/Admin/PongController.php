<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Minigame;
use App\Enums\PongGameStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\PongGame;
use App\Models\User;
use App\Services\MinigameLeaderboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PongController extends Controller
{
    public function __construct(
        private MinigameLeaderboardService $leaderboard,
    ) {}
    public function admins(): JsonResponse
    {
        $admins = User::query()
            ->where('role', UserRole::Admin)
            ->where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name', 'avatar_path'])
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar_url' => $user->avatar_url,
            ]);

        return response()->json(['admins' => $admins]);
    }

    public function invites(): JsonResponse
    {
        $userId = auth()->id();

        $incoming = PongGame::query()
            ->with(['playerLeft', 'playerRight'])
            ->where('player_right_id', $userId)
            ->where('status', PongGameStatus::Pending)
            ->latest()
            ->get()
            ->map(fn (PongGame $game) => $game->toInviteArray($userId));

        $outgoing = PongGame::query()
            ->with(['playerLeft', 'playerRight'])
            ->where('player_left_id', $userId)
            ->where('status', PongGameStatus::Pending)
            ->latest()
            ->get()
            ->map(fn (PongGame $game) => $game->toInviteArray($userId));

        return response()->json([
            'incoming' => $incoming,
            'outgoing' => $outgoing,
        ]);
    }

    public function invite(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'opponent_id' => ['required', 'integer', 'exists:users,id', 'not_in:'.auth()->id()],
        ]);

        PongGame::assertInviteeIsAdmin($validated['opponent_id']);

        $existing = PongGame::query()
            ->where('status', PongGameStatus::Pending)
            ->where(function ($query) use ($validated) {
                $query->where(function ($pair) use ($validated) {
                    $pair->where('player_left_id', auth()->id())
                        ->where('player_right_id', $validated['opponent_id']);
                })->orWhere(function ($pair) use ($validated) {
                    $pair->where('player_left_id', $validated['opponent_id'])
                        ->where('player_right_id', auth()->id());
                });
            })
            ->first();

        if ($existing) {
            return response()->json([
                'game' => $existing->load(['playerLeft', 'playerRight'])->toGameArray(),
                'invite' => $existing->toInviteArray(),
            ]);
        }

        $game = PongGame::create([
            'player_left_id' => auth()->id(),
            'player_right_id' => $validated['opponent_id'],
            'status' => PongGameStatus::Pending,
            ...PongGame::initialAttributes(),
        ]);

        $game->load(['playerLeft', 'playerRight']);

        return response()->json([
            'game' => $game->toGameArray(),
            'invite' => $game->toInviteArray(),
        ], 201);
    }

    public function accept(PongGame $game): JsonResponse
    {
        if ($game->player_right_id !== auth()->id()) {
            abort(403);
        }

        if ($game->status !== PongGameStatus::Pending) {
            abort(422, 'This invite is no longer pending.');
        }

        $game->update([
            'status' => PongGameStatus::Active,
            'last_tick_at' => now(),
        ]);

        $game->load(['playerLeft', 'playerRight']);

        return response()->json([
            'game' => $game->toGameArray(),
        ]);
    }

    public function decline(PongGame $game): JsonResponse
    {
        if ($game->player_right_id !== auth()->id()) {
            abort(403);
        }

        if ($game->status !== PongGameStatus::Pending) {
            abort(422, 'This invite is no longer pending.');
        }

        $game->update([
            'status' => PongGameStatus::Declined,
        ]);

        return response()->json(['ok' => true]);
    }

    public function show(PongGame $game): JsonResponse
    {
        if (! $game->isParticipant((int) auth()->id())) {
            abort(403);
        }

        $previousStatus = $game->status;

        $game->tick();
        $game->save();

        $this->recordWinIfNeeded($game, $previousStatus);

        $game->load(['playerLeft', 'playerRight']);

        return response()->json([
            'game' => $game->toGameArray(),
        ]);
    }

    public function paddle(Request $request, PongGame $game): JsonResponse
    {
        if (! $game->isParticipant((int) auth()->id())) {
            abort(403);
        }

        $validated = $request->validate([
            'y' => ['required', 'numeric', 'min:0', 'max:'.PongGame::HEIGHT],
        ]);

        $previousStatus = $game->status;

        $game->tick();
        $game->updatePaddle((int) auth()->id(), (float) $validated['y']);
        $game->tick();
        $game->save();

        $this->recordWinIfNeeded($game, $previousStatus);

        $game->load(['playerLeft', 'playerRight']);

        return response()->json([
            'game' => $game->toGameArray(),
        ]);
    }

    private function recordWinIfNeeded(PongGame $game, PongGameStatus $previousStatus): void
    {
        if ($previousStatus !== PongGameStatus::Active || $game->status !== PongGameStatus::Won || ! $game->winner_id) {
            return;
        }

        $winner = User::query()->find($game->winner_id);

        if ($winner) {
            $this->leaderboard->recordWin($winner, Minigame::Pong);
        }
    }
}
