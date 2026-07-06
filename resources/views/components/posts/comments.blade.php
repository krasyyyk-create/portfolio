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
            enctype="multipart/form-data"
            x-data="{ previewUrl: null }"
            class="glass-card border border-white/15 rounded-xl p-5 space-y-4"
        >
            @csrf
            <x-wysiwyg-editor
                name="body"
                id="comment-body"
                :value="old('parent_id') ? '' : old('body', '')"
                label='<span class="font-mono uppercase tracking-wider text-white/50">Add a comment</span>'
                hint="Use the toolbar to format your comment."
                min-height="96px"
                max-height="240px"
                placeholder="Share your thoughts..."
                :maxlength="2000"
            />

            <div class="space-y-2">
                <label for="comment-image" class="font-mono text-xs text-white/50 uppercase tracking-wider">
                    Attach image or GIF
                </label>
                <input
                    id="comment-image"
                    type="file"
                    name="image"
                    accept="image/jpeg,image/png,image/webp,image/gif"
                    class="block w-full text-sm text-white/70 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border file:border-white/10 file:text-sm file:font-mono file:bg-white/5 file:text-white/80 hover:file:bg-white/10 file:cursor-pointer cursor-pointer @error('image') border border-red-500/60 rounded-lg @enderror"
                    @change="
                        previewUrl = $event.target.files[0]
                            ? URL.createObjectURL($event.target.files[0])
                            : null
                    "
                />
                <p class="font-mono text-[10px] text-white/30">JPEG, PNG, WebP, or GIF — max 4 MB. Large images are cropped to 480×320.</p>
                @error('image')
                    <p class="font-mono text-xs text-red-400">{{ $message }}</p>
                @enderror

                <div x-show="previewUrl" x-cloak class="pt-1">
                    <p class="font-mono text-[10px] text-white/40 mb-2">Preview</p>
                    <img
                        :src="previewUrl"
                        alt="Attachment preview"
                        class="rounded-lg border border-white/10 object-cover max-w-[480px] max-h-[320px] w-auto h-auto"
                    />
                </div>
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

    @if ($post->topLevelComments->isEmpty())
        <p class="font-mono text-sm text-white/40">No comments yet. Be the first to respond.</p>
    @else
        <ul class="space-y-4">
            @foreach ($post->topLevelComments as $comment)
                <x-posts.comment :comment="$comment" :post="$post" />
            @endforeach
        </ul>
    @endif
</section>
