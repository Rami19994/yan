<?php

declare(strict_types=1);

namespace App\Domain\Draw\Policies;

use App\Domain\Draw\Models\Draw;
use App\Domain\User\Models\User;

class DrawPolicy
{
    public function viewAny(User $actor): bool
    {
        return true;
    }

    public function view(User $actor, Draw $draw): bool
    {
        return true;
    }

    public function control(User $actor): bool
    {
        return $actor->isAdmin();
    }
}
