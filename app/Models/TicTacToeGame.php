<?php

namespace App\Models;

use App\Enums\TicTacToeGameStatus;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicTacToeGame extends Model
{
    /** @var list<int> */
    public const WIN_LINES = [
        [0, 1, 2],
        [3, 4, 5],
        [6, 7, 8],
        [0, 3, 6],
        [1, 4, 7],
        [2, 5, 8],
        [0, 4, 8],
        [2, 4, 6],
    ];

    protected $fillable = [
        'player_x_id',
        'player_o_id',
        'board',
        'current_turn',
        'status',
        'winner_id',
    ];

    protected function casts(): array
    {
        return [
            'board' => 'array',
            'status' => TicTacToeGameStatus::class,
        ];
    }

    public function playerX(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_x_id');
    }

    public function playerO(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_o_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public static function emptyBoard(): array
    {
        return array_fill(0, 9, null);
    }

    public function isParticipant(int $userId): bool
    {
        return $this->player_x_id === $userId || $this->player_o_id === $userId;
    }

    public function symbolFor(int $userId): ?string
    {
        if ($this->player_x_id === $userId) {
            return 'x';
        }

        if ($this->player_o_id === $userId) {
            return 'o';
        }

        return null;
    }

    public function opponentIdFor(int $userId): ?int
    {
        if ($this->player_x_id === $userId) {
            return $this->player_o_id;
        }

        if ($this->player_o_id === $userId) {
            return $this->player_x_id;
        }

        return null;
    }

    public function makeMove(int $userId, int $cell): void
    {
        if ($this->status !== TicTacToeGameStatus::Active) {
            abort(422, 'This game is not active.');
        }

        $symbol = $this->symbolFor($userId);

        if ($symbol === null) {
            abort(403);
        }

        if ($this->current_turn !== $symbol) {
            abort(422, 'It is not your turn.');
        }

        if ($cell < 0 || $cell > 8) {
            abort(422, 'Invalid cell.');
        }

        $board = $this->board;

        if ($board[$cell] !== null) {
            abort(422, 'That cell is already taken.');
        }

        $board[$cell] = $symbol;
        $this->board = $board;

        $winnerSymbol = $this->winningSymbol($board);

        if ($winnerSymbol !== null) {
            $this->status = TicTacToeGameStatus::Won;
            $this->winner_id = $winnerSymbol === 'x' ? $this->player_x_id : $this->player_o_id;

            return;
        }

        if (! in_array(null, $board, true)) {
            $this->status = TicTacToeGameStatus::Draw;

            return;
        }

        $this->current_turn = $symbol === 'x' ? 'o' : 'x';
    }

    /**
     * @param  array<int, string|null>  $board
     */
    public function winningSymbol(array $board): ?string
    {
        foreach (self::WIN_LINES as [$a, $b, $c]) {
            if ($board[$a] !== null && $board[$a] === $board[$b] && $board[$b] === $board[$c]) {
                return $board[$a];
            }
        }

        return null;
    }

    public function toGameArray(?int $viewerId = null): array
    {
        $viewerId ??= auth()->id();
        $mySymbol = $this->symbolFor((int) $viewerId);

        return [
            'id' => $this->id,
            'board' => $this->board,
            'current_turn' => $this->current_turn,
            'status' => $this->status->value,
            'player_x' => [
                'id' => $this->playerX?->id,
                'name' => $this->playerX?->name,
                'avatar_url' => $this->playerX?->avatar_url,
            ],
            'player_o' => [
                'id' => $this->playerO?->id,
                'name' => $this->playerO?->name,
                'avatar_url' => $this->playerO?->avatar_url,
            ],
            'winner_id' => $this->winner_id,
            'my_symbol' => $mySymbol,
            'is_my_turn' => $this->status === TicTacToeGameStatus::Active
                && $mySymbol !== null
                && $this->current_turn === $mySymbol,
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    public function toInviteArray(?int $viewerId = null): array
    {
        $viewerId ??= auth()->id();
        $isIncoming = $this->player_o_id === $viewerId;

        return [
            'id' => $this->id,
            'game' => 'tic-tac-toe',
            'status' => $this->status->value,
            'is_incoming' => $isIncoming,
            'opponent' => $isIncoming
                ? [
                    'id' => $this->playerX?->id,
                    'name' => $this->playerX?->name,
                    'avatar_url' => $this->playerX?->avatar_url,
                ]
                : [
                    'id' => $this->playerO?->id,
                    'name' => $this->playerO?->name,
                    'avatar_url' => $this->playerO?->avatar_url,
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
