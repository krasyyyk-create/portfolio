<?php

namespace App\Models;

use App\Enums\Minigame;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminMinigameScore extends Model
{
    protected $fillable = [
        'user_id',
        'game',
        'score',
    ];

    protected function casts(): array
    {
        return [
            'game' => Minigame::class,
            'score' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toLeaderboardArray(int $rank): array
    {
        return [
            'rank' => $rank,
            'user_id' => $this->user_id,
            'name' => $this->user?->name ?? 'Unknown',
            'avatar_url' => $this->user?->avatar_url,
            'score' => $this->score,
            'is_me' => auth()->id() === $this->user_id,
        ];
    }
}
