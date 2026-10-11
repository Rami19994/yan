<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin;

use App\Application\UseCases\Competition\ChangeCompetitionStatusUseCase;
use App\Application\UseCases\Competition\DeleteCompetitionUseCase;
use App\Application\UseCases\Competition\StartCompetitionNowUseCase;
use App\Domain\Competition\Enums\CompetitionStatus;
use App\Domain\Competition\Models\Competition;
use App\Shared\Exceptions\DomainException;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class CompetitionsIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public ?string $errorMessage = null;

    public function mount(): void
    {
        Gate::authorize('viewAny', Competition::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function changeStatus(int $id, string $status, ChangeCompetitionStatusUseCase $useCase): void
    {
        $this->errorMessage = null;

        $competition = Competition::query()->findOrFail($id);
        Gate::authorize('update', $competition);

        try {
            $useCase->execute($competition, CompetitionStatus::from($status));
        } catch (DomainException $e) {
            $this->errorMessage = __($e->getMessage());
        }
    }

    public function startNow(int $id, StartCompetitionNowUseCase $useCase): void
    {
        $this->errorMessage = null;

        $competition = Competition::query()->findOrFail($id);
        Gate::authorize('update', $competition);

        try {
            $useCase->execute($competition);
        } catch (DomainException $e) {
            $this->errorMessage = __($e->getMessage());
        }
    }

    public function delete(int $id, DeleteCompetitionUseCase $useCase): void
    {
        $this->errorMessage = null;

        $competition = Competition::query()->findOrFail($id);
        Gate::authorize('delete', $competition);

        $useCase->execute($competition);
    }

    public function render()
    {
        $competitions = Competition::query()
            ->when($this->search, fn ($query) => $query
                ->where(fn ($q) => $q
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('slug', 'like', "%{$this->search}%")))
            ->when($this->statusFilter, fn ($query) => $query->where('status', $this->statusFilter))
            ->withCount('tickets')
            ->latest()
            ->paginate(15);

        return view('livewire.admin.competitions-index', [
            'competitions' => $competitions,
            'statuses' => CompetitionStatus::cases(),
        ]);
    }
}
