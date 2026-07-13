@props([
    'size' => 'md',
    'showText' => true,
])

@php
    $markSize = match ($size) {
        'sm' => 'w-6 h-6 rounded-md',
        'md' => 'w-8 h-8 rounded-lg',
        default => 'w-8 h-8 rounded-lg',
    };

    $textSize = match ($size) {
        'sm' => 'text-sm md:text-base',
        'md' => 'text-lg',
        default => 'text-lg',
    };
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-2.5 min-w-0']) }}>
    <img
        src="{{ asset('images/logo.svg') }}"
        alt=""
        class="{{ $markSize }} shrink-0 shadow-md shadow-indigo-500/20 group-hover:rotate-12 transition-transform duration-300"
        width="32"
        height="32"
        aria-hidden="true"
    />

    @if ($showText)
        <span class="truncate font-sans font-bold tracking-tight text-white {{ $textSize }}">VERTEX</span>
    @endif
</div>
