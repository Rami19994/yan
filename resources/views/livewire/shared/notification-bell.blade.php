<div class="fixed bottom-4 end-4 z-50" x-data="{ open: false }" @click.outside="open = false">
    <button
        @click="open = !open"
        class="relative flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-slate-900/90 text-slate-200 shadow-lg backdrop-blur hover:bg-slate-800"
        aria-label="{{ __('Notifications') }}"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9" />
        </svg>
        @if ($unreadCount > 0)
            <span class="absolute -top-1 -end-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-amber-500 px-1 text-[10px] font-bold text-slate-950">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition
        class="absolute end-0 bottom-full mb-2 w-80 rounded-xl border border-white/10 bg-slate-900 shadow-xl"
    >
        <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
            <span class="text-sm font-semibold text-slate-200">{{ __('Notifications') }}</span>
            @if ($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-xs text-amber-400 hover:underline">{{ __('Mark all as read') }}</button>
            @endif
        </div>

        <div class="max-h-96 divide-y divide-white/5 overflow-y-auto">
            @forelse ($recent as $notification)
                <button
                    wire:click="markAsRead('{{ $notification->id }}')"
                    class="block w-full px-4 py-3 text-start text-sm {{ $notification->read_at ? 'text-slate-500' : 'text-slate-200' }} hover:bg-white/5"
                >
                    <div>{{ $notification->data['message'] ?? '' }}</div>
                    <div class="mt-1 text-xs text-slate-600">{{ $notification->created_at->diffForHumans() }}</div>
                </button>
            @empty
                <div class="px-4 py-6 text-center text-sm text-slate-500">{{ __('No notifications yet.') }}</div>
            @endforelse
        </div>

        <div class="border-t border-white/10 px-4 py-2 text-center">
            <a href="{{ route('notifications.index') }}" class="text-xs text-amber-400 hover:underline">{{ __('View all') }}</a>
        </div>
    </div>
</div>
