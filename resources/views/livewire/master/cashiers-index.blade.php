<div class="p-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-amber-400">{{ __('Cashiers') }}</h1>
            <a href="{{ route('master.dashboard') }}" class="text-sm text-slate-400 hover:text-slate-300">&larr; {{ __('Dashboard') }}</a>
        </div>
        <button wire:click="openCreate" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-amber-400">
            {{ __('New Cashier') }}
        </button>
    </div>

    <div class="mt-6">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name or email') }}"
            class="w-full max-w-sm rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-amber-400 focus:outline-none">
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-white/10">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-900/60 text-slate-400">
                <tr>
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="px-4 py-3">{{ __('Email') }}</th>
                    <th class="px-4 py-3">{{ __('Phone') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($cashiers as $cashier)
                    <tr wire:key="cashier-{{ $cashier->id }}">
                        <td class="px-4 py-3">{{ $cashier->name }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $cashier->email }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $cashier->phone ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($cashier->is_active)
                                <span class="rounded-full bg-emerald-500/10 px-2 py-1 text-xs text-emerald-400">{{ __('Active') }}</span>
                            @else
                                <span class="rounded-full bg-red-500/10 px-2 py-1 text-xs text-red-400">{{ __('Inactive') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="openEdit({{ $cashier->id }})" class="text-amber-400 hover:underline">{{ __('Edit') }}</button>
                            <button wire:click="toggleActive({{ $cashier->id }})" wire:confirm="{{ __('Are you sure?') }}" class="ms-3 text-slate-400 hover:underline">
                                {{ $cashier->is_active ? __('Deactivate') : __('Activate') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">{{ __('No cashiers yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $cashiers->links() }}</div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 px-4" wire:click.self="$set('showModal', false)">
            <div class="w-full max-w-md rounded-2xl border border-white/10 bg-slate-950 p-6">
                <h2 class="mb-4 text-lg font-semibold text-amber-400">
                    {{ $editingId ? __('Edit Cashier') : __('New Cashier') }}
                </h2>
                <form wire:submit="save" class="space-y-4" autocomplete="off">
                    <div>
                        <label class="mb-1 block text-sm text-slate-300">{{ __('Name') }}</label>
                        <input type="text" name="cashier_name" wire:model="name" autocomplete="off" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                        @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-slate-300">{{ __('Email') }}</label>
                        <input type="email" name="cashier_email" wire:model="email" autocomplete="off" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                        @error('email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-slate-300">{{ __('Phone') }}</label>
                        <input type="text" name="cashier_phone" wire:model="phone" autocomplete="off" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                        @error('phone') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-slate-300">
                            {{ $editingId ? __('New Password (optional)') : __('Password') }}
                        </label>
                        <input type="password" name="cashier_password" wire:model="password" autocomplete="new-password" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                        @error('password') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showModal', false)" class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">{{ __('Cancel') }}</button>
                        <button type="submit" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-amber-400">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
