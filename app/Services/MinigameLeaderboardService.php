<?php

namespace App\Services;

use App\Enums\Minigame;
use App\Models\AdminMinigameScore;
use App\Models\User;
use Illuminate\Support\Collection;

class MinigameLeaderboardService
{
    public function recordSnakeScore(User $user, int $score): AdminMinigameScore
    {
        $entry = AdminMinigameScore::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'game' => Minigame::Snake,
            ],
            ['score' => 0],
        );

        if ($score > $entry->score) {
            $entry->update(['score' => $score]);
        }

        return $entry->fresh();
    }

    public function recordWin(User $user, Minigame $game): AdminMinigameScore
    {
        if ($game === Minigame::Snake) {
            abort(422, 'Snake scores must be submitted as points.');
        }

        $entry = AdminMinigameScore::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'game' => $game,
            ],
            ['score' => 0],
        );

        $entry->increment('score');

        return $entry->fresh();
    }

    public function leaderboard(Minigame $game, int $limit = 10): Collection
    {
        return AdminMinigameScore::query()
            ->with('user')
            ->where('game', $game)
            ->where('score', '>', 0)
            ->orderByDesc('score')
            ->orderBy('updated_at')
            ->limit($limit)
            ->get()
            ->values()
            ->map(fn (AdminMinigameScore $entry, int $index) => $entry->toLeaderboardArray($index + 1));
    }
}
