@php
    $navItems = [
        ['route' => 'home', 'label' => 'home', 'path' => '/'],
        ['route' => 'projects', 'label' => 'projects', 'path' => '/projects'],
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
                        'text-white font-bold' => request()->routeIs($item['route']),
                        'text-white/60 hover:text-white' => ! request()->routeIs($item['route']),
                    ])
                >
                    {{ $item['label'] }}
                    @if (request()->routeIs($item['route']))
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-400 rounded-full"></span>
                    @endif
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-4">
            <button
                @click="toggle()"
                type="button"
                class="p-2.5 text-white/80 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 transition-all duration-300 rounded-lg active:scale-95 cursor-pointer flex items-center justify-center"
                title="Toggle Systems Terminal"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </button>

            <a
                href="{{ route('contact.index') }}"
                class="hidden md:block bg-indigo-500/80 hover:bg-indigo-500 text-white border border-white/10 font-sans text-sm px-6 py-2 rounded-lg font-semibold active:scale-95 transition-all shadow-md shadow-indigo-500/10 hover:shadow-indigo-500/25"
            >
                Hire Me
            </a>
        </div>
    </div>
</nav>
