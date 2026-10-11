<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin;

use App\Domain\Ticket\Enums\TicketStatus;
use App\Domain\User\Enums\UserRole;
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

    public function mount(): void
    {
        Gate::authorize('viewAnyCashiers', User::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $soldStatuses = [TicketStatus::Sold->value, TicketStatus::Winner->value, TicketStatus::PrizePaid->value];

        $cashiers = User::query()
            ->role(UserRole::Cashier->value)
            ->with('master')
            ->withCount([
                'ticketsAsCashier as tickets_held' => fn ($query) => $query->where('status', TicketStatus::AssignedToCashier->value),
                'ticketsAsCashier as tickets_sold' => fn ($query) => $query->whereIn('status', $soldStatuses),
            ])
            ->when($this->search, fn ($query) => $query
                ->where(fn ($q) => $q
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhereHas('master', fn ($m) => $m->where('name', 'like', "%{$this->search}%"))))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.cashiers-index', ['cashiers' => $cashiers]);
    }
}
