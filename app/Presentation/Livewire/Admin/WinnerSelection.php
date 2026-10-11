<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin;

use App\Application\DTOs\Draw\SelectWinnerData;
use App\Application\UseCases\Draw\SelectWinnerUseCase;
use App\Domain\Competition\Models\Competition;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Domain\Ticket\Models\Ticket;
use App\Shared\Exceptions\DomainException;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class WinnerSelection extends Component
{
    use WithPagination;

    public Competition $competition;

    public string $search = '';

    public ?string $message = null;

    public function mount(Competition $competition): void
    {
        Gate::authorize('selectWinner', $competition);

        $this->competition = $competition;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function selectWinner(int $ticketId, SelectWinnerUseCase $useCase): void
    {
        Gate::authorize('selectWinner', $this->competition);

        try {
            $useCase->execute(
                new SelectWinnerData($this->competition->id, $ticketId),
                auth()->user(),
            );
            $this->message = __('Winner selected.');
            $this->competition->refresh();
        } catch (DomainException $e) {
            $this->message = __($e->getMessage());
        }
    }

    public function render()
    {
        $tickets = Ticket::query()
            ->where('competition_id', $this->competition->id)
            ->where('status', TicketStatus::Sold->value)
            ->with(['customer', 'master', 'cashier'])
            ->when($this->search, function ($query) {
                $term = "%{$this->search}%";
                $query->where(function ($q) use ($term) {
                    $q->where('ticket_number', 'like', $term)
                        ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', $term)->orWhere('phone', 'like', $term))
                        ->orWhereHas('master', fn ($m) => $m->where('name', 'like', $term))
                        ->orWhereHas('cashier', fn ($c) => $c->where('name', 'like', $term));
                });
            })
            ->orderBy('ticket_number')
            ->paginate(15);

        return view('livewire.admin.winner-selection', ['tickets' => $tickets]);
    }
}
