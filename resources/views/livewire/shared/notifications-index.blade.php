<div class="mx-auto max-w-2xl p-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-amber-400">{{ __('Notifications') }}</h1>
            <a href="{{ url()->previous() }}" class="text-sm text-slate-400 hover:text-slate-300">&larr; {{ __('Back') }}</a>
        </div>
        <button wire:click="markAllAsRead" class="rounded-lg bg-slate-800 px-4 py-2 text-sm hover:bg-slate-700">{{ __('Mark all as read') }}</button>
    </div>

    <div class="mt-6 divide-y divide-white/5 rounded-xl border border-white/10 bg-white/5">
        @forelse ($notifications as $notification)
            <div class="flex items-start justify-between gap-4 px-4 py-4 {{ $notification->read_at ? 'text-slate-400' : 'text-slate-100' }}">
                <div>
                    <div class="text-sm">{{ $notification->data['message'] ?? '' }}</div>
                    <div class="mt-1 text-xs text-slate-600">{{ $notification->created_at->format('Y-m-d H:i') }}</div>
                </div>
                @unless ($notification->read_at)
                    <button wire:click="markAsRead('{{ $notification->id }}')" class="shrink-0 rounded-lg bg-slate-800 px-3 py-1 text-xs hover:bg-slate-700">{{ __('Mark as read') }}</button>
                @endunless
            </div>
        @empty
            <div class="px-4 py-10 text-center text-sm text-slate-500">{{ __('No notifications yet.') }}</div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
</div>
