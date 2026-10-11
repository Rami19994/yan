<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifications;

use App\Domain\Competition\Models\Competition;
use App\Domain\User\Models\User;
use Illuminate\Notifications\Notification;

class TicketsAssignedNotification extends Notification
{
    public function __construct(
        private readonly Competition $competition,
        private readonly int $count,
        private readonly User $assignedBy,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "{$this->count} ticket(s) for \"{$this->competition->name}\" were assigned to you by {$this->assignedBy->name}.",
            'competition_id' => $this->competition->id,
            'count' => $this->count,
        ];
    }
}
