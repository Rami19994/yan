<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifications;

use App\Domain\User\Models\User;
use Illuminate\Notifications\Notification;

class CashierCreatedNotification extends Notification
{
    public function __construct(
        private readonly User $cashier,
        private readonly User $master,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "New cashier \"{$this->cashier->name}\" was created under master \"{$this->master->name}\".",
            'cashier_id' => $this->cashier->id,
            'master_id' => $this->master->id,
        ];
    }
}
