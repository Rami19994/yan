<?php

declare(strict_types=1);

namespace App\Domain\User\Events;

use App\Domain\User\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CashierCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly User $cashier,
        public readonly User $master,
    ) {}
}
