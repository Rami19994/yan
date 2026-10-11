<?php

declare(strict_types=1);

namespace App\Application\UseCases\Competition;

use App\Domain\Audit\Services\AuditLogger;
use App\Domain\Competition\Models\Competition;
use App\Domain\Draw\Models\Draw;
use App\Domain\Draw\Models\DrawAttempt;
use App\Domain\Sale\Models\TicketSale;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\Winner\Models\Winner;
use Illuminate\Support\Facades\DB;

final readonly class DeleteCompetitionUseCase
{
    public function __construct(
        private AuditLogger $auditLogger,
    ) {}

    public function execute(Competition $competition): void
    {
        DB::transaction(function () use ($competition) {
            /** @var Competition $locked */
            $locked = Competition::query()->lockForUpdate()->findOrFail($competition->id);

            $this->auditLogger->log('Competition Deleted', $locked, [
                'name' => $locked->name,
                'status' => $locked->status->value,
            ], []);

            // Deleted explicitly, in dependency order, rather than relying on the
            // database to cascade: the production database (imported from a SQL
            // dump on shared hosting) does not reliably enforce the FK cascade
            // rules the migrations define, so a plain $locked->delete() can leave
            // tickets/sales/winners orphaned instead of removing them.
            $locked->update(['selected_winner_ticket_id' => null]);

            Winner::query()->where('competition_id', $locked->id)->delete();
            DrawAttempt::query()->where('competition_id', $locked->id)->delete();
            Draw::query()->where('competition_id', $locked->id)->delete();
            TicketSale::query()->where('competition_id', $locked->id)->delete();
            Ticket::query()->where('competition_id', $locked->id)->delete();

            $locked->delete();
        });
    }
}
