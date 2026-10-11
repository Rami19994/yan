<?php

declare(strict_types=1);

namespace App\Domain\Winner\Policies;

use App\Domain\User\Models\User;
use App\Domain\Winner\Models\Winner;

class WinnerPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->isAdmin();
    }

    public function create(User $actor): bool
    {
        return $actor->isAdmin();
    }

    public function updateStatus(User $actor, Winner $winner): bool
    {
        return $actor->isAdmin();
    }
}
