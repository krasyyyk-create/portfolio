<?php

namespace App\Models;

use App\Enums\PongGameStatus;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PongGame extends Model
{
    public const WIDTH = 320;

    public const HEIGHT = 180;

    public const PADDLE_WIDTH = 8;

    public const PADDLE_HEIGHT = 48;

    public const BALL_RADIUS = 5;

    public const WIN_SCORE = 5;

    public const BALL_SPEED = 140;

    public const PADDLE_MARGIN = 10;

    protected $fillable = [
        'player_left_id',
        'player_right_id',
        'left_paddle_y',
        'right_paddle_y',
        'ball_x',
        'ball_y',
        'ball_vx',
        'ball_vy',
        'score_left',
        'score_right',
        'status',
        'winner_id',
        'last_tick_at',
    ];

    protected function casts(): array
    {
        return [
            'left_paddle_y' => 'float',
            'right_paddle_y' => 'float',
            'ball_x' => 'float',
            'ball_y' => 'float',
            'ball_vx' => 'float',
            'ball_vy' => 'float',
            'status' => PongGameStatus::class,
            'last_tick_at' => 'datetime',
        ];
    }

    public function playerLeft(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_left_id');
    }

    public function playerRight(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_right_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public static function initialAttributes(): array
    {
        $direction = random_int(0, 1) === 0 ? -1 : 1;
        $angle = (random_int(-40, 40) / 100) * self::BALL_SPEED;

        return [
            'left_paddle_y' => self::HEIGHT / 2,
            'right_paddle_y' => self::HEIGHT / 2,
            'ball_x' => self::WIDTH / 2,
            'ball_y' => self::HEIGHT / 2,
            'ball_vx' => self::BALL_SPEED * $direction,
            'ball_vy' => $angle,
            'score_left' => 0,
            'score_right' => 0,
            'last_tick_at' => now(),
        ];
    }

    public function isParticipant(int $userId): bool
    {
        return $this->player_left_id === $userId || $this->player_right_id === $userId;
    }

    public function sideFor(int $userId): ?string
    {
        if ($this->player_left_id === $userId) {
            return 'left';
        }

        if ($this->player_right_id === $userId) {
            return 'right';
        }

        return null;
    }

    public function updatePaddle(int $userId, float $y): void
    {
        if ($this->status !== PongGameStatus::Active) {
            abort(422, 'This game is not active.');
        }

        $side = $this->sideFor($userId);

        if ($side === null) {
            abort(403);
        }

        $clamped = $this->clampPaddleY($y);

        if ($side === 'left') {
            $this->left_paddle_y = $clamped;
        } else {
            $this->right_paddle_y = $clamped;
        }
    }

    public function tick(?float $deltaSeconds = null): void
    {
        if ($this->status !== PongGameStatus::Active) {
            return;
        }

        if ($deltaSeconds === null) {
            $deltaSeconds = $this->last_tick_at
                ? max(0, min(0.05, now()->diffInMilliseconds($this->last_tick_at) / 1000))
                : 0.016;
        }

        $deltaSeconds = max(0, min(0.05, $deltaSeconds));

        $this->ball_x += $this->ball_vx * $deltaSeconds;
        $this->ball_y += $this->ball_vy * $deltaSeconds;

        if ($this->ball_y - self::BALL_RADIUS <= 0) {
            $this->ball_y = self::BALL_RADIUS;
            $this->ball_vy = abs($this->ball_vy);
        } elseif ($this->ball_y + self::BALL_RADIUS >= self::HEIGHT) {
            $this->ball_y = self::HEIGHT - self::BALL_RADIUS;
            $this->ball_vy = -abs($this->ball_vy);
        }

        $this->handlePaddleCollision('left');
        $this->handlePaddleCollision('right');

        if ($this->ball_x < -self::BALL_RADIUS) {
            $this->score_right++;
            $this->resetBall(1);
        } elseif ($this->ball_x > self::WIDTH + self::BALL_RADIUS) {
            $this->score_left++;
            $this->resetBall(-1);
        }

        if ($this->score_left >= self::WIN_SCORE) {
            $this->status = PongGameStatus::Won;
            $this->winner_id = $this->player_left_id;
        } elseif ($this->score_right >= self::WIN_SCORE) {
            $this->status = PongGameStatus::Won;
            $this->winner_id = $this->player_right_id;
        }

        $this->last_tick_at = now();
    }

    private function handlePaddleCollision(string $side): void
    {
        $paddleX = $side === 'left'
            ? self::PADDLE_MARGIN
            : self::WIDTH - self::PADDLE_MARGIN - self::PADDLE_WIDTH;

        $paddleY = $side === 'left' ? $this->left_paddle_y : $this->right_paddle_y;
        $paddleTop = $paddleY - (self::PADDLE_HEIGHT / 2);
        $paddleBottom = $paddleY + (self::PADDLE_HEIGHT / 2);

        $ballLeft = $this->ball_x - self::BALL_RADIUS;
        $ballRight = $this->ball_x + self::BALL_RADIUS;
        $ballTop = $this->ball_y - self::BALL_RADIUS;
        $ballBottom = $this->ball_y + self::BALL_RADIUS;

        $paddleRight = $paddleX + self::PADDLE_WIDTH;
        $movingToward = $side === 'left' ? $this->ball_vx < 0 : $this->ball_vx > 0;

        if (! $movingToward) {
            return;
        }

        $overlapY = $ballBottom >= $paddleTop && $ballTop <= $paddleBottom;
        $overlapX = $side === 'left'
            ? $ballLeft <= $paddleRight && $ballRight >= $paddleX
            : $ballRight >= $paddleX && $ballLeft <= $paddleRight;

        if (! $overlapX || ! $overlapY) {
            return;
        }

        $relative = (($this->ball_y - $paddleY) / (self::PADDLE_HEIGHT / 2));
        $relative = max(-1, min(1, $relative));

        $this->ball_vx = ($side === 'left' ? 1 : -1) * abs($this->ball_vx);
        $this->ball_vy = $relative * self::BALL_SPEED * 0.75;
        $this->ball_x = $side === 'left'
            ? $paddleRight + self::BALL_RADIUS
            : $paddleX - self::BALL_RADIUS;
    }

    private function resetBall(int $direction): void
    {
        $this->ball_x = self::WIDTH / 2;
        $this->ball_y = self::HEIGHT / 2;
        $this->ball_vx = self::BALL_SPEED * $direction;
        $this->ball_vy = (random_int(-35, 35) / 100) * self::BALL_SPEED;
        $this->left_paddle_y = self::HEIGHT / 2;
        $this->right_paddle_y = self::HEIGHT / 2;
    }

    private function clampPaddleY(float $y): float
    {
        $half = self::PADDLE_HEIGHT / 2;

        return max($half, min(self::HEIGHT - $half, $y));
    }

    public function toGameArray(?int $viewerId = null): array
    {
        $viewerId ??= (int) auth()->id();
        $side = $this->sideFor($viewerId);

        return [
            'id' => $this->id,
            'width' => self::WIDTH,
            'height' => self::HEIGHT,
            'left_paddle_y' => $this->left_paddle_y,
            'right_paddle_y' => $this->right_paddle_y,
            'ball_x' => $this->ball_x,
            'ball_y' => $this->ball_y,
            'score_left' => $this->score_left,
            'score_right' => $this->score_right,
            'status' => $this->status->value,
            'winner_id' => $this->winner_id,
            'my_side' => $side,
            'player_left' => [
                'id' => $this->playerLeft?->id,
                'name' => $this->playerLeft?->name,
                'avatar_url' => $this->playerLeft?->avatar_url,
            ],
            'player_right' => [
                'id' => $this->playerRight?->id,
                'name' => $this->playerRight?->name,
                'avatar_url' => $this->playerRight?->avatar_url,
            ],
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    public function toInviteArray(?int $viewerId = null): array
    {
        $viewerId ??= (int) auth()->id();
        $isIncoming = $this->player_right_id === $viewerId;

        return [
            'id' => $this->id,
            'game' => 'pong',
            'status' => $this->status->value,
            'is_incoming' => $isIncoming,
            'opponent' => $isIncoming
                ? [
                    'id' => $this->playerLeft?->id,
                    'name' => $this->playerLeft?->name,
                    'avatar_url' => $this->playerLeft?->avatar_url,
                ]
                : [
                    'id' => $this->playerRight?->id,
                    'name' => $this->playerRight?->name,
                    'avatar_url' => $this->playerRight?->avatar_url,
                ],
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    public static function assertInviteeIsAdmin(int $userId): void
    {
        $user = User::query()->findOrFail($userId);

        if (! $user->hasRole(UserRole::Admin)) {
            abort(422, 'You can only invite administrators.');
        }
    }
}
