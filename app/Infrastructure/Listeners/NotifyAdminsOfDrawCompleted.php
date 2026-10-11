<?php

declare(strict_types=1);

namespace App\Infrastructure\Listeners;

use App\Domain\Draw\Events\DrawCompleted;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use App\Infrastructure\Notifications\DrawCompletedNotification;
use Illuminate\Support\Facades\Notification;

class NotifyAdminsOfDrawCompleted
{
    public function handle(DrawCompleted $event): void
    {
        Notification::send(
            User::role(UserRole::Admin->value)->get(),
            new DrawCompletedNotification($event->competition, $event->winnerTicket),
        );
    }
}
