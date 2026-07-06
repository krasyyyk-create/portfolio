<x-layouts.app :title="'DEV_ARCHITECT — ' . $post->title">
    <article class="max-w-3xl mx-auto space-y-8">
        <div class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <a href="{{ route('posts.index') }}" class="font-mono text-xs text-white/40 hover:text-white transition-colors inline-flex items-center gap-1">
                    <span>&larr;</span> back to posts
                </a>
                @can('update', $post)
                    <a
                        href="{{ route('posts.edit', $post) }}"
                        class="font-mono text-xs text-indigo-300 hover:text-white border border-indigo-400/30 hover:border-indigo-400/50 bg-indigo-500/10 hover:bg-indigo-500/20 px-3 py-1.5 rounded-lg transition-all"
                    >
                        edit post
                    </a>
                @endcan
            </div>

            <div class="space-y-3">
                <time class="font-mono text-xs text-indigo-400 uppercase tracking-wider">
                    {{ $post->published_at->format('F j, Y') }}
                </time>
                <h1 class="font-sans text-3xl md:text-4xl font-bold text-white leading-tight">{{ $post->title }}</h1>
                @if ($post->categories->isNotEmpty())
                    <x-post-categories :categories="$post->categories" />
                @endif
                <p class="font-mono text-sm text-white/50">by {{ $post->author->name }}</p>
            </div>
        </div>

        @if ($post->excerpt)
            <p class="font-sans text-lg text-white/70 leading-relaxed border-l-2 border-indigo-400/50 pl-4">
                {{ $post->excerpt }}
            </p>
        @endif

        @if ($post->image_url)
            <div class="rounded-2xl overflow-hidden border border-white/15">
                <img
                    src="{{ $post->image_url }}"
                    alt="{{ $post->title }}"
                    class="w-full max-h-[28rem] object-cover"
                />
            </div>
        @endif

        @if ($post->hasBodyContent())
            <div class="glass-card-heavy border border-white/15 rounded-2xl overflow-hidden">
                <div class="bg-white/5 p-3.5 flex justify-between items-center border-b border-white/10 select-none">
                    <div class="flex gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500/40"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-yellow-500/40"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500/40"></span>
                    </div>
                    <span class="font-mono text-[10px] text-white/40">{{ $post->slug }}.md</span>
                </div>

                <div class="p-6 md:p-8 prose prose-invert prose-sm max-w-none font-sans text-white/80 leading-relaxed space-y-4">
                    {!! $post->content !!}
                </div>
            </div>
        @endif

        <x-posts.comments :post="$post" />
    </article>
</x-layouts.app>
