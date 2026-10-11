<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin;

use App\Application\UseCases\Draw\PreviewDrawUseCase;
use App\Application\UseCases\Draw\StartOfficialDrawUseCase;
use App\Domain\Competition\Models\Competition;
use App\Domain\Draw\Events\DrawResultBroadcast;
use App\Domain\Draw\Models\DrawAttempt;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Domain\Ticket\Models\Ticket;
use App\Shared\Exceptions\DomainException;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class DrawControl extends Component
{
    public Competition $competition;

    public ?string $errorMessage = null;

    public function mount(Competition $competition): void
    {
        Gate::authorize('manageDraw', $competition);

        $this->competition = $competition;
    }

    public function preview(PreviewDrawUseCase $useCase): void
    {
        Gate::authorize('manageDraw', $this->competition);

        $this->errorMessage = null;
        $number = $useCase->execute($this->competition);

        $ticket = Ticket::query()
            ->where('competition_id', $this->competition->id)
            ->where('ticket_number', $number)
            ->where('status', TicketStatus::Sold->value)
            ->with(['customer', 'cashier', 'master'])
            ->first();

        $this->dispatch('draw-result', ...[
            'number' => $number,
            'mode' => 'preview',
            'winner' => $ticket ? $this->ticketToWinnerPayload($ticket) : null,
            'noWinner' => $ticket === null,
        ]);
    }

    public function resetPreview(): void
    {
        $this->dispatch('draw-reset');
    }

    public function startOfficialDraw(StartOfficialDrawUseCase $useCase): void
    {
        Gate::authorize('manageDraw', $this->competition);

        try {
            $draw = $useCase->execute($this->competition->id, auth()->user());
        } catch (DomainException $e) {
            $this->errorMessage = __($e->getMessage());

            return;
        }

        $this->competition->refresh();

        $lastAttempt = DrawAttempt::query()
            ->where('draw_id', $draw->id)
            ->orderByDesc('attempt_number')
            ->first();

        $winnerTicket = $this->competition->selected_winner_ticket_id
            ? Ticket::query()->with(['customer', 'cashier', 'master'])->find($this->competition->selected_winner_ticket_id)
            : null;

        $payload = [
            'number' => $lastAttempt?->result_number,
            'mode' => 'official',
            'winner' => $winnerTicket ? $this->ticketToWinnerPayload($winnerTicket) : null,
            'noWinner' => $winnerTicket === null,
        ];

        // Local dispatch gives the admin's own browser an instant reveal
        // without waiting on a WebSocket round-trip; the broadcast is what
        // lets every OTHER open screen (the public /draw/live/{competition}
        // spectator view, other admin sessions, a projector) play the exact
        // same animation at the exact same moment.
        $this->dispatch('draw-result', ...$payload);
        DrawResultBroadcast::dispatch($this->competition->id, $payload);
    }

    private function ticketToWinnerPayload(Ticket $ticket): array
    {
        return [
            'ticketNumber' => $ticket->ticket_number,
            'customerName' => $ticket->customer?->name,
            'prizeAmount' => $this->competition->prize_amount,
            'cashierName' => $ticket->cashier?->name,
            'masterName' => $ticket->master?->name,
        ];
    }

    public function render()
    {
        return view('livewire.admin.draw-control');
    }
}
