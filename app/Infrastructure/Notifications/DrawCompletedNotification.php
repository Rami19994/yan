<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifications;

use App\Domain\Competition\Models\Competition;
use App\Domain\Ticket\Models\Ticket;
use Illuminate\Notifications\Notification;

class DrawCompletedNotification extends Notification
{
    public function __construct(
        private readonly Competition $competition,
        private readonly ?Ticket $winnerTicket,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $message = $this->winnerTicket !== null
            ? "Draw completed for \"{$this->competition->name}\" — winning ticket #{$this->winnerTicket->ticket_number}."
            : "Draw completed for \"{$this->competition->name}\" — no winner.";

        return [
            'message' => $message,
            'competition_id' => $this->competition->id,
            'winner_ticket_id' => $this->winnerTicket?->id,
        ];
    }
}
