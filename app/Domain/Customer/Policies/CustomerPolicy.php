<?php

declare(strict_types=1);

namespace App\Domain\Customer\Policies;

use App\Domain\Customer\Models\Customer;
use App\Domain\User\Models\User;

class CustomerPolicy
{
    public function viewAny(User $actor): bool
    {
        return true;
    }

    public function view(User $actor, Customer $customer): bool
    {
        if ($actor->isAdmin()) {
            return true;
        }

        if ($actor->isMaster()) {
            return $customer->master_id === $actor->id;
        }

        return $customer->cashier_id === $actor->id;
    }

    public function create(User $actor): bool
    {
        return $actor->isCashier();
    }

    public function update(User $actor, Customer $customer): bool
    {
        return $this->view($actor, $customer);
    }
}
