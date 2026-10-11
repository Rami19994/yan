<?php

declare(strict_types=1);

namespace App\Domain\Draw\Enums;

enum DrawStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
