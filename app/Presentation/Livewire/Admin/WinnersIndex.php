<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin;

use App\Application\DTOs\Winner\AddWinnerData;
use App\Application\UseCases\Winner\AddWinnerUseCase;
use App\Application\UseCases\Winner\UpdateWinnerStatusUseCase;
use App\Domain\Competition\Enums\CompetitionStatus;
use App\Domain\Competition\Models\Competition;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\Winner\Enums\WinnerStatus;
use App\Domain\Winner\Models\Winner;
use App\Shared\Exceptions\DomainException;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class WinnersIndex extends Component
{
    use WithPagination;

    public string $status = '';

    public ?string $message = null;

    public string $newCompetitionId = '';

    public string $newTicketNumber = '';

    public string $newPrizeAmount = '';

    public function mount(): void
    {
        Gate::authorize('viewAny', Winner::class);
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function addWinner(AddWinnerUseCase $useCase): void
    {
        Gate::authorize('create', Winner::class);

        $this->validate([
            'newCompetitionId' => ['required', 'integer'],
            'newTicketNumber' => ['required', 'string'],
            'newPrizeAmount' => ['required', 'integer', 'min:1'],
        ]);

        $ticket = Ticket::query()
            ->where('competition_id', $this->newCompetitionId)
            ->where('ticket_number', $this->newTicketNumber)
            ->first();

        if (! $ticket) {
            $this->addError('newTicketNumber', __('No ticket with this number exists in the selected competition.'));

            return;
        }

        try {
            $useCase->execute(
                new AddWinnerData((int) $this->newCompetitionId, $ticket->id, (int) $this->newPrizeAmount),
                auth()->user(),
            );
            $this->message = __('Winner added.');
            $this->reset(['newCompetitionId', 'newTicketNumber', 'newPrizeAmount']);
        } catch (DomainException $e) {
            $this->addError('newTicketNumber', __($e->getMessage()));
        }
    }

    public function advance(int $winnerId, UpdateWinnerStatusUseCase $useCase): void
    {
        $winner = Winner::query()->findOrFail($winnerId);
        Gate::authorize('updateStatus', $winner);

        $next = match ($winner->status) {
            WinnerStatus::Pending => WinnerStatus::Confirmed,
            WinnerStatus::Confirmed => WinnerStatus::VirtualPrizeDelivered,
            WinnerStatus::VirtualPrizeDelivered => null,
        };

        if ($next === null) {
            return;
        }

        try {
            $useCase->execute($winner, $next, auth()->user());
            $this->message = __('Prize status updated.');
        } catch (DomainException $e) {
            $this->message = __($e->getMessage());
        }
    }

    public function render()
    {
        $winners = Winner::query()
            ->with(['customer', 'ticket.cashier', 'competition'])
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->latest('created_at')
            ->paginate(15);

        return view('livewire.admin.winners-index', [
            'winners' => $winners,
            'statuses' => WinnerStatus::cases(),
            'completedCompetitions' => Competition::query()
                ->where('status', CompetitionStatus::Completed)
                ->orderByDesc('draw_completed_at')
                ->get(),
        ]);
    }
}
