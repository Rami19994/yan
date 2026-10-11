<x-layouts.app :title="__('Verify Ticket')">
<div class="flex min-h-screen items-center justify-center px-4 py-10">
    <div class="w-full max-w-md rounded-2xl border border-red-500/20 bg-white/5 p-8 text-center">
        <h1 class="text-xl font-bold text-red-400">{{ __('Ticket Not Found') }}</h1>
        <p class="mt-2 text-sm text-slate-400">{{ __('This verification link is invalid or has expired.') }}</p>
    </div>
</div>
</x-layouts.app>
