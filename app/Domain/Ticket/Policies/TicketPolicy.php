<?php

declare(strict_types=1);

namespace App\Domain\Ticket\Policies;

use App\Domain\Ticket\Models\Ticket;
use App\Domain\User\Models\User;

class TicketPolicy
{
    public function viewAny(User $actor): bool
    {
        return true;
    }

    public function view(User $actor, Ticket $ticket): bool
    {
        if ($actor->isAdmin()) {
            return true;
        }

        if ($actor->isMaster()) {
            return $ticket->master_id === $actor->id;
        }

        return $ticket->cashier_id === $actor->id;
    }

    public function generate(User $actor): bool
    {
        return $actor->isAdmin();
    }

    public function assignToMaster(User $actor): bool
    {
        return $actor->isAdmin();
    }

    public function assignToCashier(User $actor, Ticket $ticket): bool
    {
        return $actor->isMaster() && $ticket->master_id === $actor->id;
    }

    /**
     * Class-level check: can this user assign tickets to cashiers at all
     * (used to gate the "assign to cashier" screen before a specific ticket is known).
     */
    public function assignToCashierAny(User $actor): bool
    {
        return $actor->isMaster();
    }

    public function sell(User $actor, Ticket $ticket): bool
    {
        return $actor->isCashier() && $ticket->cashier_id === $actor->id;
    }

    public function cancel(User $actor, Ticket $ticket): bool
    {
        if ($actor->isAdmin()) {
            return true;
        }

        if ($actor->isMaster()) {
            return $ticket->master_id === $actor->id;
        }

        return $ticket->cashier_id === $actor->id;
    }
}
