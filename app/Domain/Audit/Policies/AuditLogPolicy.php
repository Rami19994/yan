<?php

declare(strict_types=1);

namespace App\Domain\Audit\Policies;

use App\Domain\User\Models\User;

class AuditLogPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->isAdmin();
    }
}
