@props(['title' => 'VERTEX'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data="terminalOverlay()"
    class="bg-transparent text-white min-h-screen selection:bg-indigo-500/30 selection:text-white antialiased"
>
    <div x-data="sidebarNav()" class="flex min-h-screen">
        <x-navbar />

        <div
            class="flex-1 flex flex-col min-w-0 w-full transition-[margin] duration-300 ease-in-out"
            :class="open ? 'md:ml-64' : 'md:ml-0'"
        >
            <header class="sticky top-0 z-30 h-14 flex items-center gap-3 px-4 md:px-6 border-b border-white/10 bg-white/5 backdrop-blur-xl shrink-0">
                <button
                    type="button"
                    @click="toggle()"
                    class="p-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 border border-white/10 hover:border-white/20 transition-colors cursor-pointer shrink-0"
                    :aria-expanded="open"
                    aria-label="Toggle navigation"
                >
                    <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="open" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <a
                    href="{{ route('home') }}"
                    class="group select-none min-w-0"
                >
                    <x-brand-logo size="sm" />
                </a>
            </header>

            <main class="flex-grow py-8 pb-24 px-6 md:px-12 max-w-[1200px] mx-auto w-full">
                {{ $slot }}
            </main>

            <x-footer />
        </div>
    </div>

    <x-terminal-overlay />

    <x-moderation-notice :notifications="$moderationNotifications" />
</body>
</html>
