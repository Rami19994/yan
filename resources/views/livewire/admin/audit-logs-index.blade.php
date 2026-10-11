<div class="mx-auto max-w-6xl p-8">
    <div>
        <h1 class="text-2xl font-bold text-amber-400">{{ __('Audit Logs') }}</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-slate-400 hover:text-slate-300">&larr; {{ __('Dashboard') }}</a>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search action, entity or user') }}"
            class="rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-amber-400 focus:outline-none sm:col-span-2">
        <select wire:model.live="action" class="rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-amber-400 focus:outline-none">
            <option value="">{{ __('All actions') }}</option>
            @foreach ($distinctActions as $a)
                <option value="{{ $a }}">{{ $a }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <input type="date" wire:model.live="dateFrom" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-amber-400 focus:outline-none">
            <input type="date" wire:model.live="dateTo" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-amber-400 focus:outline-none">
        </div>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-white/10">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-900/60 text-slate-400">
                <tr>
                    <th class="px-4 py-3">{{ __('When') }}</th>
                    <th class="px-4 py-3">{{ __('User') }}</th>
                    <th class="px-4 py-3">{{ __('Action') }}</th>
                    <th class="px-4 py-3">{{ __('Entity') }}</th>
                    <th class="px-4 py-3">{{ __('IP') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($logs as $log)
                    <tr wire:key="log-{{ $log->id }}" class="cursor-pointer hover:bg-white/5" wire:click="toggleExpand({{ $log->id }})">
                        <td class="px-4 py-3 text-slate-400">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="px-4 py-3">{{ $log->user?->name ?? __('System') }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-slate-800 px-2 py-1 text-xs text-slate-300">{{ $log->action }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-400">
                            {{ $log->entity_type ? class_basename($log->entity_type).' #'.$log->entity_id : '—' }}
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ $log->ip_address }}</td>
                        <td class="px-4 py-3 text-right text-slate-500">{{ $expandedId === $log->id ? '▲' : '▼' }}</td>
                    </tr>
                    @if ($expandedId === $log->id)
                        <tr wire:key="log-detail-{{ $log->id }}">
                            <td colspan="6" class="bg-slate-950/60 px-4 py-4">
                                <div class="grid grid-cols-1 gap-4 text-xs sm:grid-cols-2">
                                    <div>
                                        <div class="mb-1 font-semibold text-slate-400">{{ __('Old Values') }}</div>
                                        <pre class="overflow-x-auto rounded-lg bg-black/40 p-3 text-slate-300">{{ $log->old_values ? json_encode($log->old_values, JSON_PRETTY_PRINT) : '—' }}</pre>
                                    </div>
                                    <div>
                                        <div class="mb-1 font-semibold text-slate-400">{{ __('New Values') }}</div>
                                        <pre class="overflow-x-auto rounded-lg bg-black/40 p-3 text-slate-300">{{ $log->new_values ? json_encode($log->new_values, JSON_PRETTY_PRINT) : '—' }}</pre>
                                    </div>
                                </div>
                                <div class="mt-3 truncate text-xs text-slate-600">{{ $log->user_agent }}</div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">{{ __('No audit log entries found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
</div>
