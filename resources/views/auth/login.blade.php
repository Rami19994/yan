<x-layouts.app :title="__('Login')">
<div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-sm rounded-2xl border border-white/10 bg-white/5 p-8 shadow-xl backdrop-blur">
        <h1 class="mb-1 text-2xl font-bold text-amber-400">{{ config('app.name') }}</h1>
        <p class="mb-6 text-sm text-slate-400">{{ __('All rights reserved.') }}</p>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-500/40 bg-red-500/10 p-3 text-sm text-red-300">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm text-slate-300">{{ __('Email') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-slate-100 focus:border-amber-400 focus:outline-none">
            </div>
            <div>
                <label class="mb-1 block text-sm text-slate-300">{{ __('Password') }}</label>
                <input type="password" name="password" required
                    class="w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-slate-100 focus:border-amber-400 focus:outline-none">
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-400">
                <input type="checkbox" name="remember"> {{ __('Remember me') }}
            </label>
            <button type="submit"
                class="w-full rounded-lg bg-amber-500 px-4 py-2 font-semibold text-slate-950 transition hover:bg-amber-400">
                {{ __('Sign in') }}
            </button>
        </form>
    </div>
</div>
</x-layouts.app>
