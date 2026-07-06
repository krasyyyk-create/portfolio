@props(['title' => 'DEV_ARCHITECT'])

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
    class="bg-transparent text-white min-h-screen flex flex-col selection:bg-indigo-500/30 selection:text-white antialiased"
>
    <x-navbar />

    <main class="flex-grow pt-32 pb-24 px-6 md:px-12 max-w-[1200px] mx-auto w-full">
        {{ $slot }}
    </main>

    <x-footer />

    <x-terminal-overlay />

    <x-moderation-notice :notifications="$moderationNotifications" />
</body>
</html>
