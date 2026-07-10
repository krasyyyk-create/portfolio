@auth
    @if (auth()->user()->isAdmin() && ! request()->routeIs('admin.*'))
        <a
            href="{{ route('admin.dashboard') }}"
            class="fixed bottom-6 left-6 z-40 p-3.5 text-white/90 hover:text-white bg-indigo-500/90 hover:bg-indigo-500 border border-white/10 hover:border-white/20 transition-all duration-300 rounded-full active:scale-95 cursor-pointer flex items-center justify-center shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/40"
            title="Admin Panel"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
        </a>
    @endif
@endauth
