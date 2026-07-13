@php
    $navItems = [
        ['route' => 'home', 'label' => 'home', 'path' => '/'],
        ['route' => 'projects', 'label' => 'projects', 'path' => '/projects'],
        ['route' => 'posts.index', 'label' => 'posts', 'path' => '/posts'],
        ['route' => 'services', 'label' => 'services', 'path' => '/services'],
        ['route' => 'reservations.index', 'label' => 'book', 'path' => '/reservations'],
        ['route' => 'contact.index', 'label' => 'contact', 'path' => '/contact'],
    ];
@endphp

{{-- Mobile backdrop --}}
<div
    x-show="open"
    x-transition:enter="transition-opacity ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="close()"
    class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm md:hidden"
    x-cloak
></div>

<aside
    class="fixed top-0 left-0 z-50 h-full w-64 flex flex-col border-r border-white/10 bg-slate-900/95 backdrop-blur-xl shadow-xl shadow-black/20 transition-transform duration-300 ease-in-out -translate-x-full"
    :class="{ 'translate-x-0': open }"
>
    <div class="h-16 flex items-center justify-between px-5 border-b border-white/10 shrink-0">
        <a
            href="{{ route('home') }}"
            class="group select-none min-w-0"
        >
            <x-brand-logo />
        </a>

        <button
            type="button"
            @click="close()"
            class="p-1.5 rounded-lg text-white/50 hover:text-white hover:bg-white/10 transition-colors cursor-pointer shrink-0"
            aria-label="Close navigation"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto p-4 space-y-1">
        @foreach ($navItems as $item)
            <a
                href="{{ route($item['route']) }}"
                @click="closeOnMobile()"
                @class([
                    'flex items-center gap-3 px-3 py-2.5 rounded-lg font-mono text-sm capitalize transition-all',
                    'bg-indigo-500/20 text-white border border-indigo-400/30' => request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*'),
                    'text-white/60 hover:text-white hover:bg-white/5 border border-transparent' => ! request()->routeIs($item['route']) && ! request()->routeIs($item['route'].'.*'),
                ])
            >
                <span class="text-indigo-400/80">&gt;</span>
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="p-4 border-t border-white/10 space-y-3 shrink-0">
        @auth
            <x-admin-panel-button @click="closeOnMobile()" />
            <div x-data="{ profileOpen: false }" class="relative">
                <button
                    type="button"
                    @click="profileOpen = !profileOpen"
                    @click.outside="profileOpen = false"
                    @class([
                        'w-full font-mono text-sm border px-3 py-2.5 rounded-lg transition-all flex items-center gap-2 cursor-pointer',
                        'text-white border-white/20 bg-white/10' => request()->routeIs('profile.*') || request()->routeIs('account.*'),
                        'text-white/60 hover:text-white border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10' => ! request()->routeIs('profile.*') && ! request()->routeIs('account.*'),
                    ])
                >
                    @if (auth()->user()->avatar_url)
                        <img
                            src="{{ auth()->user()->avatar_url }}"
                            alt=""
                            class="w-5 h-5 rounded-full object-cover shrink-0"
                        />
                    @else
                        <span class="w-5 h-5 rounded-full bg-indigo-500/40 flex items-center justify-center text-[10px] font-bold text-indigo-200 shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    @endif
                    <span class="flex-1 text-left truncate">&gt; profile</span>
                    <svg class="w-3 h-3 text-white/50 transition-transform shrink-0" :class="profileOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div
                    x-show="profileOpen"
                    x-transition
                    class="absolute bottom-full left-0 right-0 mb-2 py-2 bg-slate-900/95 backdrop-blur-xl border border-white/10 rounded-lg shadow-xl shadow-black/20 z-50"
                >
                    <a
                        href="{{ route('users.show', auth()->user()) }}"
                        @click="closeOnMobile()"
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
                        @click="closeOnMobile()"
                        @class([
                            'block px-4 py-2.5 font-mono text-sm transition-colors',
                            'text-white bg-white/10' => request()->routeIs('profile.*') || request()->routeIs('account.*'),
                            'text-white/70 hover:text-white hover:bg-white/5' => ! request()->routeIs('profile.*') && ! request()->routeIs('account.*'),
                        ])
                    >
                        &gt; edit profile
                    </a>
                    <div class="my-1 border-t border-white/10"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            class="block w-full text-left px-4 py-2.5 font-mono text-sm text-white/70 hover:text-white hover:bg-white/5 transition-colors cursor-pointer"
                        >
                            &gt; logout
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="space-y-2">
                <a
                    href="{{ route('register') }}"
                    @click="closeOnMobile()"
                    class="block w-full text-center font-mono text-sm text-white/60 hover:text-white border border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10 px-3 py-2.5 rounded-lg transition-all"
                >
                    &gt; register
                </a>
                <a
                    href="{{ route('login') }}"
                    @click="closeOnMobile()"
                    class="block w-full text-center font-mono text-sm text-white/60 hover:text-white border border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10 px-3 py-2.5 rounded-lg transition-all"
                >
                    &gt; login
                </a>
            </div>
        @endauth

        <a
            href="{{ route('contact.index') }}"
            @click="closeOnMobile()"
            class="block w-full text-center bg-indigo-500/80 hover:bg-indigo-500 text-white border border-white/10 font-sans text-sm px-4 py-2.5 rounded-lg font-semibold active:scale-95 transition-all shadow-md shadow-indigo-500/10 hover:shadow-indigo-500/25"
        >
            Hire Me
        </a>
    </div>
</aside>
