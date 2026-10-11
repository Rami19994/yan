<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Master;

use App\Application\DTOs\User\CreateCashierData;
use App\Application\DTOs\User\UpdateUserData;
use App\Application\UseCases\User\CreateCashierUseCase;
use App\Application\UseCases\User\ToggleUserActiveUseCase;
use App\Application\UseCases\User\UpdateUserUseCase;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class CashiersIndex extends Component
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
        Gate::authorize('viewAnyCashiers', User::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        Gate::authorize('createCashier', User::class);

        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $cashier = User::query()->findOrFail($id);
        Gate::authorize('update', $cashier);

        $this->editingId = $cashier->id;
        $this->name = $cashier->name;
        $this->email = $cashier->email;
        $this->phone = (string) $cashier->phone;
        $this->password = '';
        $this->showModal = true;
    }

    public function save(CreateCashierUseCase $createUseCase, UpdateUserUseCase $updateUseCase): void
    {
        if ($this->editingId) {
            $cashier = User::query()->findOrFail($this->editingId);
            Gate::authorize('update', $cashier);

            $data = $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$cashier->id],
                'phone' => ['nullable', 'string', 'max:30'],
                'password' => ['nullable', 'string', 'min:8'],
            ]);

            $updateUseCase->execute($cashier, new UpdateUserData(
                name: $data['name'],
                email: $data['email'],
                phone: $data['phone'] ?: null,
                password: $data['password'] ?: null,
            ));
        } else {
            Gate::authorize('createCashier', User::class);

            $data = $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'phone' => ['nullable', 'string', 'max:30'],
                'password' => ['required', 'string', 'min:8'],
            ]);

            $createUseCase->execute(new CreateCashierData(
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
        $cashier = User::query()->findOrFail($id);
        Gate::authorize('toggleActive', $cashier);

        $useCase->execute($cashier);
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
        $cashiers = User::query()
            ->where('master_id', auth()->id())
            ->when($this->search, fn ($query) => $query
                ->where(fn ($q) => $q
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.master.cashiers-index', ['cashiers' => $cashiers]);
    }
}
