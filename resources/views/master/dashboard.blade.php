<x-layouts.app :title="__('Master Dashboard')">
<div class="p-8">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-amber-400">{{ __('Master Dashboard') }}</h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">{{ __('Logout') }}</button>
        </form>
    </div>
    <p class="mt-2 text-slate-400">{{ __('Welcome') }}, {{ auth()->user()->name }}.</p>

    <div class="mt-6 rounded-2xl border border-amber-500/30 bg-gradient-to-br from-amber-500/10 to-transparent p-6">
        <div class="text-sm text-slate-400">{{ __('Your Ticket Balance') }}</div>
        <div class="mt-1 text-4xl font-extrabold text-amber-400">{{ number_format($totals['own_ticket_balance']) }}</div>
        <div class="mt-1 text-xs text-slate-500">{{ __('Tickets assigned to you that you haven\'t handed to a cashier yet.') }}</div>
    </div>

    <nav class="mt-6 flex gap-3">
        <a href="{{ route('master.cashiers.index') }}" class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">{{ __('Cashiers') }}</a>
        <a href="{{ route('master.tickets.assign') }}" class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">{{ __('Assign to Cashier') }}</a>
    </nav>

    <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
        @php
            $cards = [
                ['label' => __('Cashiers'), 'value' => $totals['cashiers_count']],
                ['label' => __('Assigned'), 'value' => $totals['assigned_tickets']],
                ['label' => __('Sold'), 'value' => $totals['sold_tickets']],
                ['label' => __('Remaining'), 'value' => $totals['remaining_tickets']],
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
            <div class="mt-1 text-xl font-bold text-amber-400">{{ number_format($totals['virtual_sales']) }} {{ __('IQD') }}</div>
        </div>
        <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-4">
            <div class="text-xs text-slate-400">{{ __('Virtual Commission') }}</div>
            <div class="mt-1 text-xl font-bold text-amber-400">{{ number_format($totals['virtual_commission']) }} {{ __('IQD') }}</div>
        </div>
    </div>

    <h2 class="mt-8 text-sm font-semibold text-slate-400">{{ __('My Cashiers') }}</h2>
    <div class="mt-3 overflow-x-auto rounded-xl border border-white/10">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-900/60 text-slate-400">
                <tr>
                    <th class="px-4 py-3">{{ __('Cashier') }}</th>
                    <th class="px-4 py-3">{{ __('Assigned') }}</th>
                    <th class="px-4 py-3">{{ __('Sold') }}</th>
                    <th class="px-4 py-3">{{ __('Remaining') }}</th>
                    <th class="px-4 py-3">{{ __('Sales') }}</th>
                    <th class="px-4 py-3">{{ __('Commission') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($cashiers as $row)
                    <tr>
                        <td class="px-4 py-3">{{ $row['name'] }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $row['assigned'] }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $row['sold'] }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $row['remaining'] }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ number_format($row['sales']) }} {{ __('IQD') }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ number_format($row['commission']) }} {{ __('IQD') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">{{ __('No cashiers yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-layouts.app>
