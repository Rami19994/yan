<?php

declare(strict_types=1);

namespace App\Domain\Competition\Enums;

enum NoWinnerPolicy: string
{
    case NoWinner = 'no_winner';
    case Redraw = 'redraw';
}
