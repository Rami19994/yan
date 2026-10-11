<?php

declare(strict_types=1);

namespace App\Infrastructure\Listeners;

use App\Domain\User\Enums\UserRole;
use App\Domain\User\Events\CashierCreated;
use App\Domain\User\Models\User;
use App\Infrastructure\Notifications\CashierCreatedNotification;
use Illuminate\Support\Facades\Notification;

class NotifyAdminsOfCashierCreated
{
    public function handle(CashierCreated $event): void
    {
        Notification::send(
            User::role(UserRole::Admin->value)->get(),
            new CashierCreatedNotification($event->cashier, $event->master),
        );
    }
}
