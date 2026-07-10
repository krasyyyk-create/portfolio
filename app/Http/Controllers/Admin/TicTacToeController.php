<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Minigame;
use App\Enums\TicTacToeGameStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\TicTacToeGame;
use App\Models\User;
use App\Services\MinigameLeaderboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicTacToeController extends Controller
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

        $incoming = TicTacToeGame::query()
            ->with(['playerX', 'playerO'])
            ->where('player_o_id', $userId)
            ->where('status', TicTacToeGameStatus::Pending)
            ->latest()
            ->get()
            ->map(fn (TicTacToeGame $game) => $game->toInviteArray($userId));

        $outgoing = TicTacToeGame::query()
            ->with(['playerX', 'playerO'])
            ->where('player_x_id', $userId)
            ->where('status', TicTacToeGameStatus::Pending)
            ->latest()
            ->get()
            ->map(fn (TicTacToeGame $game) => $game->toInviteArray($userId));

        $active = TicTacToeGame::query()
            ->with(['playerX', 'playerO'])
            ->where('status', TicTacToeGameStatus::Active)
            ->where(fn ($query) => $query
                ->where('player_x_id', $userId)
                ->orWhere('player_o_id', $userId))
            ->latest('updated_at')
            ->get()
            ->map(fn (TicTacToeGame $game) => $game->toGameArray($userId));

        return response()->json([
            'incoming' => $incoming,
            'outgoing' => $outgoing,
            'active' => $active,
        ]);
    }

    public function invite(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'opponent_id' => ['required', 'integer', 'exists:users,id', 'not_in:'.auth()->id()],
        ]);

        TicTacToeGame::assertInviteeIsAdmin($validated['opponent_id']);

        $existing = TicTacToeGame::query()
            ->where('status', TicTacToeGameStatus::Pending)
            ->where(function ($query) use ($validated) {
                $query->where(function ($pair) use ($validated) {
                    $pair->where('player_x_id', auth()->id())
                        ->where('player_o_id', $validated['opponent_id']);
                })->orWhere(function ($pair) use ($validated) {
                    $pair->where('player_x_id', $validated['opponent_id'])
                        ->where('player_o_id', auth()->id());
                });
            })
            ->first();

        if ($existing) {
            return response()->json([
                'game' => $existing->load(['playerX', 'playerO'])->toGameArray(),
                'invite' => $existing->toInviteArray(),
            ], 200);
        }

        $game = TicTacToeGame::create([
            'player_x_id' => auth()->id(),
            'player_o_id' => $validated['opponent_id'],
            'board' => TicTacToeGame::emptyBoard(),
            'current_turn' => 'x',
            'status' => TicTacToeGameStatus::Pending,
        ]);

        $game->load(['playerX', 'playerO']);

        return response()->json([
            'game' => $game->toGameArray(),
            'invite' => $game->toInviteArray(),
        ], 201);
    }

    public function accept(TicTacToeGame $game): JsonResponse
    {
        if ($game->player_o_id !== auth()->id()) {
            abort(403);
        }

        if ($game->status !== TicTacToeGameStatus::Pending) {
            abort(422, 'This invite is no longer pending.');
        }

        $game->update([
            'status' => TicTacToeGameStatus::Active,
        ]);

        $game->load(['playerX', 'playerO']);

        return response()->json([
            'game' => $game->toGameArray(),
        ]);
    }

    public function decline(TicTacToeGame $game): JsonResponse
    {
        if ($game->player_o_id !== auth()->id()) {
            abort(403);
        }

        if ($game->status !== TicTacToeGameStatus::Pending) {
            abort(422, 'This invite is no longer pending.');
        }

        $game->update([
            'status' => TicTacToeGameStatus::Declined,
        ]);

        return response()->json(['ok' => true]);
    }

    public function show(TicTacToeGame $game): JsonResponse
    {
        if (! $game->isParticipant((int) auth()->id())) {
            abort(403);
        }

        $game->load(['playerX', 'playerO']);

        return response()->json([
            'game' => $game->toGameArray(),
        ]);
    }

    public function move(Request $request, TicTacToeGame $game): JsonResponse
    {
        if (! $game->isParticipant((int) auth()->id())) {
            abort(403);
        }

        $validated = $request->validate([
            'cell' => ['required', 'integer', 'min:0', 'max:8'],
        ]);

        $game->makeMove((int) auth()->id(), $validated['cell']);
        $game->save();

        if ($game->status === TicTacToeGameStatus::Won && $game->winner_id) {
            $winner = User::query()->find($game->winner_id);

            if ($winner) {
                $this->leaderboard->recordWin($winner, Minigame::TicTacToe);
            }
        }

        $game->load(['playerX', 'playerO']);

        return response()->json([
            'game' => $game->toGameArray(),
        ]);
    }
}
