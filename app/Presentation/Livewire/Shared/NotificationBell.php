<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Shared;

use App\Domain\User\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public function markAsRead(string $id): void
    {
        /** @var User $user */
        $user = Auth::user();

        $user->notifications()->whereKey($id)->first()?->markAsRead();
    }

    public function markAllAsRead(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $user->unreadNotifications->markAsRead();
    }

    public function render()
    {
        /** @var User $user */
        $user = Auth::user();

        return view('livewire.shared.notification-bell', [
            'unreadCount' => $user->unreadNotifications()->count(),
            'recent' => $user->notifications()->latest()->limit(8)->get(),
        ]);
    }
}
