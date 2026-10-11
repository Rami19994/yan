<?php

declare(strict_types=1);

namespace App\Application\UseCases\Draw;

use App\Application\DTOs\Draw\SelectWinnerData;
use App\Domain\Audit\Services\AuditLogger;
use App\Domain\Competition\Enums\DrawMode;
use App\Domain\Competition\Models\Competition;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\User\Models\User;
use App\Domain\Winner\Events\WinnerSelected;
use App\Domain\Winner\Exceptions\WinnerNotEligibleException;
use Illuminate\Support\Facades\DB;

final readonly class SelectWinnerUseCase
{
    public function __construct(
        private AuditLogger $auditLogger,
    ) {}

    public function execute(SelectWinnerData $data, User $admin): Competition
    {
        return DB::transaction(function () use ($data, $admin) {
            /** @var Competition $competition */
            $competition = Competition::query()->lockForUpdate()->findOrFail($data->competitionId);

            if ($competition->draw_started_at !== null) {
                throw WinnerNotEligibleException::drawAlreadyStarted();
            }

            if ($competition->draw_mode !== DrawMode::AdminSelected) {
                throw WinnerNotEligibleException::wrongDrawMode();
            }

            /** @var Ticket $ticket */
            $ticket = Ticket::query()->findOrFail($data->ticketId);

            if ($ticket->competition_id !== $competition->id) {
                throw WinnerNotEligibleException::differentCompetition();
            }

            if ($ticket->status === TicketStatus::Cancelled) {
                throw WinnerNotEligibleException::cancelled();
            }

            if ($ticket->status !== TicketStatus::Sold) {
                throw WinnerNotEligibleException::notSold();
            }

            $old = $competition->only(['selected_winner_ticket_id', 'winner_selected_by']);

            $competition->update([
                'selected_winner_ticket_id' => $ticket->id,
                'winner_selected_by' => $admin->id,
                'winner_selected_at' => now(),
            ]);

            $this->auditLogger->log('Winner Selected', $competition, $old, [
                'selected_winner_ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
            ]);

            event(new WinnerSelected($competition, $ticket, $admin));

            return $competition;
        });
    }
}
