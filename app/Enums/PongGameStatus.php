<?php

namespace App\Enums;

enum PongGameStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Won = 'won';
    case Declined = 'declined';
    case Cancelled = 'cancelled';
}
