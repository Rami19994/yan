<x-layouts.app :title="__('Admin Dashboard')">
<div class="p-8">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-amber-400">{{ __('Admin Dashboard') }}</h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">{{ __('Logout') }}</button>
        </form>
    </div>
    <p class="mt-2 text-slate-400">{{ __('Welcome') }}, {{ auth()->user()->name }}.</p>

    <nav class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('admin.masters.index') }}" class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">{{ __('Masters') }}</a>
        <a href="{{ route('admin.cashiers.index') }}" class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">{{ __('Cashiers') }}</a>
        <a href="{{ route('admin.competitions.index') }}" class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">{{ __('Competitions') }}</a>
        <a href="{{ route('admin.tickets.generate') }}" class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">{{ __('Generate Tickets') }}</a>
        <a href="{{ route('admin.tickets.assign') }}" class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">{{ __('Assign to Master') }}</a>
        <a href="{{ route('admin.winners.index') }}" class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">{{ __('Winners') }}</a>
        <a href="{{ route('admin.audit-logs.index') }}" class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">{{ __('Audit Logs') }}</a>
        <a href="{{ route('admin.settings.index') }}" class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">{{ __('Settings') }}</a>
    </nav>

    <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
        @php
            $cards = [
                ['label' => __('Masters'), 'value' => $stats['total_masters']],
                ['label' => __('Cashiers'), 'value' => $stats['total_cashiers']],
                ['label' => __('Competitions'), 'value' => $stats['total_competitions']],
                ['label' => __('Total Tickets'), 'value' => $stats['total_tickets']],
                ['label' => __('Sold Tickets'), 'value' => $stats['sold_tickets']],
                ['label' => __('Available Tickets'), 'value' => $stats['available_tickets']],
                ['label' => __('Cancelled Tickets'), 'value' => $stats['cancelled_tickets']],
                ['label' => __('Winners'), 'value' => $stats['winners_count']],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <div class="text-xs text-slate-400">{{ $card['label'] }}</div>
                <div class="mt-1 text-2xl font-bold text-slate-100">{{ number_format($card['value']) }}</div>
            </div>
        @endforeach
    </div>

    <h2 class="mt-8 text-sm font-semibold text-slate-400">{{ __('Virtual Finance') }}</h2>
    <div class="mt-3 grid grid-cols-2 gap-4 sm:grid-cols-4">
        @php
            $moneyCards = [
                ['label' => __('Total Virtual Sales'), 'value' => $stats['total_virtual_sales']],
                ['label' => __('Cashier Commission'), 'value' => $stats['cashier_virtual_commission']],
                ['label' => __('Master Commission'), 'value' => $stats['master_virtual_commission']],
                ['label' => __('Net Revenue'), 'value' => $stats['virtual_net_revenue']],
                ['label' => __('Virtual Prize Pool'), 'value' => $stats['virtual_prize_amount']],
            ];
        @endphp
        @foreach ($moneyCards as $card)
            <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-4">
                <div class="text-xs text-slate-400">{{ $card['label'] }}</div>
                <div class="mt-1 text-xl font-bold text-amber-400">{{ number_format($card['value']) }} {{ __('IQD') }}</div>
            </div>
        @endforeach
    </div>

    <p class="mt-6 text-xs text-slate-600">{{ __('All rights reserved.') }}</p>
</div>
</x-layouts.app>
