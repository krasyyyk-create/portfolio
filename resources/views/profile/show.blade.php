<x-layouts.app :title="'DEV_ARCHITECT — ' . $user->name">
    <div class="max-w-4xl mx-auto space-y-10">
        <x-profile.header :user="$user" :is-owner="$isOwner" />

        <section class="space-y-6">
            <h2 class="font-mono text-sm font-bold text-white flex items-center gap-2">
                <span class="text-indigo-400">&gt;</span> RECENTLY LIKED
            </h2>

            @if ($recentLikes->isEmpty())
                <div class="glass-card rounded-2xl p-10 text-center">
                    <p class="font-mono text-sm text-white/40">
                        @if ($isOwner)
                            No liked posts yet. Browse the <a href="{{ route('posts.index') }}" class="text-indigo-300 hover:text-white transition-colors">engineering log</a> and like some posts.
                        @else
                            {{ $user->name }} hasn't liked any posts yet.
                        @endif
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($recentLikes as $post)
                        <article class="glass-card hover:bg-white/15 hover:border-indigo-400/30 rounded-2xl overflow-hidden flex flex-col transition-all duration-300 group hover:-translate-y-1 shadow-xl relative cursor-pointer">
                            @if ($post->image_url)
                                <div class="aspect-[16/9] overflow-hidden">
                                    <img
                                        src="{{ $post->image_url }}"
                                        alt="{{ $post->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    />
                                </div>
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
                                    <time class="font-mono text-[10px] text-indigo-400 uppercase tracking-wider">
                                        {{ $post->published_at->format('M j, Y') }}
                                    </time>
                                    @if ($post->categories->isNotEmpty())
                                        <x-post-categories :categories="$post->categories" />
                                    @endif
                                    <h3 class="font-sans text-lg font-bold text-white group-hover:text-indigo-400 transition-colors">
                                        {{ $post->title }}
                                    </h3>
                                    @if ($post->excerpt)
                                        <p class="font-sans text-sm text-white/70 leading-relaxed line-clamp-2">{{ $post->excerpt }}</p>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between pt-4 border-t border-white/10">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('users.show', $post->author) }}" class="relative z-10 font-mono text-[10px] text-white/40 hover:text-indigo-300 transition-colors">
                                            by {{ $post->author->name }}
                                        </a>
                                        <span class="font-mono text-xs text-rose-300/80 flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                            </svg>
                                            {{ $post->likes_count }}
                                        </span>
                                    </div>
                                    <span class="font-mono text-xs text-indigo-300 group-hover:text-white flex items-center gap-1 transition-colors pointer-events-none">
                                        <span>&gt; read</span>
                                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <a
                                href="{{ route('posts.show', $post) }}"
                                class="absolute inset-0 z-[1] rounded-2xl"
                                aria-label="Read {{ $post->title }}"
                            >
                                <span class="sr-only">Read {{ $post->title }}</span>
                            </a>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
