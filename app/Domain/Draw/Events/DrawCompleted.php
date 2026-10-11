<?php

declare(strict_types=1);

namespace App\Domain\Draw\Events;

use App\Domain\Competition\Models\Competition;
use App\Domain\Ticket\Models\Ticket;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DrawCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Competition $competition,
        public readonly ?Ticket $winnerTicket,
    ) {}
}
