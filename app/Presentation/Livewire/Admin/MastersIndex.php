<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin;

use App\Application\DTOs\User\CreateMasterData;
use App\Application\DTOs\User\UpdateUserData;
use App\Application\UseCases\User\CreateMasterUseCase;
use App\Application\UseCases\User\ToggleUserActiveUseCase;
use App\Application\UseCases\User\UpdateUserUseCase;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class MastersIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $password = '';

    public function mount(): void
    {
        Gate::authorize('viewAnyMasters', User::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        Gate::authorize('createMaster', User::class);

        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $master = User::query()->findOrFail($id);
        Gate::authorize('update', $master);

        $this->editingId = $master->id;
        $this->name = $master->name;
        $this->email = $master->email;
        $this->phone = (string) $master->phone;
        $this->password = '';
        $this->showModal = true;
    }

    public function save(CreateMasterUseCase $createUseCase, UpdateUserUseCase $updateUseCase): void
    {
        if ($this->editingId) {
            $master = User::query()->findOrFail($this->editingId);
            Gate::authorize('update', $master);

            $data = $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$master->id],
                'phone' => ['nullable', 'string', 'max:30'],
                'password' => ['nullable', 'string', 'min:8'],
            ]);

            $updateUseCase->execute($master, new UpdateUserData(
                name: $data['name'],
                email: $data['email'],
                phone: $data['phone'] ?: null,
                password: $data['password'] ?: null,
            ));
        } else {
            Gate::authorize('createMaster', User::class);

            $data = $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'phone' => ['nullable', 'string', 'max:30'],
                'password' => ['required', 'string', 'min:8'],
            ]);

            $createUseCase->execute(new CreateMasterData(
                name: $data['name'],
                email: $data['email'],
                phone: $data['phone'] ?: null,
                password: $data['password'],
            ), auth()->user());
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function toggleActive(int $id, ToggleUserActiveUseCase $useCase): void
    {
        $master = User::query()->findOrFail($id);
        Gate::authorize('toggleActive', $master);

        $useCase->execute($master);
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->password = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        $masters = User::query()
            ->role(UserRole::Master->value)
            ->withCount('cashiers')
            ->when($this->search, fn ($query) => $query
                ->where(fn ($q) => $q
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")))
            ->orderBy('name')
            ->paginate(15);

        $ticketStats = DB::table('tickets')
            ->whereIn('master_id', $masters->pluck('id'))
            ->select('master_id')
            ->selectRaw('COUNT(*) as sent')
            ->selectRaw('SUM(CASE WHEN cashier_id IS NOT NULL THEN 1 ELSE 0 END) as distributed')
            ->groupBy('master_id')
            ->get()
            ->keyBy('master_id')
            ->map(fn ($row) => [
                'sent' => (int) $row->sent,
                'distributed' => (int) $row->distributed,
                'remaining' => (int) $row->sent - (int) $row->distributed,
            ]);

        return view('livewire.admin.masters-index', ['masters' => $masters, 'ticketStats' => $ticketStats]);
    }
}
