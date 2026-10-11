<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifications;

use App\Domain\Ticket\Models\Ticket;
use Illuminate\Notifications\Notification;

class TicketSoldNotification extends Notification
{
    public function __construct(
        private readonly Ticket $ticket,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Ticket #{$this->ticket->ticket_number} was sold.",
            'ticket_id' => $this->ticket->id,
            'competition_id' => $this->ticket->competition_id,
        ];
    }
}
