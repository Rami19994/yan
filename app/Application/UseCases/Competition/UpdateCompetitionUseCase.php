<?php

declare(strict_types=1);

namespace App\Application\UseCases\Competition;

use App\Application\DTOs\Competition\CompetitionData;
use App\Domain\Audit\Services\AuditLogger;
use App\Domain\Competition\Models\Competition;
use App\Domain\Draw\Exceptions\DrawAlreadyStartedException;
use Illuminate\Support\Facades\DB;

final readonly class UpdateCompetitionUseCase
{
    public function __construct(
        private AuditLogger $auditLogger,
    ) {}

    public function execute(Competition $competition, CompetitionData $data): Competition
    {
        return DB::transaction(function () use ($competition, $data) {
            if ($competition->draw_started_at !== null) {
                $drawModeChanged = $competition->draw_mode !== $data->drawMode;
                $prizeChanged = $competition->prize_amount !== $data->prizeAmount;

                if ($drawModeChanged || $prizeChanged) {
                    throw DrawAlreadyStartedException::create();
                }
            }

            $old = $competition->only([
                'name', 'description', 'ticket_price', 'cashier_commission_fixed',
                'master_commission_fixed', 'prize_amount', 'draw_mode', 'no_winner_policy',
                'draw_datetime', 'sales_open_datetime', 'sales_close_datetime',
            ]);

            $competition->update([
                'name' => $data->name,
                'description' => $data->description,
                'ticket_price' => $data->ticketPrice,
                'cashier_commission_fixed' => $data->cashierCommissionFixed,
                'master_commission_fixed' => $data->masterCommissionFixed,
                'prize_amount' => $data->prizeAmount,
                'draw_mode' => $data->drawMode,
                'no_winner_policy' => $data->noWinnerPolicy,
                'draw_datetime' => $data->drawDatetime,
                'sales_open_datetime' => $data->salesOpenDatetime,
                'sales_close_datetime' => $data->salesCloseDatetime,
            ]);

            $this->auditLogger->log('Competition Updated', $competition, $old, $competition->only([
                'name', 'description', 'ticket_price', 'cashier_commission_fixed',
                'master_commission_fixed', 'prize_amount', 'draw_mode', 'no_winner_policy',
                'draw_datetime', 'sales_open_datetime', 'sales_close_datetime',
            ]));

            return $competition;
        });
    }
}
