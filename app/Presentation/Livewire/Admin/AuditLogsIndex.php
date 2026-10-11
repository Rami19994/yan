<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin;

use App\Domain\Audit\Models\AuditLog;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class AuditLogsIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $action = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public ?int $expandedId = null;

    public function mount(): void
    {
        Gate::authorize('audit-logs.view');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingAction(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function toggleExpand(int $id): void
    {
        $this->expandedId = $this->expandedId === $id ? null : $id;
    }

    public function render()
    {
        $logs = AuditLog::query()
            ->with('user')
            ->when($this->search, function ($query) {
                $term = "%{$this->search}%";
                $query->where(fn ($q) => $q
                    ->where('action', 'like', $term)
                    ->orWhere('entity_type', 'like', $term)
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $term)));
            })
            ->when($this->action, fn ($query) => $query->where('action', $this->action))
            ->when($this->dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($query) => $query->whereDate('created_at', '<=', $this->dateTo))
            ->latest('created_at')
            ->paginate(25);

        $distinctActions = AuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('livewire.admin.audit-logs-index', [
            'logs' => $logs,
            'distinctActions' => $distinctActions,
        ]);
    }
}
