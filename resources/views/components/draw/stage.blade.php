@props(['competition'])

{{--
    wire:ignore is essential here: this subtree is owned by the Three.js/GSAP
    draw engine (resources/js/draw.js) after first render. Without it,
    Livewire's DOM morph on every wire:click (even ones that dispatch an
    event without changing any public property) resets the canvas's
    JS-managed width/height attributes back to the server template's
    (attribute-less) markup, snapping the WebGL canvas back to the browser's
    default 300x150 intrinsic size and breaking the whole scene.
--}}
<div data-draw-root wire:ignore class="relative" data-draw-stage>
    <div data-draw-stage
        class="relative flex min-h-[70vh] flex-col items-center justify-center overflow-hidden rounded-2xl border border-amber-500/20 bg-gradient-to-b from-slate-950 via-[#060a14] to-black p-6"
        style="background-image: radial-gradient(ellipse at 50% 0%, rgba(251,191,36,0.08), transparent 60%);">

        {{-- Top bar --}}
        <div class="absolute inset-x-0 top-0 flex items-center justify-between p-4 text-xs text-slate-400">
            <div class="flex items-center gap-2">
                <span class="font-semibold text-amber-400">{{ $competition->name }}</span>
                <span data-draw-preview-badge class="hidden rounded-full border border-sky-400/50 bg-sky-500/10 px-2 py-0.5 font-bold uppercase tracking-widest text-sky-300">
                    {{ __('Preview Mode') }}
                </span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" data-draw-sound-toggle aria-pressed="false"
                    class="rounded-lg border border-white/10 bg-white/5 px-2 py-1 hover:bg-white/10" title="{{ __('Sound') }}">🔇</button>
                <button type="button" data-draw-fullscreen-toggle
                    class="rounded-lg border border-white/10 bg-white/5 px-2 py-1 hover:bg-white/10" title="{{ __('Fullscreen') }}">⛶</button>
            </div>
        </div>

        {{-- 3D canvas (wheels render here) --}}
        <div class="relative aspect-[7/2] w-full max-w-4xl">
            <canvas data-draw-canvas class="h-full w-full"></canvas>

            {{-- CSS fallback for no-WebGL devices --}}
            <div data-draw-fallback class="hidden absolute inset-0 grid grid-cols-7 gap-2">
                @for ($i = 0; $i < 7; $i++)
                    <div class="flex items-center justify-center rounded-lg border border-amber-500/30 bg-slate-900 text-3xl font-bold text-amber-400">
                        -
                    </div>
                @endfor
            </div>
        </div>

        {{-- Status / digits / winner --}}
        <div class="mt-8 text-center">
            <div data-draw-digits class="font-mono text-4xl font-bold tracking-[0.3em] text-amber-300"></div>
            <div data-draw-status class="mt-3 text-lg uppercase tracking-widest text-slate-300"></div>
            <div data-draw-winner-panel class="hidden mt-6 rounded-xl border border-amber-500/30 bg-amber-500/5 p-6"></div>
        </div>
    </div>
</div>

@once
    @vite(['resources/js/draw.js'])
@endonce
