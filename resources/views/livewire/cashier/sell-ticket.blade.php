<div class="mx-auto max-w-xl p-8">
    @vite('resources/js/ticket-share.js')
    <div>
        <h1 class="text-2xl font-bold text-amber-400">{{ __('Sell Ticket') }}</h1>
        <a href="{{ route('cashier.dashboard') }}" class="text-sm text-slate-400 hover:text-slate-300">&larr; {{ __('Dashboard') }}</a>
    </div>

    @if ($successMessage)
        <div class="mt-6 rounded-lg border border-emerald-500/40 bg-emerald-500/10 p-3 text-sm text-emerald-300">
            {{ $successMessage }}
        </div>
    @endif

    @if ($soldTicket)
        <div class="mt-4">
            <button
                type="button"
                data-ticket-share="{{ json_encode([
                    'dir' => app()->getLocale() === 'ar' ? 'rtl' : 'ltr',
                    'brand' => config('app.name'),
                    'tagline' => __('Draw Ticket'),
                    'competitionName' => $soldTicket['competitionName'],
                    'nameLabel' => __('Customer Name'),
                    'customerName' => $soldTicket['customerName'],
                    'phoneLabel' => __('Phone Number'),
                    'customerPhone' => $soldTicket['customerPhone'],
                    'drawDateLabel' => __('Draw Date'),
                    'drawDate' => $soldTicket['drawDate'] ?? __('Not scheduled'),
                    'luckyNumberLabel' => __('Your Lucky Number'),
                    'ticketNumber' => $soldTicket['ticketNumber'],
                    'qrDataUri' => $soldTicket['qrDataUri'],
                    'footerLine' => __('Valid for the next draw. Please keep this number.'),
                    'disclaimer' => __('All rights reserved.'),
                    'shareText' => __('Ticket :number for :competition', ['number' => $soldTicket['ticketNumber'], 'competition' => $soldTicket['competitionName']]),
                    'generatingLabel' => __('Generating image...'),
                    'fallbackMessage' => __('Image downloaded. Attach it to a WhatsApp chat to share it.'),
                    'errorMessage' => __('Could not generate the ticket image.'),
                ]) }}"
                class="flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M20.52 3.48A11.86 11.86 0 0 0 12.04 0C5.5 0 .2 5.3.2 11.84c0 2.09.55 4.13 1.6 5.93L0 24l6.4-1.68a11.86 11.86 0 0 0 5.64 1.44h.01c6.54 0 11.84-5.3 11.84-11.84 0-3.16-1.23-6.13-3.37-8.44ZM12.05 21.6h-.01a9.8 9.8 0 0 1-5-1.37l-.36-.21-3.8 1 1.01-3.7-.23-.38a9.78 9.78 0 0 1-1.5-5.1c0-5.4 4.4-9.8 9.9-9.8 2.64 0 5.12 1.03 6.99 2.9a9.75 9.75 0 0 1 2.9 6.95c0 5.4-4.4 9.71-9.9 9.71Zm5.4-7.28c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.65-2.05-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.6-.91-2.2-.24-.57-.49-.5-.67-.5h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.47s1.07 2.86 1.22 3.06c.15.2 2.1 3.2 5.08 4.5.71.3 1.26.49 1.7.63.71.22 1.36.19 1.87.12.57-.09 1.76-.72 2-1.41.25-.7.25-1.3.17-1.42-.07-.13-.27-.2-.57-.35Z"/></svg>
                {{ __('Share on WhatsApp') }}
            </button>
        </div>
    @endif

    <form wire:submit="sell" class="mt-6 space-y-5">
        <div>
            <label class="mb-1 block text-sm text-slate-300">{{ __('Ticket Number') }}</label>
            <div class="flex gap-2">
                <input type="text" inputmode="numeric" maxlength="7" wire:model="ticketNumber" placeholder="5832041"
                    class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-lg tracking-widest focus:border-amber-400 focus:outline-none">
                <button type="button" wire:click="pickRandomAvailableTicket"
                    class="shrink-0 rounded-lg bg-slate-800 px-3 py-2 text-sm hover:bg-slate-700" title="{{ __('Pick Random Ticket') }}">
                    🎲 {{ __('Random') }}
                </button>
            </div>
            @error('ticketNumber') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-sm text-slate-300">{{ __('Customer Name') }}</label>
            <input type="text" wire:model="customerName" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
            @error('customerName') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-sm text-slate-300">{{ __('Phone Number') }}</label>
            <input type="text" wire:model="customerPhone" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
            @error('customerPhone') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-amber-400">
            {{ __('Sell') }}
        </button>
    </form>
</div>
