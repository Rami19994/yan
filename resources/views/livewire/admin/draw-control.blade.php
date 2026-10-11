<div class="mx-auto max-w-5xl p-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-amber-400">{{ __('Draw Control Room') }} — {{ $competition->name }}</h1>
            <a href="{{ route('admin.competitions.index') }}" class="text-sm text-slate-400 hover:text-slate-300">&larr; {{ __('Competitions') }}</a>
        </div>
        <a href="{{ route('draw.live', $competition) }}" target="_blank" class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">
            {{ __('Open Public Screen') }} ↗
        </a>
    </div>

    <div class="mt-4 flex gap-4 text-sm text-slate-300">
        <div>{{ __('Status') }}: <span class="text-amber-400">{{ __($competition->status->label()) }}</span></div>
        <div>{{ __('Draw Mode') }}: <span class="text-amber-400">{{ $competition->draw_mode->value }}</span></div>
        <div>{{ __('No Winner Policy') }}: <span class="text-amber-400">{{ $competition->no_winner_policy->value }}</span></div>
    </div>

    @if ($errorMessage)
        <div class="mt-4 rounded-lg border border-red-500/40 bg-red-500/10 p-3 text-sm text-red-300">{{ $errorMessage }}</div>
    @endif

    <div class="mt-6">
        <x-draw.stage :competition="$competition" />
    </div>

    <div class="mt-6 flex gap-3">
        <button wire:click="preview" class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">
            {{ __('Preview Draw') }}
        </button>
        <button wire:click="resetPreview" class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">
            {{ __('Reset Preview') }}
        </button>
        <button wire:click="startOfficialDraw" wire:confirm="{{ __('Start the official draw? This cannot be undone.') }}"
            class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-amber-400">
            {{ __('Start Official Draw') }}
        </button>
    </div>
</div>
