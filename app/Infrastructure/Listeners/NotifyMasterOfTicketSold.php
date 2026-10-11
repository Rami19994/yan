<?php

declare(strict_types=1);

namespace App\Infrastructure\Listeners;

use App\Domain\Ticket\Events\TicketSold;
use App\Infrastructure\Notifications\TicketSoldNotification;

class NotifyMasterOfTicketSold
{
    public function handle(TicketSold $event): void
    {
        $event->ticket->master?->notify(new TicketSoldNotification($event->ticket));
    }
}
