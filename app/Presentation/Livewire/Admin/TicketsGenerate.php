<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin;

use App\Application\DTOs\Ticket\GenerateTicketsData;
use App\Application\UseCases\Ticket\GenerateTicketsUseCase;
use App\Domain\Competition\Models\Competition;
use App\Domain\Ticket\Models\Ticket;
use App\Shared\Exceptions\DomainException;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class TicketsGenerate extends Component
{
    public string $competitionId = '';

    public string $count = '';

    public ?string $successMessage = null;

    public function mount(): void
    {
        Gate::authorize('generate', Ticket::class);
    }

    public function generate(GenerateTicketsUseCase $useCase): void
    {
        Gate::authorize('generate', Ticket::class);

        $data = $this->validate([
            'competitionId' => ['required', 'integer', 'exists:competitions,id'],
            'count' => ['required', 'integer', 'min:1', 'max:100000'],
        ]);

        try {
            $created = $useCase->execute(
                new GenerateTicketsData((int) $data['competitionId'], (int) $data['count']),
                auth()->user(),
            );
        } catch (DomainException $e) {
            $this->addError('competitionId', __($e->getMessage()));

            return;
        }

        $this->successMessage = __('Generated :count tickets.', ['count' => $created]);
        $this->count = '';
    }

    public function render()
    {
        $competitions = Competition::query()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->withCount('tickets')
            ->orderBy('name')
            ->get();

        return view('livewire.admin.tickets-generate', ['competitions' => $competitions]);
    }
}
