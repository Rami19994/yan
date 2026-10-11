<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Events;

use App\Domain\User\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketsAssigned
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $recipientId,
        public readonly int $competitionId,
        public readonly int $count,
        public readonly User $assignedBy,
    ) {}
}
