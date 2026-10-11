<?php

declare(strict_types=1);

namespace App\Domain\User\Policies;

use App\Domain\User\Models\User;

class UserPolicy
{
    /**
     * View the list of Masters (Admin only) or Cashiers (Admin, or Master for their own).
     */
    public function viewAnyMasters(User $actor): bool
    {
        return $actor->isAdmin();
    }

    public function viewAnyCashiers(User $actor): bool
    {
        return $actor->isAdmin() || $actor->isMaster();
    }

    public function view(User $actor, User $target): bool
    {
        if ($actor->isAdmin()) {
            return true;
        }

        if ($actor->isMaster()) {
            return $target->master_id === $actor->id;
        }

        return $actor->id === $target->id;
    }

    public function createMaster(User $actor): bool
    {
        return $actor->isAdmin();
    }

    public function createCashier(User $actor): bool
    {
        return $actor->isMaster();
    }

    public function update(User $actor, User $target): bool
    {
        if ($actor->isAdmin()) {
            return $target->isMaster();
        }

        if ($actor->isMaster()) {
            return $target->isCashier() && $target->master_id === $actor->id;
        }

        return false;
    }

    public function toggleActive(User $actor, User $target): bool
    {
        return $this->update($actor, $target);
    }
}
