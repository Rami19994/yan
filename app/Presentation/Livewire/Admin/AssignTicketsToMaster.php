<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin;

use App\Application\DTOs\Assignment\AssignTicketsToMasterData;
use App\Application\UseCases\Assignment\AssignTicketsToMasterUseCase;
use App\Domain\Competition\Models\Competition;
use App\Domain\Ticket\Enums\TicketStatus;
use App\Domain\Ticket\Models\Ticket;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use App\Shared\Exceptions\DomainException;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class AssignTicketsToMaster extends Component
{
    public string $competitionId = '';

    public string $masterId = '';

    public string $count = '';

    public ?string $successMessage = null;

    public function mount(): void
    {
        Gate::authorize('assignToMaster', Ticket::class);
    }

    public function assign(AssignTicketsToMasterUseCase $useCase): void
    {
        Gate::authorize('assignToMaster', Ticket::class);

        $data = $this->validate([
            'competitionId' => ['required', 'integer', 'exists:competitions,id'],
            'masterId' => ['required', 'integer', 'exists:users,id'],
            'count' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $assigned = $useCase->execute(
                new AssignTicketsToMasterData(
                    (int) $data['competitionId'],
                    (int) $data['masterId'],
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
        $competitions = Competition::query()->orderBy('name')->get();
        $masters = User::query()->role(UserRole::Master->value)->orderBy('name')->get();

        $availableCount = null;
        if ($this->competitionId) {
            $availableCount = Ticket::query()
                ->where('competition_id', $this->competitionId)
                ->where('status', TicketStatus::Generated->value)
                ->count();
        }

        return view('livewire.admin.assign-tickets-to-master', [
            'competitions' => $competitions,
            'masters' => $masters,
            'availableCount' => $availableCount,
        ]);
    }
}
