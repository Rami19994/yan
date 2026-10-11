<?php

declare(strict_types=1);

namespace App\Domain\Competition\Policies;

use App\Domain\Competition\Models\Competition;
use App\Domain\User\Models\User;

class CompetitionPolicy
{
    public function viewAny(User $actor): bool
    {
        return true;
    }

    public function view(User $actor, Competition $competition): bool
    {
        return true;
    }

    public function create(User $actor): bool
    {
        return $actor->isAdmin();
    }

    public function update(User $actor, Competition $competition): bool
    {
        return $actor->isAdmin();
    }

    public function delete(User $actor, Competition $competition): bool
    {
        return $actor->isAdmin();
    }

    public function manageDraw(User $actor, Competition $competition): bool
    {
        return $actor->isAdmin();
    }

    public function selectWinner(User $actor, Competition $competition): bool
    {
        return $actor->isAdmin();
    }
}
