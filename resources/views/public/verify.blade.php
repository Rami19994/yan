<x-layouts.app :title="__('Verify Ticket')">
<div class="flex min-h-screen items-center justify-center px-4 py-10">
    <div class="w-full max-w-md rounded-2xl border border-amber-500/20 bg-white/5 p-8">
        <h1 class="mb-1 text-xl font-bold text-amber-400">{{ __('Ticket Verification') }}</h1>
        <p class="mb-6 text-xs text-slate-500">{{ __('All rights reserved.') }}</p>

        <dl class="space-y-4 text-sm">
            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <dt class="text-slate-400">{{ __('Ticket Number') }}</dt>
                <dd class="font-mono text-lg font-bold text-slate-100">{{ $ticket->ticket_number }}</dd>
            </div>

            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <dt class="text-slate-400">{{ __('Competition') }}</dt>
                <dd class="text-slate-100">{{ $ticket->competition->name }}</dd>
            </div>

            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <dt class="text-slate-400">{{ __('Status') }}</dt>
                <dd>
                    <span @class([
                        'rounded-full px-3 py-1 text-xs font-semibold',
                        'bg-amber-500/10 text-amber-400' => $ticket->status->value === 'winner',
                        'bg-emerald-500/10 text-emerald-400' => $ticket->status->value === 'sold',
                        'bg-red-500/10 text-red-400' => $ticket->status->value === 'cancelled',
                        'bg-slate-700/40 text-slate-300' => ! in_array($ticket->status->value, ['winner', 'sold', 'cancelled'], true),
                    ])>
                        {{ __($ticket->status->publicLabel()) }}
                    </span>
                </dd>
            </div>

            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <dt class="text-slate-400">{{ __('Draw Date') }}</dt>
                <dd class="text-slate-100">{{ $ticket->competition->draw_datetime?->format('Y-m-d H:i') ?? __('Not scheduled') }}</dd>
            </div>

            @if ($isSold)
                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                    <dt class="text-slate-400">{{ __('Customer') }}</dt>
                    <dd class="text-slate-100">{{ $maskedCustomerName }}</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-slate-400">{{ __('Phone') }}</dt>
                    <dd class="text-slate-100">{{ $maskedCustomerPhone }}</dd>
                </div>
            @endif
        </dl>
    </div>
</div>
</x-layouts.app>
