<x-layouts.admin title="Admin — Categories" header="categories">
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="font-sans text-2xl md:text-3xl font-bold text-white">Categories</h1>
                <p class="font-sans text-sm text-white/50 mt-1">Organize posts into topics</p>
            </div>
            <a
                href="{{ route('admin.categories.create') }}"
                class="font-mono text-sm text-white bg-indigo-500/80 hover:bg-indigo-500 border border-white/10 px-4 py-2 rounded-lg transition-all shrink-0"
            >
                + new category
            </a>
        </div>

        <div class="glass-card rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Posts</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-3">
                                    <p class="font-sans text-sm text-white">{{ $category->name }}</p>
                                    <p class="font-mono text-[10px] text-white/40 mt-0.5">{{ $category->slug }}</p>
                                </td>
                                <td class="px-6 py-3 font-mono text-xs text-white/60">{{ $category->posts_count }}</td>
                                <td class="px-6 py-3 text-right space-x-2">
                                    <a
                                        href="{{ route('admin.categories.edit', $category) }}"
                                        class="inline-block font-mono text-xs text-indigo-400 hover:text-indigo-300 transition-colors"
                                    >
                                        edit
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?')">
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
                                <td colspan="3" class="px-6 py-8 text-center font-mono text-sm text-white/40">No categories yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="px-6 py-4 border-t border-white/10">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
