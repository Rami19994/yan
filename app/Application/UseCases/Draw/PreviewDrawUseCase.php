<?php

declare(strict_types=1);

namespace App\Application\UseCases\Draw;

use App\Domain\Competition\Enums\DrawMode;
use App\Domain\Competition\Models\Competition;
use App\Domain\Ticket\Models\Ticket;
use App\Shared\ValueObjects\TicketNumber;

/**
 * Preview mode never touches the database beyond read-only lookups: no
 * draw/draw_attempt rows, no status changes, no winner. It only returns a
 * result number for the 3D animation to play, clearly marked as a rehearsal
 * — never an official result. Mirrors StartOfficialDrawUseCase's sampling
 * (random_int() offset into the competition's own generated tickets) so the
 * preview behaves like the real thing.
 */
final readonly class PreviewDrawUseCase
{
    public function execute(Competition $competition): int
    {
        if ($competition->draw_mode === DrawMode::AdminSelected && $competition->selected_winner_ticket_id) {
            /** @var Ticket|null $ticket */
            $ticket = Ticket::query()->find($competition->selected_winner_ticket_id);

            if ($ticket) {
                return $ticket->ticket_number;
            }
        }

        $ticketCount = Ticket::query()->where('competition_id', $competition->id)->count();

        if ($ticketCount === 0) {
            return TicketNumber::generateRandom()->toInt();
        }

        $randomOffset = random_int(0, $ticketCount - 1);

        $ticket = Ticket::query()
            ->where('competition_id', $competition->id)
            ->orderBy('id')
            ->skip($randomOffset)
            ->take(1)
            ->first();

        return $ticket->ticket_number;
    }
}
