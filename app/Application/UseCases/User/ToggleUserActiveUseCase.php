<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Domain\Audit\Services\AuditLogger;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class ToggleUserActiveUseCase
{
    public function __construct(
        private AuditLogger $auditLogger,
    ) {}

    public function execute(User $target): User
    {
        return DB::transaction(function () use ($target) {
            $wasActive = $target->is_active;

            $target->update(['is_active' => ! $wasActive]);

            $this->auditLogger->log(
                $wasActive ? 'User Deactivated' : 'User Activated',
                $target,
                ['is_active' => $wasActive],
                ['is_active' => $target->is_active],
            );

            return $target;
        });
    }
}
