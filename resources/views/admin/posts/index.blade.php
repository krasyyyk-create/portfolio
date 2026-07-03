<x-layouts.admin title="Admin — Posts" header="posts">
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="font-sans text-2xl md:text-3xl font-bold text-white">Posts</h1>
                <p class="font-sans text-sm text-white/50 mt-1">Create and manage blog posts</p>
            </div>
            <a
                href="{{ route('admin.posts.create') }}"
                class="font-mono text-sm text-white bg-indigo-500/80 hover:bg-indigo-500 border border-white/10 px-4 py-2 rounded-lg transition-all shrink-0"
            >
                + new post
            </a>
        </div>

        <div class="glass-card rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Categories</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Author</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Published</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($posts as $post)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-3">
                                    <p class="font-sans text-sm text-white">{{ $post->title }}</p>
                                    <p class="font-mono text-[10px] text-white/40 mt-0.5">{{ $post->slug }}</p>
                                </td>
                                <td class="px-6 py-3">
                                    @if ($post->categories->isEmpty())
                                        <span class="font-mono text-xs text-white/30">—</span>
                                    @else
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach ($post->categories as $category)
                                                <span class="inline-flex font-mono text-[10px] px-2 py-0.5 rounded-full border bg-indigo-500/10 text-indigo-300 border-indigo-400/20">
                                                    {{ $category->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-3 font-mono text-xs text-white/60">{{ $post->author->name }}</td>
                                <td class="px-6 py-3">
                                    <span @class([
                                        'inline-flex font-mono text-[11px] px-2 py-0.5 rounded-full border',
                                        'bg-emerald-500/20 text-emerald-300 border-emerald-400/30' => $post->is_published,
                                        'bg-white/5 text-white/50 border-white/10' => ! $post->is_published,
                                    ])>
                                        {{ $post->is_published ? 'published' : 'draft' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 font-mono text-xs text-white/40">
                                    {{ $post->published_at?->format('M j, Y') ?? '—' }}
                                </td>
                                <td class="px-6 py-3 text-right space-x-2">
                                    @if ($post->is_published)
                                        <a
                                            href="{{ route('posts.show', $post) }}"
                                            target="_blank"
                                            class="inline-block font-mono text-xs text-white/40 hover:text-white transition-colors"
                                        >
                                            view
                                        </a>
                                    @endif
                                    <a
                                        href="{{ route('admin.posts.edit', $post) }}"
                                        class="inline-block font-mono text-xs text-indigo-400 hover:text-indigo-300 transition-colors"
                                    >
                                        edit
                                    </a>
                                    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('Delete this post?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-mono text-xs text-red-400 hover:text-red-300 transition-colors cursor-pointer">
                                            delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center font-mono text-sm text-white/40">No posts yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($posts->hasPages())
                <div class="px-6 py-4 border-t border-white/10">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
