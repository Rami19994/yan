<x-layouts.app :title="__('Ticket QR Code')">
<div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
    <div class="w-full max-w-sm rounded-2xl border border-amber-500/20 bg-white/5 p-8 text-center">
        <h1 class="mb-1 text-lg font-bold text-amber-400">{{ __('Ticket') }} #{{ $ticket->ticket_number }}</h1>
        <p class="mb-6 text-xs text-slate-500">{{ __('Scan to verify this ticket') }}</p>

        <img src="{{ $dataUri }}" alt="QR code" class="mx-auto rounded-lg border border-white/10 bg-white p-3">

        <p class="mt-4 break-all text-xs text-slate-500">{{ $verifyUrl }}</p>
    </div>
</div>
</x-layouts.app>
