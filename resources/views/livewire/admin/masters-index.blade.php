<div class="p-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-amber-400">{{ __('Masters') }}</h1>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-slate-400 hover:text-slate-300">&larr; {{ __('Dashboard') }}</a>
        </div>
        <button wire:click="openCreate" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-amber-400">
            {{ __('New Master') }}
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
                    <th class="px-4 py-3">{{ __('Cashiers') }}</th>
                    <th class="px-4 py-3">{{ __('Tickets Sent') }}</th>
                    <th class="px-4 py-3">{{ __('Tickets Distributed') }}</th>
                    <th class="px-4 py-3">{{ __('Tickets Remaining') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($masters as $master)
                    @php $stats = $ticketStats->get($master->id, ['sent' => 0, 'distributed' => 0, 'remaining' => 0]) @endphp
                    <tr wire:key="master-{{ $master->id }}">
                        <td class="px-4 py-3">{{ $master->name }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $master->email }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $master->phone ?: '—' }}</td>
                        <td class="px-4 py-3">{{ $master->cashiers_count }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ number_format($stats['sent']) }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ number_format($stats['distributed']) }}</td>
                        <td class="px-4 py-3 text-amber-400">{{ number_format($stats['remaining']) }}</td>
                        <td class="px-4 py-3">
                            @if ($master->is_active)
                                <span class="rounded-full bg-emerald-500/10 px-2 py-1 text-xs text-emerald-400">{{ __('Active') }}</span>
                            @else
                                <span class="rounded-full bg-red-500/10 px-2 py-1 text-xs text-red-400">{{ __('Inactive') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="openEdit({{ $master->id }})" class="text-amber-400 hover:underline">{{ __('Edit') }}</button>
                            <button wire:click="toggleActive({{ $master->id }})" wire:confirm="{{ __('Are you sure?') }}" class="ms-3 text-slate-400 hover:underline">
                                {{ $master->is_active ? __('Deactivate') : __('Activate') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-slate-500">{{ __('No masters yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $masters->links() }}</div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 px-4" wire:click.self="$set('showModal', false)">
            <div class="w-full max-w-md rounded-2xl border border-white/10 bg-slate-950 p-6">
                <h2 class="mb-4 text-lg font-semibold text-amber-400">
                    {{ $editingId ? __('Edit Master') : __('New Master') }}
                </h2>
                <form wire:submit="save" class="space-y-4" autocomplete="off">
                    <div>
                        <label class="mb-1 block text-sm text-slate-300">{{ __('Name') }}</label>
                        <input type="text" name="master_name" wire:model="name" autocomplete="off" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                        @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-slate-300">{{ __('Email') }}</label>
                        <input type="email" name="master_email" wire:model="email" autocomplete="off" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                        @error('email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-slate-300">{{ __('Phone') }}</label>
                        <input type="text" name="master_phone" wire:model="phone" autocomplete="off" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                        @error('phone') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-slate-300">
                            {{ $editingId ? __('New Password (optional)') : __('Password') }}
                        </label>
                        <input type="password" name="master_password" wire:model="password" autocomplete="new-password" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
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
