<x-layouts.admin title="Admin — Edit Post" header="posts/edit">
    <div class="max-w-3xl space-y-6">
        <div>
            <a href="{{ route('admin.posts.index') }}" class="font-mono text-xs text-white/40 hover:text-white transition-colors">
                &larr; back to posts
            </a>
            <h1 class="font-sans text-2xl md:text-3xl font-bold text-white mt-2">Edit Post</h1>
            <p class="font-sans text-sm text-white/50 mt-1">{{ $post->slug }}</p>
        </div>

        <div class="glass-card-heavy border border-white/15 rounded-xl p-6 md:p-8">
            <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="space-y-2">
                    <label class="font-sans font-medium text-xs text-white/60" for="title">TITLE</label>
                    <input
                        class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans @error('title') border-red-500/60 @enderror"
                        id="title"
                        name="title"
                        value="{{ old('title', $post->title) }}"
                        required
                    />
                    @error('title')
                        <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="font-sans font-medium text-xs text-white/60" for="slug">SLUG</label>
                    <input
                        class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-mono text-sm @error('slug') border-red-500/60 @enderror"
                        id="slug"
                        name="slug"
                        value="{{ old('slug', $post->slug) }}"
                    />
                    @error('slug')
                        <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="font-sans font-medium text-xs text-white/60" for="excerpt">EXCERPT</label>
                    <textarea
                        class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans resize-y min-h-[80px] @error('excerpt') border-red-500/60 @enderror"
                        id="excerpt"
                        name="excerpt"
                        rows="3"
                    >{{ old('excerpt', $post->excerpt) }}</textarea>
                    @error('excerpt')
                        <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                    @enderror
                </div>

                <x-admin.post-image-upload :post="$post" />

                <div class="space-y-2">
                    <label class="font-sans font-medium text-xs text-white/60" for="content">
                        CONTENT <span class="text-white/30">(optional with a featured image)</span>
                    </label>
                    <textarea
                        class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans resize-y min-h-[280px] @error('content') border-red-500/60 @enderror"
                        id="content"
                        name="content"
                        rows="14"
                    >{{ old('content', $post->content) }}</textarea>
                    <p class="font-mono text-[10px] text-white/30">HTML is supported (e.g. &lt;p&gt;, &lt;strong&gt;, &lt;code&gt;). Leave blank for image-only posts.</p>
                    @error('content')
                        <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                    @enderror
                </div>

                <x-admin.post-category-picker :categories="$categories" :selected-ids="$post->categories->pluck('id')" />

                <label class="flex items-center gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        name="is_published"
                        value="1"
                        @checked(old('is_published', $post->is_published))
                        class="w-4 h-4 rounded border-white/20 bg-slate-950/20 text-indigo-500 focus:ring-indigo-400/50"
                    />
                    <span class="font-sans text-sm text-white/70">Published</span>
                </label>

                <button
                    type="submit"
                    class="w-full bg-indigo-500/85 hover:bg-indigo-500 border border-white/10 text-white font-sans font-semibold py-3 rounded-lg active:scale-[0.98] transition-all cursor-pointer shadow-lg shadow-indigo-500/25"
                >
                    Save Changes
                </button>
            </form>
        </div>
    </div>
</x-layouts.admin>
