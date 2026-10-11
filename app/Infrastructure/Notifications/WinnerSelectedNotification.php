<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifications;

use App\Domain\Competition\Models\Competition;
use App\Domain\Ticket\Models\Ticket;
use Illuminate\Notifications\Notification;

class WinnerSelectedNotification extends Notification
{
    public function __construct(
        private readonly Competition $competition,
        private readonly Ticket $ticket,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Winner pre-selected for \"{$this->competition->name}\" — ticket #{$this->ticket->ticket_number}.",
            'competition_id' => $this->competition->id,
            'ticket_id' => $this->ticket->id,
        ];
    }
}
