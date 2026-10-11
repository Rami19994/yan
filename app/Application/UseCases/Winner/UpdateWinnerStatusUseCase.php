<?php

declare(strict_types=1);

namespace App\Application\UseCases\Winner;

use App\Domain\Audit\Services\AuditLogger;
use App\Domain\User\Models\User;
use App\Domain\Winner\Enums\WinnerStatus;
use App\Domain\Winner\Exceptions\InvalidWinnerStatusTransitionException;
use App\Domain\Winner\Models\Winner;
use Illuminate\Support\Facades\DB;

final readonly class UpdateWinnerStatusUseCase
{
    public function __construct(
        private AuditLogger $auditLogger,
    ) {}

    public function execute(Winner $winner, WinnerStatus $next, User $actor): Winner
    {
        return DB::transaction(function () use ($winner, $next, $actor) {
            /** @var Winner $locked */
            $locked = Winner::query()->lockForUpdate()->findOrFail($winner->id);

            if (! $locked->status->canTransitionTo($next)) {
                throw InvalidWinnerStatusTransitionException::from($locked->status, $next);
            }

            $old = $locked->status;

            $locked->update([
                'status' => $next,
                'confirmed_by' => $next === WinnerStatus::Confirmed ? $actor->id : $locked->confirmed_by,
                'confirmed_at' => $next === WinnerStatus::Confirmed ? now() : $locked->confirmed_at,
            ]);

            $this->auditLogger->log('Prize Status Changed', $locked, [
                'status' => $old->value,
            ], [
                'status' => $next->value,
            ]);

            return $locked;
        });
    }
}
