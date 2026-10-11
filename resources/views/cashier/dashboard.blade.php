<x-layouts.app :title="__('Cashier Dashboard')">
<div class="p-8">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-amber-400">{{ __('Cashier Dashboard') }}</h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">{{ __('Logout') }}</button>
        </form>
    </div>
    <p class="mt-2 text-slate-400">{{ __('Welcome') }}, {{ auth()->user()->name }}.</p>

    <nav class="mt-6 flex gap-3">
        <a href="{{ route('cashier.tickets.sell') }}" class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">{{ __('Sell Ticket') }}</a>
        <a href="{{ route('cashier.winners.index') }}" class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">{{ __('My Winners') }}</a>
    </nav>

    <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-5">
        @php
            $cards = [
                ['label' => __('Assigned'), 'value' => $stats['assigned_tickets']],
                ['label' => __('Sold'), 'value' => $stats['sold_tickets']],
                ['label' => __('Remaining'), 'value' => $stats['remaining_tickets']],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <div class="text-xs text-slate-400">{{ $card['label'] }}</div>
                <div class="mt-1 text-2xl font-bold text-slate-100">{{ number_format($card['value']) }}</div>
            </div>
        @endforeach
        <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-4">
            <div class="text-xs text-slate-400">{{ __('Virtual Sales') }}</div>
            <div class="mt-1 text-xl font-bold text-amber-400">{{ number_format($stats['virtual_sales']) }} {{ __('IQD') }}</div>
        </div>
        <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-4">
            <div class="text-xs text-slate-400">{{ __('Virtual Commission') }}</div>
            <div class="mt-1 text-xl font-bold text-amber-400">{{ number_format($stats['virtual_commission']) }} {{ __('IQD') }}</div>
        </div>
    </div>

    <h2 class="mt-8 text-sm font-semibold text-slate-400">{{ __('Recent Sales') }}</h2>
    <div class="mt-3 overflow-x-auto rounded-xl border border-white/10">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-900/60 text-slate-400">
                <tr>
                    <th class="px-4 py-3">{{ __('Ticket') }}</th>
                    <th class="px-4 py-3">{{ __('Customer') }}</th>
                    <th class="px-4 py-3">{{ __('Price') }}</th>
                    <th class="px-4 py-3">{{ __('Sold At') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($recent_sales as $sale)
                    <tr>
                        <td class="px-4 py-3">{{ $sale->ticket?->ticket_number }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $sale->customer?->name }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ number_format($sale->ticket_price) }} {{ __('IQD') }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $sale->sold_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">{{ __('No sales yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-layouts.app>
