<?php

declare(strict_types=1);

namespace App\Application\UseCases\Draw;

use App\Domain\Audit\Services\AuditLogger;
use App\Domain\Competition\Enums\CompetitionStatus;
use App\Domain\Competition\Enums\DrawMode;
use App\Domain\Competition\Enums\NoWinnerPolicy;
use App\Domain\Competition\Models\Competition;
use App\Domain\Draw\Enums\DrawStatus;
use App\Domain\Draw\Events\DrawCompleted;
use App\Domain\Draw\Exceptions\DrawAlreadyStartedException;
use App\Domain\Draw\Exceptions\NoWinnerSelectedException;
use App\Domain\Draw\Models\Draw;
use App\Domain\Draw\Models\DrawAttempt;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\User\Models\User;
use App\Domain\Winner\Models\Winner;
use Illuminate\Support\Facades\DB;

final readonly class StartOfficialDrawUseCase
{
    private const int MAX_REDRAW_ATTEMPTS = 25;

    public function __construct(
        private AuditLogger $auditLogger,
    ) {}

    public function execute(int $competitionId, User $admin): Draw
    {
        return DB::transaction(function () use ($competitionId, $admin) {
            /** @var Competition $competition */
            $competition = Competition::query()->lockForUpdate()->findOrFail($competitionId);

            if ($competition->draw_started_at !== null) {
                throw DrawAlreadyStartedException::create();
            }

            if ($competition->draw_mode === DrawMode::AdminSelected && $competition->selected_winner_ticket_id === null) {
                throw NoWinnerSelectedException::create();
            }

            $now = now();

            $competition->update([
                'status' => CompetitionStatus::Drawing,
                'draw_started_at' => $now,
            ]);

            $draw = Draw::query()->create([
                'competition_id' => $competition->id,
                'draw_mode' => $competition->draw_mode,
                'status' => DrawStatus::InProgress,
                'is_preview' => false,
                'started_by' => $admin->id,
                'started_at' => $now,
            ]);

            $this->auditLogger->log('Draw Started', $competition, [], [
                'draw_mode' => $competition->draw_mode->value,
            ]);

            $winnerTicket = $competition->draw_mode === DrawMode::AdminSelected
                ? $this->resolveAdminSelected($draw, $competition, $admin)
                : $this->resolveRandom($draw, $competition, $admin);

            $completedAt = now();

            if ($winnerTicket) {
                $winnerTicket->update(['status' => TicketStatus::Winner, 'is_winner' => true]);

                Winner::query()->create([
                    'customer_id' => $winnerTicket->customer_id,
                    'ticket_id' => $winnerTicket->id,
                    'competition_id' => $competition->id,
                    'prize_amount' => $competition->prize_amount,
                    'status' => 'pending',
                ]);

                $competition->update([
                    'selected_winner_ticket_id' => $winnerTicket->id,
                    'status' => CompetitionStatus::Completed,
                    'draw_completed_at' => $completedAt,
                ]);

                $this->auditLogger->log('Draw Completed', $competition, [], [
                    'winner_ticket_id' => $winnerTicket->id,
                    'ticket_number' => $winnerTicket->ticket_number,
                ]);
            } else {
                $competition->update([
                    'status' => CompetitionStatus::Completed,
                    'draw_completed_at' => $completedAt,
                ]);

                $this->auditLogger->log('Draw Completed', $competition, [], [
                    'winner_ticket_id' => null,
                    'result' => 'no_winner',
                ]);
            }

            $draw->update(['status' => DrawStatus::Completed, 'completed_at' => $completedAt]);

            event(new DrawCompleted($competition, $winnerTicket));

            return $draw->fresh();
        });
    }

    private function resolveAdminSelected(Draw $draw, Competition $competition, User $admin): Ticket
    {
        /** @var Ticket $ticket */
        $ticket = Ticket::query()->lockForUpdate()->findOrFail($competition->selected_winner_ticket_id);

        $now = now();

        DrawAttempt::query()->create([
            'draw_id' => $draw->id,
            'competition_id' => $competition->id,
            'attempt_number' => 1,
            'draw_mode' => DrawMode::AdminSelected,
            'result_number' => $ticket->ticket_number,
            'selected_ticket_id' => $ticket->id,
            'is_winner' => true,
            'winner_ticket_id' => $ticket->id,
            'started_by' => $admin->id,
            'started_at' => $now,
            'completed_at' => $now,
            'created_at' => $now,
            'hash' => DrawAttempt::computeHash($competition->id, 1, $ticket->ticket_number, $now->toIso8601String()),
        ]);

        return $ticket;
    }

    /**
     * Draws by picking a random_int() offset into THIS competition's own generated
     * tickets (not the full 1,000,000–9,999,999 address space). Sampling the full
     * range would make "no winner" the near-certain outcome for any realistic ticket
     * count, which would make the whole feature undemonstrable; sampling the
     * competition's real, printed tickets keeps odds meaningful while still using
     * backend random_int() exclusively and still genuinely checking "is this
     * ticket sold?" before declaring a winner. Each attempt samples with
     * replacement (a later attempt may re-land on an already-tried ticket).
     */
    private function resolveRandom(Draw $draw, Competition $competition, User $admin): ?Ticket
    {
        $ticketCount = Ticket::query()->where('competition_id', $competition->id)->count();

        if ($ticketCount === 0) {
            return null;
        }

        for ($attempt = 1; $attempt <= self::MAX_REDRAW_ATTEMPTS; $attempt++) {
            $randomOffset = random_int(0, $ticketCount - 1);

            $candidate = Ticket::query()
                ->where('competition_id', $competition->id)
                ->orderBy('id')
                ->skip($randomOffset)
                ->take(1)
                ->lockForUpdate()
                ->first();

            $resultNumber = $candidate->ticket_number;
            $now = now();

            $ticket = $candidate->status === TicketStatus::Sold ? $candidate : null;
            $isWinner = $ticket !== null;

            DrawAttempt::query()->create([
                'draw_id' => $draw->id,
                'competition_id' => $competition->id,
                'attempt_number' => $attempt,
                'draw_mode' => DrawMode::Random,
                'result_number' => $resultNumber,
                'selected_ticket_id' => null,
                'is_winner' => $isWinner,
                'winner_ticket_id' => $ticket?->id,
                'started_by' => $admin->id,
                'started_at' => $now,
                'completed_at' => $now,
                'created_at' => $now,
                'hash' => DrawAttempt::computeHash($competition->id, $attempt, $resultNumber, $now->toIso8601String()),
            ]);

            if ($isWinner) {
                return $ticket;
            }

            if ($competition->no_winner_policy === NoWinnerPolicy::NoWinner) {
                return null;
            }

            // Redraw: loop to the next attempt.
        }

        return null;
    }
}
