<?php

namespace App\Enums;

enum Minigame: string
{
    case Snake = 'snake';
    case TicTacToe = 'tic-tac-toe';
    case Pong = 'pong';
    case Minesweeper = 'minesweeper';

    public function label(): string
    {
        return match ($this) {
            self::Snake => 'Snake',
            self::TicTacToe => 'Tic Tac Toe',
            self::Pong => 'Pong',
            self::Minesweeper => 'Minesweeper',
        };
    }

    public function scoreLabel(): string
    {
        return $this === self::Snake ? 'Points' : 'Wins';
    }

    public function ranksByWins(): bool
    {
        return $this !== self::Snake;
    }
}
