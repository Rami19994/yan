<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Public;

use App\Domain\Competition\Models\Competition;
use App\Domain\Draw\Models\DrawAttempt;
use App\Domain\Ticket\Models\Ticket;
use App\Presentation\Http\Controllers\Controller;
use Illuminate\View\View;

class LiveDrawController extends Controller
{
    public function __invoke(Competition $competition): View
    {
        $finalResult = null;

        if ($competition->status->value === 'completed') {
            $lastAttempt = DrawAttempt::query()
                ->where('competition_id', $competition->id)
                ->orderByDesc('id')
                ->first();

            $winnerTicket = $competition->selected_winner_ticket_id
                ? Ticket::query()->with(['customer', 'cashier', 'master'])->find($competition->selected_winner_ticket_id)
                : null;

            $finalResult = [
                'number' => $lastAttempt?->result_number,
                'mode' => 'official',
                'winner' => $winnerTicket ? [
                    'ticketNumber' => $winnerTicket->ticket_number,
                    'customerName' => $winnerTicket->customer?->name,
                    'prizeAmount' => $competition->prize_amount,
                    'cashierName' => $winnerTicket->cashier?->name,
                    'masterName' => $winnerTicket->master?->name,
                ] : null,
                'noWinner' => $winnerTicket === null,
            ];
        }

        return view('public.draw-live', [
            'competition' => $competition,
            'finalResult' => $finalResult,
        ]);
    }
}
