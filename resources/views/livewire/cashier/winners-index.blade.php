<div class="mx-auto max-w-3xl p-8">
    <div>
        <h1 class="text-2xl font-bold text-amber-400">{{ __('My Winners') }}</h1>
        <a href="{{ route('cashier.dashboard') }}" class="text-sm text-slate-400 hover:text-slate-300">&larr; {{ __('Dashboard') }}</a>
    </div>

    <div class="mt-6 space-y-4">
        @forelse ($competitions as $competition)
            @php $winners = $myWinners->get($competition->id, collect()) @endphp
            <div class="rounded-xl border border-white/10 bg-white/5 p-5">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-slate-100">{{ $competition->name }}</h2>
                    <span class="text-xs text-slate-500">{{ $competition->draw_completed_at?->format('Y-m-d H:i') }}</span>
                </div>

                @forelse ($winners as $winner)
                    <div class="mt-3 rounded-lg border border-amber-500/40 bg-amber-500/10 p-4">
                        <div class="text-sm font-semibold text-amber-400">🏆 {{ __('Winning ticket from your sales!') }}</div>
                        <div class="mt-2 grid grid-cols-2 gap-2 text-sm text-slate-300 sm:grid-cols-4">
                            <div><span class="text-slate-500">{{ __('Ticket') }}:</span> #{{ $winner->ticket->ticket_number }}</div>
                            <div><span class="text-slate-500">{{ __('Customer') }}:</span> {{ $winner->customer?->name ?? '—' }}</div>
                            <div><span class="text-slate-500">{{ __('Prize Amount') }}:</span> {{ number_format($winner->prize_amount) }} {{ __('IQD') }}</div>
                            <div><span class="text-slate-500">{{ __('Status') }}:</span> {{ __($winner->status->label()) }}</div>
                        </div>
                    </div>
                @empty
                    <div class="mt-3 rounded-lg border border-white/10 bg-slate-900/40 p-4 text-sm text-slate-500">
                        {{ __('No winners among your customers in this competition.') }}
                    </div>
                @endforelse
            </div>
        @empty
            <div class="rounded-xl border border-white/10 bg-white/5 p-8 text-center text-slate-500">
                {{ __('No completed draws yet for competitions you sold tickets in.') }}
            </div>
        @endforelse
    </div>

    <p class="mt-6 text-xs text-slate-600">{{ __('All rights reserved.') }}</p>
</div>
