<div class="mx-auto max-w-2xl p-8">
    <div>
        <h1 class="text-2xl font-bold text-amber-400">
            {{ $competitionId ? __('Edit Competition') : __('New Competition') }}
        </h1>
        <a href="{{ route('admin.competitions.index') }}" class="text-sm text-slate-400 hover:text-slate-300">&larr; {{ __('Competitions') }}</a>
    </div>

    <form wire:submit="save" class="mt-6 space-y-5">
        <div>
            <label class="mb-1 block text-sm text-slate-300">{{ __('Name') }}</label>
            <input type="text" wire:model="name" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
            @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-sm text-slate-300">{{ __('Description') }}</label>
            <textarea wire:model="description" rows="3" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="mb-1 block text-sm text-slate-300">{{ __('Ticket Price (IQD)') }}</label>
                <input type="number" min="0" wire:model="ticketPrice" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                @error('ticketPrice') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-slate-300">{{ __('Virtual Prize Amount (IQD)') }}</label>
                <input type="number" min="0" wire:model="prizeAmount" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                @error('prizeAmount') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-slate-300">{{ __('Cashier Commission (IQD)') }}</label>
                <input type="number" min="0" wire:model="cashierCommissionFixed" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                @error('cashierCommissionFixed') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-slate-300">{{ __('Master Commission (IQD)') }}</label>
                <input type="number" min="0" wire:model="masterCommissionFixed" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                @error('masterCommissionFixed') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="mb-1 block text-sm text-slate-300">{{ __('Draw Mode') }}</label>
                <select wire:model="drawMode" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                    <option value="random">{{ __('Random') }}</option>
                    <option value="admin_selected">{{ __('Admin Selected') }}</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm text-slate-300">{{ __('If No Winner') }}</label>
                <select wire:model="noWinnerPolicy" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                    <option value="no_winner">{{ __('No Winner') }}</option>
                    <option value="redraw">{{ __('Redraw') }}</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="mb-1 block text-sm text-slate-300">{{ __('Sales Open') }}</label>
                <input type="datetime-local" wire:model="salesOpenDatetime" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
            </div>
            <div>
                <label class="mb-1 block text-sm text-slate-300">{{ __('Sales Close') }}</label>
                <input type="datetime-local" wire:model="salesCloseDatetime" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                @error('salesCloseDatetime') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-slate-300">{{ __('Draw Date') }}</label>
                <input type="datetime-local" wire:model="drawDatetime" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.competitions.index') }}" class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">{{ __('Cancel') }}</a>
            <button type="submit" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-amber-400">{{ __('Save') }}</button>
        </div>
    </form>
</div>
