<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Minigame;
use App\Http\Controllers\Controller;
use App\Services\MinigameLeaderboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeaderboardController extends Controller
{
    public function __construct(
        private MinigameLeaderboardService $leaderboard,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'game' => ['required', Rule::enum(Minigame::class)],
        ]);

        $game = Minigame::from($validated['game']);

        return response()->json([
            'game' => $game->value,
            'label' => $game->label(),
            'score_label' => $game->scoreLabel(),
            'entries' => $this->leaderboard->leaderboard($game),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'game' => ['required', Rule::enum(Minigame::class)],
            'score' => ['nullable', 'integer', 'min:1'],
        ]);

        $game = Minigame::from($validated['game']);
        $user = $request->user();

        if ($game === Minigame::Snake) {
            $request->validate([
                'score' => ['required', 'integer', 'min:1'],
            ]);

            $entry = $this->leaderboard->recordSnakeScore($user, $validated['score']);
        } else {
            $entry = $this->leaderboard->recordWin($user, $game);
        }

        return response()->json([
            'entry' => [
                'game' => $entry->game->value,
                'score' => $entry->score,
            ],
        ]);
    }
}
