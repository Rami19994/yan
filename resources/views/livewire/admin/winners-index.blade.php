<div class="mx-auto max-w-6xl p-8">
    <div>
        <h1 class="text-2xl font-bold text-amber-400">{{ __('Winners') }}</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-slate-400 hover:text-slate-300">&larr; {{ __('Dashboard') }}</a>
    </div>

    @if ($message)
        <div class="mt-4 rounded-lg border border-amber-500/30 bg-amber-500/10 px-4 py-2 text-sm text-amber-300">{{ $message }}</div>
    @endif

    <div class="mt-6">
        <select wire:model.live="status" class="rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-amber-400 focus:outline-none">
            <option value="">{{ __('All statuses') }}</option>
            @foreach ($statuses as $s)
                <option value="{{ $s->value }}">{{ __($s->label()) }}</option>
            @endforeach
        </select>
    </div>

    <div class="mt-6 rounded-xl border border-white/10 bg-slate-900/40 p-4">
        <h2 class="text-sm font-semibold text-slate-200">{{ __('Add Winner') }}</h2>
        <p class="mt-1 text-xs text-slate-500">{{ __('Add a grand-prize or consolation-prize winner for a sold ticket, after the competition\'s draw has completed. It will appear in that ticket\'s cashier\'s winners list.') }}</p>

        <form wire:submit="addWinner" class="mt-3 grid gap-3 sm:grid-cols-4">
            <div class="sm:col-span-2">
                <label class="block text-xs text-slate-400">{{ __('Competition') }}</label>
                <select wire:model="newCompetitionId" class="mt-1 w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-amber-400 focus:outline-none">
                    <option value="">{{ __('Select competition') }}</option>
                    @foreach ($completedCompetitions as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('newCompetitionId') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs text-slate-400">{{ __('Ticket Number') }}</label>
                <input type="text" wire:model="newTicketNumber" class="mt-1 w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-amber-400 focus:outline-none" />
                @error('newTicketNumber') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs text-slate-400">{{ __('Prize Amount') }}</label>
                <input type="number" min="1" wire:model="newPrizeAmount" class="mt-1 w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-amber-400 focus:outline-none" />
                @error('newPrizeAmount') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-4">
                <button type="submit" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-amber-400">{{ __('Add') }}</button>
            </div>
        </form>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-white/10">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-900/60 text-slate-400">
                <tr>
                    <th class="px-4 py-3">{{ __('Competition') }}</th>
                    <th class="px-4 py-3">{{ __('Ticket') }}</th>
                    <th class="px-4 py-3">{{ __('Customer') }}</th>
                    <th class="px-4 py-3">{{ __('Cashier') }}</th>
                    <th class="px-4 py-3">{{ __('Prize Amount') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($winners as $winner)
                    <tr wire:key="winner-{{ $winner->id }}">
                        <td class="px-4 py-3">{{ $winner->competition->name }}</td>
                        <td class="px-4 py-3 text-slate-400">#{{ $winner->ticket->ticket_number }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $winner->customer?->maskedName() ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $winner->ticket->cashier?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-amber-400">{{ number_format($winner->prize_amount) }} {{ __('IQD') }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-slate-800 px-2 py-1 text-xs text-slate-300">{{ __($winner->status->label()) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if ($winner->status->value !== 'virtual_prize_delivered')
                                <button wire:click="advance({{ $winner->id }})" class="rounded-lg bg-slate-800 px-3 py-1 text-xs hover:bg-slate-700">
                                    {{ $winner->status->value === 'pending' ? __('Confirm') : __('Mark Prize Delivered') }}
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">{{ __('No winners yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $winners->links() }}</div>

    <p class="mt-6 text-xs text-slate-600">{{ __('All rights reserved.') }}</p>
</div>
