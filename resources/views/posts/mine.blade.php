<x-layouts.app title="VERTEX — My Posts">
    <div class="space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div class="space-y-2">
                <h1 class="font-sans text-3xl font-bold text-white">My Posts</h1>
                <p class="font-sans text-sm text-white/60">Manage your drafts and published posts</p>
            </div>
            <a
                href="{{ route('posts.create') }}"
                class="inline-flex items-center justify-center bg-indigo-500/85 hover:bg-indigo-500 border border-white/10 text-white font-sans font-semibold px-5 py-2.5 rounded-lg active:scale-[0.98] transition-all shadow-lg shadow-indigo-500/25"
            >
                + New Post
            </a>
        </div>

        @if (session('success'))
            <div class="glass-card border border-green-500/30 bg-green-500/10 rounded-xl px-4 py-3">
                <p class="font-mono text-sm text-green-300">&gt; {{ session('success') }}</p>
            </div>
        @endif

        @if ($posts->isEmpty())
            <div class="glass-card rounded-2xl p-12 text-center space-y-4">
                <p class="font-mono text-sm text-white/40">You haven't written any posts yet.</p>
                <a
                    href="{{ route('posts.create') }}"
                    class="inline-flex font-mono text-sm text-indigo-300 hover:text-white transition-colors"
                >
                    &gt; write your first post
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($posts as $post)
                    <article class="glass-card border border-white/10 rounded-xl p-5 md:p-6 hover:border-indigo-400/30 transition-colors">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                            <div class="space-y-2 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($post->is_published && $post->published_at?->isPast())
                                        <span class="font-mono text-[10px] uppercase tracking-wider text-green-400 bg-green-500/10 border border-green-500/20 px-2 py-0.5 rounded-full">
                                            published
                                        </span>
                                    @else
                                        <span class="font-mono text-[10px] uppercase tracking-wider text-yellow-400 bg-yellow-500/10 border border-yellow-500/20 px-2 py-0.5 rounded-full">
                                            draft
                                        </span>
                                    @endif
                                    @if ($post->categories->isNotEmpty())
                                        <x-post-categories :categories="$post->categories" />
                                    @endif
                                </div>
                                <h2 class="font-sans text-lg font-bold text-white truncate">{{ $post->title }}</h2>
                                @if ($post->excerpt)
                                    <p class="font-sans text-sm text-white/60 line-clamp-2">{{ $post->excerpt }}</p>
                                @endif
                                <p class="font-mono text-[10px] text-white/40">
                                    updated {{ $post->updated_at->diffForHumans() }}
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 shrink-0">
                                @if ($post->is_published && $post->published_at?->isPast())
                                    <a
                                        href="{{ route('posts.show', $post) }}"
                                        class="font-mono text-xs text-white/60 hover:text-white border border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10 px-3 py-1.5 rounded-lg transition-all"
                                    >
                                        view
                                    </a>
                                @endif
                                <a
                                    href="{{ route('posts.edit', $post) }}"
                                    class="font-mono text-xs text-indigo-300 hover:text-white border border-indigo-400/30 hover:border-indigo-400/50 bg-indigo-500/10 hover:bg-indigo-500/20 px-3 py-1.5 rounded-lg transition-all"
                                >
                                    edit
                                </a>
                                <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Delete this post?')">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="font-mono text-xs text-red-400 hover:text-red-300 border border-red-500/30 hover:border-red-500/50 bg-red-500/10 hover:bg-red-500/20 px-3 py-1.5 rounded-lg transition-all cursor-pointer"
                                    >
                                        delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($posts->hasPages())
                <div class="pt-2">
                    {{ $posts->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>
