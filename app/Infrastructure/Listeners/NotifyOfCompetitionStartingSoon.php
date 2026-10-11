<?php

declare(strict_types=1);

namespace App\Infrastructure\Listeners;

use App\Domain\Competition\Events\CompetitionStartingSoon;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use App\Infrastructure\Notifications\CompetitionStartingSoonNotification;
use Illuminate\Support\Facades\Notification;

class NotifyOfCompetitionStartingSoon
{
    public function handle(CompetitionStartingSoon $event): void
    {
        Notification::send(
            User::role(UserRole::Admin->value)->get(),
            new CompetitionStartingSoonNotification($event->competition),
        );
    }
}
