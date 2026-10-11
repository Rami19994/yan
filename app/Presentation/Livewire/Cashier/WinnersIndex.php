<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Cashier;

use App\Domain\Competition\Models\Competition;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Domain\Winner\Models\Winner;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class WinnersIndex extends Component
{
    public function render()
    {
        $cashierId = auth()->id();

        $soldStatuses = [TicketStatus::Sold->value, TicketStatus::Winner->value, TicketStatus::PrizePaid->value];

        $competitions = Competition::query()
            ->whereNotNull('draw_completed_at')
            ->whereHas('tickets', fn ($query) => $query
                ->where('cashier_id', $cashierId)
                ->whereIn('status', $soldStatuses))
            ->orderByDesc('draw_completed_at')
            ->get();

        $myWinners = Winner::query()
            ->whereIn('competition_id', $competitions->pluck('id'))
            ->whereHas('ticket', fn ($query) => $query->where('cashier_id', $cashierId))
            ->with(['ticket', 'customer'])
            ->oldest('id')
            ->get()
            ->groupBy('competition_id');

        return view('livewire.cashier.winners-index', [
            'competitions' => $competitions,
            'myWinners' => $myWinners,
        ]);
    }
}
