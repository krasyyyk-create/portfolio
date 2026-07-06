@props(['post'])

<section id="comments" class="space-y-6 pt-8 border-t border-white/10">
    <div class="flex items-center justify-between gap-4">
        <h2 class="font-sans text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <span>Comments</span>
            <span class="font-mono text-sm font-normal text-white/40">({{ $post->comments->count() }})</span>
        </h2>
    </div>

    @if (session('success'))
        <div class="glass-card border border-green-500/30 bg-green-500/10 rounded-xl px-4 py-3">
            <p class="font-mono text-sm text-green-300">&gt; {{ session('success') }}</p>
        </div>
    @endif

    @auth
        <form
            action="{{ route('posts.comments.store', $post) }}"
            method="POST"
            class="glass-card border border-white/15 rounded-xl p-5 space-y-4"
        >
            @csrf
            <div class="space-y-2">
                <label for="comment-body" class="font-mono text-xs text-white/50 uppercase tracking-wider">
                    Add a comment
                </label>
                <textarea
                    id="comment-body"
                    name="body"
                    rows="3"
                    required
                    maxlength="2000"
                    placeholder="Share your thoughts..."
                    class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400/50 focus:bg-white/10 transition-all font-sans text-sm resize-y min-h-[96px] placeholder:text-white/30 @error('body') border-red-500/60 @enderror"
                >{{ old('body') }}</textarea>
                @error('body')
                    <p class="font-mono text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center bg-indigo-500/85 hover:bg-indigo-500 border border-white/10 text-white font-sans text-sm font-semibold px-5 py-2 rounded-lg active:scale-[0.98] transition-all"
                >
                    Post comment
                </button>
            </div>
        </form>
    @else
        <div class="glass-card border border-white/10 rounded-xl px-5 py-4">
            <p class="font-sans text-sm text-white/60">
                <a href="{{ route('login') }}" class="text-indigo-300 hover:text-white transition-colors">Sign in</a>
                to join the discussion.
            </p>
        </div>
    @endauth

    @if ($post->comments->isEmpty())
        <p class="font-mono text-sm text-white/40">No comments yet. Be the first to respond.</p>
    @else
        <ul class="space-y-4">
            @foreach ($post->comments as $comment)
                <li class="glass-card border border-white/10 rounded-xl p-5">
                    <div class="flex items-start gap-4">
                        <div class="shrink-0">
                            @if ($comment->author->avatar_url)
                                <img
                                    src="{{ $comment->author->avatar_url }}"
                                    alt="{{ $comment->author->name }}"
                                    class="w-10 h-10 rounded-full object-cover border border-white/10"
                                />
                            @else
                                <div class="w-10 h-10 rounded-full bg-indigo-500/30 border border-indigo-400/30 flex items-center justify-center font-sans text-sm font-bold text-indigo-300">
                                    {{ strtoupper(substr($comment->author->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <div class="flex-grow min-w-0 space-y-2">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                    <span class="font-sans text-sm font-semibold text-white">{{ $comment->author->name }}</span>
                                    <time class="font-mono text-[10px] text-white/40" datetime="{{ $comment->created_at->toIso8601String() }}">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </time>
                                </div>

                                @can('delete', $comment)
                                    <form
                                        action="{{ route('posts.comments.destroy', [$post, $comment]) }}"
                                        method="POST"
                                        onsubmit="return confirm('Delete this comment?')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="font-mono text-[10px] text-white/30 hover:text-red-400 transition-colors"
                                        >
                                            delete
                                        </button>
                                    </form>
                                @endcan
                            </div>

                            <p class="font-sans text-sm text-white/80 leading-relaxed whitespace-pre-wrap">{{ $comment->body }}</p>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</section>
