@props([
    'action',
    'following' => false,
    'count' => 0,
])

<div
    x-data="{
        following: @js($following),
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
                this.following = data.following;
                this.count = data.count;
            } finally {
                this.loading = false;
            }
        },
    }"
    class="inline-flex items-center gap-2 shrink-0"
>
    <button
        type="button"
        @click="toggle()"
        :disabled="loading"
        :title="following ? 'Unfollow this user' : 'Follow this user'"
        :aria-label="following ? 'Unfollow this user' : 'Follow this user'"
        :aria-pressed="following"
        @class([
            'font-mono text-xs px-3 py-1.5 rounded-lg border transition-all shrink-0',
            'focus:outline-none focus-visible:ring-2 focus-visible:ring-white/50 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950',
            'disabled:opacity-50 disabled:cursor-not-allowed',
        ])
        :class="following
            ? 'text-white/70 border-white/20 bg-white/5 hover:text-white hover:border-white/30 hover:bg-white/10'
            : 'text-indigo-300 border-indigo-400/30 bg-indigo-500/10 hover:text-white hover:border-indigo-400/50 hover:bg-indigo-500/20'"
        x-text="following ? 'following' : '+ follow'"
    >{{ $following ? 'following' : '+ follow' }}</button>
</div>
