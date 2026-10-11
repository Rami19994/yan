<?php

declare(strict_types=1);

namespace App\Domain\Settings\Policies;

use App\Domain\User\Models\User;

class SettingPolicy
{
    public function manage(User $actor): bool
    {
        return $actor->isAdmin();
    }
}
