<x-layouts.admin title="Admin — New Post" header="posts/create">
    <div class="max-w-3xl space-y-6">
        <div>
            <a href="{{ route('admin.posts.index') }}" class="font-mono text-xs text-white/40 hover:text-white transition-colors">
                &larr; back to posts
            </a>
            <h1 class="font-sans text-2xl md:text-3xl font-bold text-white mt-2">New Post</h1>
            <p class="font-sans text-sm text-white/50 mt-1">Write and publish a new blog post</p>
        </div>

        <div class="glass-card-heavy border border-white/15 rounded-xl p-6 md:p-8">
            <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label class="font-sans font-medium text-xs text-white/60" for="title">TITLE</label>
                    <input
                        class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans @error('title') border-red-500/60 @enderror"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        required
                    />
                    @error('title')
                        <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="font-sans font-medium text-xs text-white/60" for="slug">SLUG <span class="text-white/30">(optional)</span></label>
                    <input
                        class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-mono text-sm @error('slug') border-red-500/60 @enderror"
                        id="slug"
                        name="slug"
                        value="{{ old('slug') }}"
                        placeholder="auto-generated-from-title"
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
                    >{{ old('excerpt') }}</textarea>
                    @error('excerpt')
                        <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                    @enderror
                </div>

                <x-admin.post-image-upload />

                <x-wysiwyg-editor
                    name="content"
                    label='CONTENT <span class="text-white/30">(optional with a featured image)</span>'
                    hint="Use the toolbar to format text. Leave blank for image-only posts."
                />

                <x-admin.post-category-picker :categories="$categories" />

                <x-admin.post-published-fields />

                <button
                    type="submit"
                    class="w-full bg-indigo-500/85 hover:bg-indigo-500 border border-white/10 text-white font-sans font-semibold py-3 rounded-lg active:scale-[0.98] transition-all cursor-pointer shadow-lg shadow-indigo-500/25"
                >
                    Create Post
                </button>
            </form>
        </div>
    </div>
</x-layouts.admin>
