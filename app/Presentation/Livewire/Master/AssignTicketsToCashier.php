<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Master;

use App\Application\DTOs\Assignment\AssignTicketsToCashierData;
use App\Application\UseCases\Assignment\AssignTicketsToCashierUseCase;
use App\Domain\Competition\Models\Competition;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\User\Models\User;
use App\Shared\Exceptions\DomainException;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class AssignTicketsToCashier extends Component
{
    public string $competitionId = '';

    public string $cashierId = '';

    public string $count = '';

    public ?string $successMessage = null;

    public function mount(): void
    {
        Gate::authorize('assignToCashierAny', Ticket::class);
    }

    public function assign(AssignTicketsToCashierUseCase $useCase): void
    {
        $data = $this->validate([
            'competitionId' => ['required', 'integer', 'exists:competitions,id'],
            'cashierId' => ['required', 'integer', 'exists:users,id'],
            'count' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $assigned = $useCase->execute(
                new AssignTicketsToCashierData(
                    (int) $data['competitionId'],
                    (int) $data['cashierId'],
                    (int) $data['count'],
                ),
                auth()->user(),
            );
        } catch (DomainException $e) {
            $this->addError('count', __($e->getMessage()));

            return;
        }

        $this->successMessage = __('Assigned :count tickets.', ['count' => $assigned]);
        $this->count = '';
    }

    public function render()
    {
        $competitions = Competition::query()
            ->whereHas('tickets', fn ($q) => $q->where('master_id', auth()->id()))
            ->orderBy('name')
            ->get();

        $cashiers = User::query()->where('master_id', auth()->id())->orderBy('name')->get();

        $availableCount = null;
        if ($this->competitionId) {
            $availableCount = Ticket::query()
                ->where('competition_id', $this->competitionId)
                ->where('master_id', auth()->id())
                ->where('status', TicketStatus::AssignedToMaster->value)
                ->count();
        }

        return view('livewire.master.assign-tickets-to-cashier', [
            'competitions' => $competitions,
            'cashiers' => $cashiers,
            'availableCount' => $availableCount,
        ]);
    }
}
