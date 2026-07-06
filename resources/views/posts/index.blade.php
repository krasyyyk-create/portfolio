<x-layouts.app title="DEV_ARCHITECT — Posts">
    <div class="space-y-10">
        <div class="space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="space-y-3">
                    <h1 class="font-sans text-3xl font-bold text-white flex items-center gap-2.5">
                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                <span>ENGINEERING_LOG</span>
                    </h1>
                    <p class="font-sans text-white/70 max-w-xl text-sm leading-relaxed">
                        Technical write-ups, architecture notes, and lessons from building production systems.
                    </p>
                </div>
                @auth
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <a
                            href="{{ route('posts.mine') }}"
                            class="inline-flex items-center justify-center font-mono text-sm text-white/60 hover:text-white border border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10 px-5 py-2.5 rounded-lg transition-all"
                        >
                            &gt; my posts
                        </a>
                        <a
                            href="{{ route('posts.create') }}"
                            class="inline-flex items-center justify-center bg-indigo-500/85 hover:bg-indigo-500 border border-white/10 text-white font-sans text-sm font-semibold px-5 py-2.5 rounded-lg active:scale-[0.98] transition-all shadow-lg shadow-indigo-500/25"
                        >
                            + Write Post
                        </a>
                    </div>
                @endauth
            </div>
        </div>

        <form
            action="{{ route('posts.index') }}"
            method="GET"
            class="glass-card border border-white/10 rounded-xl p-4 flex flex-col sm:flex-row gap-3"
        >
            @if ($activeCategory)
                <input type="hidden" name="category" value="{{ $activeCategory->slug }}">
            @endif

            <label for="post-search" class="sr-only">Search posts</label>
            <div class="relative flex-grow">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white/30 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    id="post-search"
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Search by title, category, or creator..."
                    class="w-full bg-white/5 border border-white/10 text-white pl-10 pr-4 py-2.5 rounded-lg focus:outline-none focus:border-indigo-400/50 focus:bg-white/10 transition-all font-sans text-sm placeholder:text-white/30"
                />
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center bg-indigo-500/85 hover:bg-indigo-500 border border-white/10 text-white font-mono text-sm px-5 py-2.5 rounded-lg active:scale-[0.98] transition-all"
                >
                    search
                </button>
                @if ($search !== '')
                    <a
                        href="{{ route('posts.index', $activeCategory ? ['category' => $activeCategory->slug] : []) }}"
                        class="inline-flex items-center justify-center font-mono text-sm text-white/60 hover:text-white border border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10 px-4 py-2.5 rounded-lg transition-all"
                    >
                        clear
                    </a>
                @endif
            </div>
        </form>

        @if ($categories->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('posts.index', $search ? ['q' => $search] : []) }}"
                    @class([
                        'inline-flex font-mono text-xs px-3 py-1.5 rounded-full border transition-colors',
                        'bg-indigo-500/20 text-white border-indigo-400/30' => ! $activeCategory,
                        'bg-white/5 text-white/60 border-white/10 hover:text-white hover:bg-white/10' => $activeCategory,
                    ])
                >
                    all
                </a>
                @foreach ($categories as $category)
                    <a
                        href="{{ route('posts.index', array_filter([
                            'category' => $category->slug,
                            'q' => $search ?: null,
                        ])) }}"
                        @class([
                            'inline-flex font-mono text-xs px-3 py-1.5 rounded-full border transition-colors',
                            'bg-indigo-500/20 text-white border-indigo-400/30' => $activeCategory?->id === $category->id,
                            'bg-white/5 text-white/60 border-white/10 hover:text-white hover:bg-white/10' => $activeCategory?->id !== $category->id,
                        ])
                    >
                        {{ $category->name }}
                        <span class="ml-1.5 text-white/40">{{ $category->posts_count }}</span>
                    </a>
                @endforeach
            </div>
        @endif

        @if ($search !== '')
            <p class="font-mono text-xs text-white/50">
                Showing results for <span class="text-indigo-300">&ldquo;{{ $search }}&rdquo;</span>
                @if ($activeCategory)
                    in <span class="text-indigo-300">{{ $activeCategory->name }}</span>
                @endif
            </p>
        @elseif ($activeCategory)
            <p class="font-mono text-xs text-white/50">
                Showing posts in <span class="text-indigo-300">{{ $activeCategory->name }}</span>
            </p>
        @endif

        @if ($posts->isEmpty())
            <div class="glass-card rounded-2xl p-12 text-center">
                <p class="font-mono text-sm text-white/40">
                    @if ($search !== '')
                        No posts match your search.
                    @elseif ($activeCategory)
                        No posts in this category yet.
                    @else
                        No posts published yet. Check back soon.
                    @endif
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($posts as $post)
                    <article class="glass-card hover:bg-white/15 hover:border-indigo-400/30 rounded-2xl overflow-hidden flex flex-col transition-all duration-300 group hover:-translate-y-1 shadow-xl">
                        @if ($post->image_url)
                            <a href="{{ route('posts.show', $post) }}" class="block aspect-[16/9] overflow-hidden">
                                <img
                                    src="{{ $post->image_url }}"
                                    alt="{{ $post->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                />
                            </a>
                        @endif
                        <div class="bg-white/5 p-3.5 flex justify-between items-center border-b border-white/10 select-none">
                            <div class="flex gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-white/20"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-white/20"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-white/20"></span>
                            </div>
                            <span class="font-mono text-[10px] text-white/40">{{ $post->slug }}.md</span>
                        </div>

                        <div class="p-6 flex flex-col flex-grow space-y-4">
                            <div class="space-y-2 flex-grow">
                                <div class="flex flex-wrap items-center gap-2">
                                    <time class="font-mono text-[10px] text-indigo-400 uppercase tracking-wider">
                                        {{ $post->published_at->format('M j, Y') }}
                                    </time>
                                    @if ($post->isCurrentlyPinned())
                                        <span class="inline-flex items-center gap-1 font-mono text-[10px] px-2 py-0.5 rounded-full border bg-amber-500/15 text-amber-300 border-amber-400/25 uppercase tracking-wider">
                                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                <path d="M12 17v5"/>
                                                <path d="M9 3h6l1 7h4l-7 8-7-8h4z"/>
                                            </svg>
                                            pinned
                                        </span>
                                    @endif
                                </div>
                                @if ($post->categories->isNotEmpty())
                                    <x-post-categories :categories="$post->categories" />
                                @endif
                                <h2 class="font-sans text-lg font-bold text-white group-hover:text-indigo-400 transition-colors">
                                    <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
                                </h2>
                                @if ($post->excerpt)
                                    <p class="font-sans text-sm text-white/70 leading-relaxed line-clamp-3">{{ $post->excerpt }}</p>
                                @endif
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-white/10">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-[10px] text-white/40">by {{ $post->author->name }}</span>
                                    @if ($post->comments_count > 0)
                                        <span class="font-mono text-[10px] text-white/30 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            {{ $post->comments_count }}
                                        </span>
                                    @endif
                                </div>
                                <a
                                    href="{{ route('posts.show', $post) }}"
                                    class="font-mono text-xs text-indigo-300 hover:text-white flex items-center gap-1 transition-colors"
                                >
                                    <span>&gt; read</span>
                                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($posts->hasPages())
                <div class="pt-4">
                    {{ $posts->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>
