@php
    $navItems = [
        ['route' => 'admin.dashboard', 'label' => 'dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['route' => 'admin.users.index', 'label' => 'users', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
        ['route' => 'admin.posts.index', 'label' => 'posts', 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
        ['route' => 'admin.categories.index', 'label' => 'categories', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
        ['route' => 'admin.reported.index', 'label' => 'reported', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'badge' => $pendingReportCount ?? 0],
        ['route' => 'admin.chat.index', 'label' => 'live chat', 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
    ];
@endphp

<aside class="w-64 shrink-0 border-r border-white/10 bg-slate-900/80 backdrop-blur-xl hidden md:flex flex-col">
    <div class="h-16 flex items-center px-6 border-b border-white/10">
        <a href="{{ route('admin.dashboard') }}" class="font-sans text-lg font-bold tracking-tight text-white flex items-center gap-2.5 group">
            <div class="w-7 h-7 rounded-lg bg-indigo-500 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 font-bold text-sm group-hover:rotate-12 transition-transform duration-300">A</div>
            <span class="font-mono text-sm">ADMIN_PANEL</span>
        </a>
    </div>

    <nav class="flex-1 p-4 space-y-1">
        @foreach ($navItems as $item)
            <a
                href="{{ route($item['route']) }}"
                @class([
                    'flex items-center gap-3 px-3 py-2.5 rounded-lg font-mono text-sm transition-all',
                    'bg-indigo-500/20 text-white border border-indigo-400/30' => request()->routeIs($item['route'] . '*'),
                    'text-white/60 hover:text-white hover:bg-white/5 border border-transparent' => ! request()->routeIs($item['route'] . '*'),
                ])
            >
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                </svg>
                <span class="flex-1">{{ $item['label'] }}</span>
                @if (! empty($item['badge']))
                    <span class="font-mono text-[10px] px-1.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-400/30">
                        {{ $item['badge'] }}
                    </span>
                @endif
            </a>
        @endforeach
    </nav>

    <div class="p-4 border-t border-white/10">
        <div class="glass-card rounded-lg p-3 space-y-1">
            <p class="font-mono text-[10px] text-white/40 uppercase tracking-wider">System Status</p>
            <p class="font-mono text-xs text-emerald-400 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                OPERATIONAL
            </p>
        </div>
    </div>
</aside>
