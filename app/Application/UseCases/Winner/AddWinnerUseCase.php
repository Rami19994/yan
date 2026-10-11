<?php

declare(strict_types=1);

namespace App\Application\UseCases\Winner;

use App\Application\DTOs\Winner\AddWinnerData;
use App\Domain\Audit\Services\AuditLogger;
use App\Domain\Competition\Models\Competition;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\User\Models\User;
use App\Domain\Winner\Enums\WinnerStatus;
use App\Domain\Winner\Exceptions\WinnerNotEligibleException;
use App\Domain\Winner\Models\Winner;
use Illuminate\Support\Facades\DB;

final readonly class AddWinnerUseCase
{
    public function __construct(
        private AuditLogger $auditLogger,
    ) {}

    public function execute(AddWinnerData $data, User $admin): Winner
    {
        return DB::transaction(function () use ($data, $admin) {
            /** @var Competition $competition */
            $competition = Competition::query()->lockForUpdate()->findOrFail($data->competitionId);

            if ($competition->draw_completed_at === null) {
                throw WinnerNotEligibleException::drawNotCompleted();
            }

            /** @var Ticket $ticket */
            $ticket = Ticket::query()->findOrFail($data->ticketId);

            if ($ticket->competition_id !== $competition->id) {
                throw WinnerNotEligibleException::differentCompetition();
            }

            if ($ticket->status === TicketStatus::Cancelled) {
                throw WinnerNotEligibleException::cancelled();
            }

            if (! in_array($ticket->status, [TicketStatus::Sold, TicketStatus::Winner, TicketStatus::PrizePaid], true)) {
                throw WinnerNotEligibleException::notSold();
            }

            if (Winner::query()->where('ticket_id', $ticket->id)->exists()) {
                throw WinnerNotEligibleException::alreadyWinner();
            }

            $winner = Winner::query()->create([
                'customer_id' => $ticket->customer_id,
                'ticket_id' => $ticket->id,
                'competition_id' => $competition->id,
                'prize_amount' => $data->prizeAmount,
                'status' => WinnerStatus::Pending->value,
            ]);

            $ticket->update(['status' => TicketStatus::Winner, 'is_winner' => true]);

            // The draw result screens (live draw + admin draw control) read
            // selected_winner_ticket_id to decide winner-vs-no-winner, so if the
            // automated draw didn't land on a winner, fill that slot with the
            // first manually-added winner — otherwise those screens would keep
            // showing "No Winner" even after one was added.
            if ($competition->selected_winner_ticket_id === null) {
                $competition->update(['selected_winner_ticket_id' => $ticket->id]);
            }

            $this->auditLogger->log('Winner Added Manually', $competition, [], [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'prize_amount' => $data->prizeAmount,
                'added_by' => $admin->id,
            ]);

            return $winner;
        });
    }
}
