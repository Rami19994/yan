<?php

declare(strict_types=1);

namespace App\Application\UseCases\Competition;

use App\Application\DTOs\Competition\CompetitionData;
use App\Domain\Audit\Services\AuditLogger;
use App\Domain\Competition\Enums\CompetitionStatus;
use App\Domain\Competition\Models\Competition;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class CreateCompetitionUseCase
{
    public function __construct(
        private AuditLogger $auditLogger,
    ) {}

    public function execute(CompetitionData $data, User $admin): Competition
    {
        return DB::transaction(function () use ($data, $admin) {
            $slug = $this->uniqueSlug($data->name);

            $competition = Competition::query()->create([
                'name' => $data->name,
                'slug' => $slug,
                'description' => $data->description,
                'status' => CompetitionStatus::Draft,
                'ticket_price' => $data->ticketPrice,
                'cashier_commission_fixed' => $data->cashierCommissionFixed,
                'master_commission_fixed' => $data->masterCommissionFixed,
                'prize_amount' => $data->prizeAmount,
                'draw_mode' => $data->drawMode,
                'no_winner_policy' => $data->noWinnerPolicy,
                'draw_datetime' => $data->drawDatetime,
                'sales_open_datetime' => $data->salesOpenDatetime,
                'sales_close_datetime' => $data->salesCloseDatetime,
                'created_by' => $admin->id,
            ]);

            $this->auditLogger->log('Competition Created', $competition, [], [
                'name' => $competition->name,
                'status' => $competition->status->value,
            ]);

            return $competition;
        });
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'competition';
        $slug = $base;
        $i = 1;

        while (Competition::query()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
