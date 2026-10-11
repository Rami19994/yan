<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Shared;

use App\Domain\User\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class NotificationsIndex extends Component
{
    use WithPagination;

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

        $notifications = $user->notifications()->latest()->paginate(20);

        return view('livewire.shared.notifications-index', [
            'notifications' => $notifications,
        ]);
    }
}
