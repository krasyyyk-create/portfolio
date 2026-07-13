@auth
    @if (auth()->user()->isAdmin() && ! request()->routeIs('admin.*'))
        <a
            href="{{ route('admin.dashboard') }}"
            {{ $attributes->merge([
                'class' => 'block w-full font-mono text-sm border px-3 py-2.5 rounded-lg transition-all flex items-center gap-2 text-indigo-300 hover:text-indigo-200 border-indigo-400/30 hover:border-indigo-400/50 bg-indigo-500/10 hover:bg-indigo-500/20',
            ]) }}
            title="Admin Panel"
        >
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
            <span class="flex-1 text-left truncate">&gt; admin panel</span>
        </a>
    @endif
@endauth
