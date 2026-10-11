<div class="p-8">
    <div>
        <h1 class="text-2xl font-bold text-amber-400">{{ __('Cashiers Overview') }}</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-slate-400 hover:text-slate-300">&larr; {{ __('Dashboard') }}</a>
    </div>

    <div class="mt-6">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name, email or master') }}"
            class="w-full max-w-sm rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-amber-400 focus:outline-none">
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-white/10">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-900/60 text-slate-400">
                <tr>
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="px-4 py-3">{{ __('Email') }}</th>
                    <th class="px-4 py-3">{{ __('Master') }}</th>
                    <th class="px-4 py-3">{{ __('Tickets Held') }}</th>
                    <th class="px-4 py-3">{{ __('Tickets Sold') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($cashiers as $cashier)
                    <tr wire:key="cashier-{{ $cashier->id }}">
                        <td class="px-4 py-3">{{ $cashier->name }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $cashier->email }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $cashier->master?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ number_format($cashier->tickets_held) }}</td>
                        <td class="px-4 py-3 text-amber-400">{{ number_format($cashier->tickets_sold) }}</td>
                        <td class="px-4 py-3">
                            @if ($cashier->is_active)
                                <span class="rounded-full bg-emerald-500/10 px-2 py-1 text-xs text-emerald-400">{{ __('Active') }}</span>
                            @else
                                <span class="rounded-full bg-red-500/10 px-2 py-1 text-xs text-red-400">{{ __('Inactive') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">{{ __('No cashiers yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $cashiers->links() }}</div>
</div>
