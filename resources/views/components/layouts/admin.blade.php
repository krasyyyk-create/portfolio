@props(['title' => 'Admin — DEV_ARCHITECT'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-white min-h-screen antialiased selection:bg-indigo-500/30 selection:text-white">
    <div class="flex min-h-screen">
        <x-admin.sidebar />

        <div class="flex-1 flex flex-col min-w-0">
            <header class="h-16 border-b border-white/10 bg-white/5 backdrop-blur-xl flex items-center justify-between px-6 md:px-8 shrink-0">
                <div class="font-mono text-xs text-white/50">
                    <span class="text-indigo-400">admin@</span>dev_architect<span class="text-white/30">:~$</span>
                    <span class="text-white/70">{{ $header ?? 'dashboard' }}</span>
                </div>

                <div class="flex items-center gap-4">
                    <span class="hidden sm:block font-sans text-sm text-white/60">{{ auth()->user()->name }}</span>
                    <a
                        href="{{ route('home') }}"
                        class="font-mono text-xs text-white/50 hover:text-white border border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10 px-3 py-1.5 rounded-lg transition-all"
                    >
                        &gt; site
                    </a>
                    <div x-data="{ open: false }" class="relative">
                        <button
                            type="button"
                            @click="open = !open"
                            @click.outside="open = false"
                            class="font-mono text-xs text-white/50 hover:text-white border border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10 px-3 py-1.5 rounded-lg transition-all flex items-center gap-2 cursor-pointer"
                        >
                            @if (auth()->user()->avatar_url)
                                <img
                                    src="{{ auth()->user()->avatar_url }}"
                                    alt=""
                                    class="w-4 h-4 rounded-full object-cover"
                                />
                            @endif
                            &gt; profile
                            <svg class="w-2.5 h-2.5 text-white/50 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-transition
                            class="absolute right-0 top-full mt-2 w-44 py-2 bg-slate-900/95 backdrop-blur-xl border border-white/10 rounded-lg shadow-xl shadow-black/20 z-50"
                        >
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 font-mono text-xs text-white/70 hover:text-white hover:bg-white/5 transition-colors">
                                &gt; edit profile
                            </a>
                            <div class="my-1 border-t border-white/10"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button
                                    type="submit"
                                    class="block w-full text-left px-4 py-2 font-mono text-xs text-white/70 hover:text-white hover:bg-white/5 transition-colors cursor-pointer"
                                >
                                    &gt; logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <nav class="md:hidden flex border-b border-white/10 bg-slate-900/80">
                <a href="{{ route('admin.dashboard') }}" @class(['flex-1 text-center font-mono text-xs py-3 transition-colors', 'text-indigo-400 border-b-2 border-indigo-400' => request()->routeIs('admin.dashboard'), 'text-white/50' => ! request()->routeIs('admin.dashboard')])>dashboard</a>
                <a href="{{ route('admin.users.index') }}" @class(['flex-1 text-center font-mono text-xs py-3 transition-colors', 'text-indigo-400 border-b-2 border-indigo-400' => request()->routeIs('admin.users.*'), 'text-white/50' => ! request()->routeIs('admin.users.*')])>users</a>
                <a href="{{ route('admin.posts.index') }}" @class(['flex-1 text-center font-mono text-xs py-3 transition-colors', 'text-indigo-400 border-b-2 border-indigo-400' => request()->routeIs('admin.posts.*'), 'text-white/50' => ! request()->routeIs('admin.posts.*')])>posts</a>
                <a href="{{ route('admin.reported.index') }}" @class(['flex-1 text-center font-mono text-xs py-3 transition-colors', 'text-indigo-400 border-b-2 border-indigo-400' => request()->routeIs('admin.reported.*'), 'text-white/50' => ! request()->routeIs('admin.reported.*')])>reported</a>
            </nav>

            <main class="flex-1 p-6 md:p-8 overflow-auto">
                @if (session('success'))
                    <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-lg font-mono text-sm text-emerald-400">
                        &gt; {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-lg font-mono text-sm text-red-400 space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>&gt; {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
