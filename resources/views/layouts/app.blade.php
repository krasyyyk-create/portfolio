<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'DEV_ARCHITECT' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (Vite or CDN) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom Theme Variables -->
    <style>
        :root {
            --color-background: #0b1326;
            --color-on-background: #dae2fd;
        }
        .custom-glow {
            box-shadow: 0 0 15px rgba(0, 240, 255, 0.3);
        }
        .input-focus-glow:focus {
            box-shadow: inset 0 0 8px rgba(0, 240, 255, 0.2), 0 0 10px rgba(0, 240, 255, 0.15);
        }
        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23849495'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1.5em;
        }
    </style>
</head>
<body class="bg-[#0b1326] text-[#dae2fd] min-h-screen flex flex-col antialiased selection:bg-[#00f0ff] selection:text-[#00363a]">
    <!-- Navbar Component -->
    <x-navbar />

    <!-- Main Content -->
    <main class="flex-grow pt-32 pb-24 px-4 md:px-12 max-w-[1200px] mx-auto w-full">
        {{ $slot }}
    </main>

    <!-- Footer Component -->
    <x-footer />
</body>
</html>