<div class="mx-auto max-w-4xl p-8">
    <div>
        <h1 class="text-2xl font-bold text-amber-400">{{ __('Select Winner') }} — {{ $competition->name }}</h1>
        <a href="{{ route('admin.competitions.index') }}" class="text-sm text-slate-400 hover:text-slate-300">&larr; {{ __('Competitions') }}</a>
    </div>

    @if ($competition->selected_winner_ticket_id)
        <div class="mt-4 rounded-lg border border-emerald-500/40 bg-emerald-500/10 p-3 text-sm text-emerald-300">
            {{ __('Current selection') }}: #{{ $competition->selectedWinnerTicket?->ticket_number }}
        </div>
    @endif

    @if ($message)
        <div class="mt-4 rounded-lg border border-amber-500/40 bg-amber-500/10 p-3 text-sm text-amber-300">
            {{ $message }}
        </div>
    @endif

    <div class="mt-6">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by ticket number, customer, phone, master or cashier') }}"
            class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-amber-400 focus:outline-none">
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-white/10">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-900/60 text-slate-400">
                <tr>
                    <th class="px-4 py-3">{{ __('Ticket') }}</th>
                    <th class="px-4 py-3">{{ __('Customer') }}</th>
                    <th class="px-4 py-3">{{ __('Phone') }}</th>
                    <th class="px-4 py-3">{{ __('Master') }}</th>
                    <th class="px-4 py-3">{{ __('Cashier') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($tickets as $ticket)
                    <tr wire:key="ticket-{{ $ticket->id }}" class="{{ $competition->selected_winner_ticket_id === $ticket->id ? 'bg-emerald-500/10' : '' }}">
                        <td class="px-4 py-3 font-mono">{{ $ticket->ticket_number }}</td>
                        <td class="px-4 py-3">{{ $ticket->customer?->name }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $ticket->customer?->phone }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $ticket->master?->name }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $ticket->cashier?->name }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.tickets.qr-code', $ticket) }}" target="_blank" class="text-slate-400 hover:underline">{{ __('QR') }}</a>
                            <button wire:click="selectWinner({{ $ticket->id }})" wire:confirm="{{ __('Select this ticket as the winner?') }}"
                                class="ms-3 text-amber-400 hover:underline">
                                {{ __('Select Winner') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">{{ __('No sold tickets yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $tickets->links() }}</div>
</div>
