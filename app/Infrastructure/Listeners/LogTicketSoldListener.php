<?php

declare(strict_types=1);

namespace App\Infrastructure\Listeners;

use App\Domain\Ticket\Events\TicketSold;
use Illuminate\Support\Facades\Log;

class LogTicketSoldListener
{
    public function handle(TicketSold $event): void
    {
        Log::info('Ticket sold', [
            'ticket_id' => $event->ticket->id,
            'ticket_number' => $event->ticket->ticket_number,
            'competition_id' => $event->ticket->competition_id,
            'cashier_id' => $event->ticket->cashier_id,
        ]);
    }
}
