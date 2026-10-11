<div class="mx-auto max-w-2xl p-8">
    <div>
        <h1 class="text-2xl font-bold text-amber-400">{{ __('Settings') }}</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-slate-400 hover:text-slate-300">&larr; {{ __('Dashboard') }}</a>
    </div>

    @if ($successMessage)
        <div class="mt-6 rounded-lg border border-emerald-500/40 bg-emerald-500/10 p-3 text-sm text-emerald-300">
            {{ $successMessage }}
        </div>
    @endif

    <form wire:submit="save" class="mt-6 space-y-8">
        <section>
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-400">{{ __('General') }}</h2>
            <div class="space-y-5">
                <div>
                    <label class="mb-1 block text-sm text-slate-300">{{ __('Site Name') }}</label>
                    <input type="text" wire:model="siteName" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                    @error('siteName') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm text-slate-300">{{ __('Logo') }}</label>
                        @if ($currentLogoUrl)
                            <img src="{{ $currentLogoUrl }}" alt="Logo" class="mb-2 h-12 rounded bg-white/5 p-1">
                        @endif
                        <input type="file" wire:model="logoUpload" accept="image/*" class="w-full text-sm text-slate-300">
                        @error('logoUpload') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-slate-300">{{ __('Favicon') }}</label>
                        @if ($currentFaviconUrl)
                            <img src="{{ $currentFaviconUrl }}" alt="Favicon" class="mb-2 h-12 w-12 rounded bg-white/5 p-1">
                        @endif
                        <input type="file" wire:model="faviconUpload" accept="image/*" class="w-full text-sm text-slate-300">
                        @error('faviconUpload') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm text-slate-300">{{ __('Default Language') }}</label>
                        <select wire:model="defaultLanguage" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                            <option value="ar">{{ __('Arabic') }}</option>
                            <option value="en">{{ __('English') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-slate-300">{{ __('Currency Display') }}</label>
                        <input type="text" wire:model="currencyDisplay" maxlength="10" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm text-slate-300">{{ __('Timezone') }}</label>
                    <select wire:model="timezone" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                        @foreach ($timezones as $tz)
                            <option value="{{ $tz }}">{{ $tz }}</option>
                        @endforeach
                    </select>
                    @error('timezone') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-300">
                    <input type="checkbox" wire:model="maintenanceMode" class="rounded border-white/20 bg-slate-900">
                    {{ __('Maintenance Mode') }}
                    <span class="text-xs text-slate-500">({{ __('non-admins will see a maintenance page') }})</span>
                </label>
            </div>
        </section>

        <section>
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-400">{{ __('Draw') }}</h2>
            <div class="space-y-5">
                <div>
                    <label class="mb-1 block text-sm text-slate-300">{{ __('Draw Animation Speed') }}</label>
                    <select wire:model="drawAnimationSpeed" class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-amber-400 focus:outline-none">
                        <option value="slow">{{ __('Slow') }}</option>
                        <option value="normal">{{ __('Normal') }}</option>
                        <option value="fast">{{ __('Fast') }}</option>
                    </select>
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-300">
                    <input type="checkbox" wire:model="soundEnabled" class="rounded border-white/20 bg-slate-900">
                    {{ __('Sound Enabled by Default') }}
                </label>
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-amber-400">
                {{ __('Save') }}
            </button>
        </div>
    </form>
</div>
