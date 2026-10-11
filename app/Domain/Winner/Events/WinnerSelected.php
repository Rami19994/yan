<?php

declare(strict_types=1);

namespace App\Domain\Winner\Events;

use App\Domain\Competition\Models\Competition;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\User\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WinnerSelected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Competition $competition,
        public readonly Ticket $ticket,
        public readonly User $selectedBy,
    ) {}
}
