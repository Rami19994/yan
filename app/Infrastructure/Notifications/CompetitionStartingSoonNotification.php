<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifications;

use App\Domain\Competition\Models\Competition;
use Illuminate\Notifications\Notification;

class CompetitionStartingSoonNotification extends Notification
{
    public function __construct(
        private readonly Competition $competition,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Competition \"{$this->competition->name}\" opens for sales soon.",
            'competition_id' => $this->competition->id,
            'sales_open_datetime' => $this->competition->sales_open_datetime?->toIso8601String(),
        ];
    }
}
