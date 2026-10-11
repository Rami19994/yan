<?php

declare(strict_types=1);

namespace App\Infrastructure\Listeners;

use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use App\Domain\Winner\Events\WinnerSelected;
use App\Infrastructure\Notifications\WinnerSelectedNotification;
use Illuminate\Support\Facades\Notification;

class NotifyAdminsOfWinnerSelected
{
    public function handle(WinnerSelected $event): void
    {
        Notification::send(
            User::role(UserRole::Admin->value)->get(),
            new WinnerSelectedNotification($event->competition, $event->ticket),
        );
    }
}
