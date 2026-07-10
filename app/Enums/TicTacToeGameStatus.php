<?php

namespace App\Enums;

enum TicTacToeGameStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Draw = 'draw';
    case Won = 'won';
    case Declined = 'declined';
    case Cancelled = 'cancelled';
}
