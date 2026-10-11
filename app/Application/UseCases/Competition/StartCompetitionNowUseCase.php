<?php

declare(strict_types=1);

namespace App\Application\UseCases\Competition;

use App\Domain\Audit\Services\AuditLogger;
use App\Domain\Competition\Enums\CompetitionStatus;
use App\Domain\Competition\Exceptions\InvalidCompetitionStatusTransitionException;
use App\Domain\Competition\Models\Competition;
use Illuminate\Support\Facades\DB;

/**
 * Lets an admin open sales immediately, bypassing a scheduled
 * sales_open_datetime that hasn't arrived yet — e.g. a competition was
 * scheduled to open tomorrow but the admin wants it live right now.
 * Only moves the competition into Active; sales_close_datetime is left
 * untouched since the request is to start early, not to also change when
 * sales end.
 */
final readonly class StartCompetitionNowUseCase
{
    public function __construct(
        private AuditLogger $auditLogger,
    ) {}

    public function execute(Competition $competition): Competition
    {
        return DB::transaction(function () use ($competition) {
            /** @var Competition $locked */
            $locked = Competition::query()->lockForUpdate()->findOrFail($competition->id);

            if (! $locked->status->canTransitionTo(CompetitionStatus::Active)) {
                throw InvalidCompetitionStatusTransitionException::from($locked->status, CompetitionStatus::Active);
            }

            $old = $locked->only(['status', 'sales_open_datetime']);

            $locked->update([
                'status' => CompetitionStatus::Active,
                'sales_open_datetime' => now(),
            ]);

            $this->auditLogger->log('Competition Started Early', $locked, $old, [
                'status' => CompetitionStatus::Active->value,
                'sales_open_datetime' => $locked->sales_open_datetime->toIso8601String(),
            ]);

            return $locked;
        });
    }
}
