@php
    $navItems = [
        ['route' => 'home', 'label' => 'home', 'path' => '/'],
        ['route' => 'projects', 'label' => 'projects', 'path' => '/projects'],
        ['route' => 'posts.index', 'label' => 'posts', 'path' => '/posts'],
        ['route' => 'services', 'label' => 'services', 'path' => '/services'],
        ['route' => 'contact.index', 'label' => 'contact', 'path' => '/contact'],
    ];
@endphp

<nav class="fixed top-0 w-full z-40 bg-white/5 backdrop-blur-xl border-b border-white/10 shadow-lg shadow-black/5">
    <div class="flex justify-between items-center max-w-[1200px] mx-auto px-6 md:px-12 h-20">
        <a
            href="{{ route('home') }}"
            class="font-sans text-xl md:text-2xl font-bold tracking-tight text-white flex items-center gap-2.5 group select-none"
        >
            <div class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 font-bold group-hover:rotate-12 transition-transform duration-300">D</div>
            <span>DEV_ARCHITECT</span>
        </a>

        <div class="hidden md:flex items-center gap-8">
            @foreach ($navItems as $item)
                <a
                    href="{{ route($item['route']) }}"
                    @class([
                        'font-mono text-sm capitalize transition-all relative py-1',
                        'text-white font-bold' => request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*'),
                        'text-white/60 hover:text-white' => ! request()->routeIs($item['route']) && ! request()->routeIs($item['route'].'.*'),
                    ])
                >
                    {{ $item['label'] }}
                    @if (request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*'))
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-400 rounded-full"></span>
                    @endif
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-3 md:gap-4 shrink-0">
            @auth
                <div class="hidden md:flex items-center gap-3">
                    @if (auth()->user()->isAdmin())
                        <a
                            href="{{ route('admin.dashboard') }}"
                            class="font-mono text-sm text-indigo-400 hover:text-indigo-300 border border-indigo-400/30 hover:border-indigo-400/50 bg-indigo-500/10 hover:bg-indigo-500/20 px-4 py-2 rounded-lg transition-all whitespace-nowrap"
                        >
                            &gt; admin
                        </a>
                    @endif
                    <div x-data="{ open: false }" class="relative">
                        <button
                            type="button"
                            @click="open = !open"
                            @click.outside="open = false"
                            @class([
                                'font-mono text-sm border px-4 py-2 rounded-lg transition-all whitespace-nowrap flex items-center gap-2 cursor-pointer',
                                'text-white border-white/20 bg-white/10' => request()->routeIs('profile.*') || request()->routeIs('account.*'),
                                'text-white/60 hover:text-white border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10' => ! request()->routeIs('profile.*') && ! request()->routeIs('account.*'),
                            ])
                        >
                            @if (auth()->user()->avatar_url)
                                <img
                                    src="{{ auth()->user()->avatar_url }}"
                                    alt=""
                                    class="w-5 h-5 rounded-full object-cover"
                                />
                            @else
                                <span class="w-5 h-5 rounded-full bg-indigo-500/40 flex items-center justify-center text-[10px] font-bold text-indigo-200">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            @endif
                            &gt; profile
                            <svg class="w-3 h-3 text-white/50 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-transition
                            class="absolute right-0 top-full mt-2 w-48 py-2 bg-slate-900/95 backdrop-blur-xl border border-white/10 rounded-lg shadow-xl shadow-black/20 z-50"
                        >
                            <a
                                href="{{ route('users.show', auth()->user()) }}"
                                @class([
                                    'block px-4 py-2.5 font-mono text-sm transition-colors',
                                    'text-white bg-white/10' => request()->routeIs('users.show') && request()->route('user')?->is(auth()->user()),
                                    'text-white/70 hover:text-white hover:bg-white/5' => ! request()->routeIs('users.show') || ! request()->route('user')?->is(auth()->user()),
                                ])
                            >
                                &gt; view profile
                            </a>
                            <a
                                href="{{ route('profile.edit') }}"
                                @class([
                                    'block px-4 py-2.5 font-mono text-sm transition-colors',
                                    'text-white bg-white/10' => request()->routeIs('profile.*') || request()->routeIs('account.*'),
                                    'text-white/70 hover:text-white hover:bg-white/5' => ! request()->routeIs('profile.*') && ! request()->routeIs('account.*'),
                                ])
                            >
                                &gt; edit profile
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="hidden md:flex items-center gap-3">
                    <a
                        href="{{ route('register') }}"
                        class="font-mono text-sm text-white/60 hover:text-white border border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10 px-4 py-2 rounded-lg transition-all whitespace-nowrap"
                    >
                        &gt; register
                    </a>
                    <a
                        href="{{ route('login') }}"
                        class="font-mono text-sm text-white/60 hover:text-white border border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10 px-4 py-2 rounded-lg transition-all whitespace-nowrap"
                    >
                        &gt; login
                    </a>
                </div>
            @endauth

            <a
                href="{{ route('contact.index') }}"
                class="hidden md:block bg-indigo-500/80 hover:bg-indigo-500 text-white border border-white/10 font-sans text-sm px-6 py-2 rounded-lg font-semibold active:scale-95 transition-all shadow-md shadow-indigo-500/10 hover:shadow-indigo-500/25 whitespace-nowrap shrink-0"
            >
                Hire Me
            </a>
        </div>
    </div>
</nav>
