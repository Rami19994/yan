<div class="mx-auto max-w-xl p-8">
    <div>
        <h1 class="text-2xl font-bold text-amber-400">{{ __('Assign Tickets to Cashier') }}</h1>
        <a href="{{ route('master.dashboard') }}" class="text-sm text-slate-400 hover:text-slate-300">&larr; {{ __('Dashboard') }}</a>
    </div>

    @if ($successMessage)
        <div class="mt-6 rounded-lg border border-emerald-500/40 bg-emerald-500/10 p-3 text-sm text-emerald-300">
            {{ $successMessage }}
        </div>
    @endif

    <form wire:submit="assign" class="mt-6 space-y-5">
        <div>
            <label class="mb-1 block text-sm text-slate-300">{{ __('Competition') }}</label>
            <select wire:model.live="competitionId" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                <option value="">{{ __('Select a competition') }}</option>
                @foreach ($competitions as $competition)
                    <option value="{{ $competition->id }}">{{ $competition->name }} — {{ __($competition->status->label()) }}</option>
                @endforeach
            </select>
            @error('competitionId') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            @if (! is_null($availableCount))
                <p class="mt-1 text-xs text-slate-500">{{ __('Available') }}: {{ $availableCount }}</p>
            @endif
        </div>

        <div>
            <label class="mb-1 block text-sm text-slate-300">{{ __('Cashier') }}</label>
            <select wire:model="cashierId" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                <option value="">{{ __('Select a cashier') }}</option>
                @foreach ($cashiers as $cashier)
                    <option value="{{ $cashier->id }}">{{ $cashier->name }}</option>
                @endforeach
            </select>
            @error('cashierId') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-sm text-slate-300">{{ __('Number of Tickets') }}</label>
            <input type="number" min="1" wire:model="count" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
            @error('count') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-amber-400">
            {{ __('Assign') }}
        </button>
    </form>
</div>
