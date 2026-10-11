<?php

declare(strict_types=1);

namespace App\Application\UseCases\Competition;

use App\Domain\Audit\Services\AuditLogger;
use App\Domain\Competition\Enums\CompetitionStatus;
use App\Domain\Competition\Exceptions\InvalidCompetitionStatusTransitionException;
use App\Domain\Competition\Models\Competition;
use Illuminate\Support\Facades\DB;

final readonly class ChangeCompetitionStatusUseCase
{
    public function __construct(
        private AuditLogger $auditLogger,
    ) {}

    public function execute(Competition $competition, CompetitionStatus $next): Competition
    {
        return DB::transaction(function () use ($competition, $next) {
            /** @var Competition $locked */
            $locked = Competition::query()->lockForUpdate()->findOrFail($competition->id);

            if (! $locked->status->canTransitionTo($next)) {
                throw InvalidCompetitionStatusTransitionException::from($locked->status, $next);
            }

            $old = $locked->status;
            $locked->update(['status' => $next]);

            $this->auditLogger->log('Competition Status Changed', $locked, [
                'status' => $old->value,
            ], [
                'status' => $next->value,
            ]);

            return $locked;
        });
    }
}
