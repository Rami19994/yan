<div class="p-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-amber-400">{{ __('Competitions') }}</h1>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-slate-400 hover:text-slate-300">&larr; {{ __('Dashboard') }}</a>
        </div>
        <a href="{{ route('admin.competitions.create') }}" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-amber-400">
            {{ __('New Competition') }}
        </a>
    </div>

    @if ($errorMessage)
        <div class="mt-4 rounded-lg border border-red-500/40 bg-red-500/10 p-3 text-sm text-red-300">
            {{ $errorMessage }}
        </div>
    @endif

    <div class="mt-6 flex gap-3">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name or slug') }}"
            class="w-full max-w-sm rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-amber-400 focus:outline-none">
        <select wire:model.live="statusFilter" class="rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-amber-400 focus:outline-none">
            <option value="">{{ __('All statuses') }}</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}">{{ __($status->label()) }}</option>
            @endforeach
        </select>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-white/10">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-900/60 text-slate-400">
                <tr>
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="px-4 py-3">{{ __('Ticket Price') }}</th>
                    <th class="px-4 py-3">{{ __('Tickets') }}</th>
                    <th class="px-4 py-3">{{ __('Draw Mode') }}</th>
                    <th class="px-4 py-3">{{ __('Draw Date') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($competitions as $competition)
                    <tr wire:key="competition-{{ $competition->id }}">
                        <td class="px-4 py-3">{{ $competition->name }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-slate-800 px-2 py-1 text-xs text-slate-300">{{ __($competition->status->label()) }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-400">{{ number_format($competition->ticket_price) }} {{ __('IQD') }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $competition->tickets_count }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $competition->draw_mode->value }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $competition->draw_datetime?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.competitions.edit', $competition) }}" class="text-amber-400 hover:underline">{{ __('Edit') }}</a>
                            <a href="{{ route('admin.competitions.winner', $competition) }}" class="ms-3 text-amber-400 hover:underline">{{ __('Winner') }}</a>
                            <a href="{{ route('admin.competitions.draw', $competition) }}" class="ms-3 text-amber-400 hover:underline">{{ __('Draw') }}</a>
                            @if ($competition->status->canTransitionTo(\App\Domain\Competition\Enums\CompetitionStatus::Active))
                                <button wire:click="startNow({{ $competition->id }})"
                                    wire:confirm="{{ __('Start sales now, even before the scheduled start time?') }}"
                                    class="ms-3 font-semibold text-emerald-400 hover:underline">
                                    {{ __('Start Now') }}
                                </button>
                            @endif
                            @foreach ($competition->status->allowedNextStatuses() as $next)
                                <button wire:click="changeStatus({{ $competition->id }}, '{{ $next->value }}')"
                                    wire:confirm="{{ __('Change status to') }} {{ __($next->label()) }}?"
                                    class="ms-3 text-slate-400 hover:underline">
                                    {{ __($next->label()) }}
                                </button>
                            @endforeach
                            <button wire:click="delete({{ $competition->id }})"
                                wire:confirm="{{ __('Permanently delete this competition and everything related to it (tickets, sales, draws, winners)? This cannot be undone.') }}"
                                class="ms-3 font-semibold text-red-400 hover:underline">
                                {{ __('Delete') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">{{ __('No competitions yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $competitions->links() }}</div>
</div>
