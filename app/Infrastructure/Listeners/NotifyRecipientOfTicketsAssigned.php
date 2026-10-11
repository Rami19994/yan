<?php

declare(strict_types=1);

namespace App\Infrastructure\Listeners;

use App\Domain\Assignment\Events\TicketsAssigned;
use App\Domain\Competition\Models\Competition;
use App\Domain\User\Models\User;
use App\Infrastructure\Notifications\TicketsAssignedNotification;

class NotifyRecipientOfTicketsAssigned
{
    public function handle(TicketsAssigned $event): void
    {
        $recipient = User::query()->find($event->recipientId);
        $competition = Competition::query()->find($event->competitionId);

        if ($recipient === null || $competition === null) {
            return;
        }

        $recipient->notify(new TicketsAssignedNotification($competition, $event->count, $event->assignedBy));
    }
}
