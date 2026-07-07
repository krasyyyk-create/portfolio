@props([
    'action',
    'count' => 0,
    'liked' => false,
    'size' => 'md',
    'showCount' => true,
])

@php
    $buttonSize = $size === 'md'
        ? 'w-16 h-16 rounded-2xl'
        : 'w-12 h-12 rounded-xl';

    $iconSize = $size === 'md'
        ? 'w-7 h-7'
        : 'w-6 h-6';

    $countSize = $size === 'md'
        ? 'text-sm'
        : 'text-xs';
@endphp

<div
    x-data="{
        liked: @js($liked),
        count: @js($count),
        loading: false,
        async toggle() {
            if (this.loading) return;
            this.loading = true;

            try {
                const response = await fetch(@js($action), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content ?? '',
                        'Accept': 'application/json',
                    },
                });

                if (! response.ok) return;

                const data = await response.json();
                this.liked = data.liked;
                this.count = data.count;
            } finally {
                this.loading = false;
            }
        },
    }"
    class="inline-flex items-center gap-2 shrink-0"
>
    @auth
        <button
            type="button"
            @click="toggle()"
            :disabled="loading"
            :title="liked ? 'Unlike this post' : 'Like this post'"
            :aria-label="liked ? 'Unlike this post' : 'Like this post'"
            :aria-pressed="liked"
            @class([
                'group relative inline-flex items-center justify-center border-2 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed',
                'focus:outline-none focus-visible:ring-2 focus-visible:ring-white/50 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950',
                $buttonSize,
            ])
            :class="liked
                ? 'bg-rose-500/20 border-rose-400/60 text-rose-400 hover:bg-rose-500/30 hover:border-rose-400'
                : 'bg-transparent border-white/70 text-white/80 hover:bg-white hover:border-white hover:text-slate-950'"
        >
            <svg
                @class([$iconSize, 'transition-transform'])
                :class="loading ? 'scale-90' : ''"
                :fill="liked ? 'currentColor' : 'none'"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    :stroke-width="liked ? 0 : 2"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                />
            </svg>
        </button>
    @else
        <a
            href="{{ route('login') }}"
            title="Sign in to like this post"
            aria-label="Sign in to like this post"
            @class([
                'group relative inline-flex items-center justify-center border-2 transition-all active:scale-95',
                'bg-transparent border-white/70 text-white/80',
                'hover:bg-white hover:border-white hover:text-slate-950',
                'focus:outline-none focus-visible:ring-2 focus-visible:ring-white/50 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950',
                $buttonSize,
            ])
        >
            <svg @class([$iconSize]) fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </a>
    @endauth

    @if ($showCount)
        <span
            x-show="count > 0"
            x-cloak
            @class([
                'font-mono text-white/50 tabular-nums',
                $countSize,
            ])
            x-text="count"
        >{{ $count > 0 ? $count : '' }}</span>
    @endif
</div>
