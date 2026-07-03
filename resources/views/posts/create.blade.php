<x-layouts.app title="DEV_ARCHITECT — New Post">
    <div class="max-w-3xl space-y-6">
        <div>
            <a href="{{ route('posts.mine') }}" class="font-mono text-xs text-white/40 hover:text-white transition-colors">
                &larr; back to my posts
            </a>
            <h1 class="font-sans text-2xl md:text-3xl font-bold text-white mt-2">New Post</h1>
            <p class="font-sans text-sm text-white/50 mt-1">Share your thoughts with the community</p>
        </div>

        @if (session('success'))
            <div class="glass-card border border-green-500/30 bg-green-500/10 rounded-xl px-4 py-3">
                <p class="font-mono text-sm text-green-300">&gt; {{ session('success') }}</p>
            </div>
        @endif

        <div class="glass-card-heavy border border-white/15 rounded-xl p-6 md:p-8">
            <x-posts.form :categories="$categories" />
        </div>
    </div>
</x-layouts.app>
