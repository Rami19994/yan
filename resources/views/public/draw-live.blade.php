<x-layouts.app :title="__('Live Draw')">
<div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
    <h1 class="mb-6 text-center text-3xl font-extrabold tracking-wide text-amber-400">
        {{ $competition->name }}
    </h1>

    <div class="w-full max-w-5xl">
        <x-draw.stage :competition="$competition" />
    </div>

    <p data-draw-waiting class="mt-8 animate-pulse text-slate-400 {{ $finalResult ? 'hidden' : '' }}">
        {{ __('Waiting for the draw to begin...') }}
    </p>
</div>

<script>
    const competitionId = {{ $competition->id }};
    const finalResult = @json($finalResult);
    const waitingEl = document.querySelector('[data-draw-waiting]');

    function playResult(payload) {
        waitingEl?.classList.add('hidden');
        window.YanasibDraw?.play(payload);
    }

    window.addEventListener('draw-stage-ready', () => {
        // A late joiner viewing an already-completed competition still sees
        // the real outcome (server-rendered, not reconstructed client-side).
        if (finalResult) {
            playResult(finalResult);
        }
    }, { once: true });

    // Live sync: any draw started from the Admin Control Room while this
    // screen is open broadcasts here over the SAME public channel, so this
    // spectator screen plays the identical animation at the identical moment.
    //
    // This classic inline script runs as soon as the parser reaches it —
    // BEFORE app.js (a deferred module script in <head>, which is what
    // defines window.Echo) has executed. Module scripts are guaranteed to
    // run before DOMContentLoaded fires, so waiting for that event (rather
    // than checking `if (window.Echo)` immediately) is what makes this
    // reliable instead of silently never subscribing.
    function subscribeToDrawChannel() {
        window.Echo.channel(`draw.${competitionId}`).listen('.draw-result', (payload) => {
            playResult(payload);
        });
    }

    if (window.Echo) {
        subscribeToDrawChannel();
    } else {
        document.addEventListener('DOMContentLoaded', subscribeToDrawChannel);
    }
</script>
</x-layouts.app>
