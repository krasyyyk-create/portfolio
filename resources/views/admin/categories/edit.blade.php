<x-layouts.admin title="Admin — Edit Category" header="categories/edit">
    <div class="max-w-3xl space-y-6">
        <div>
            <a href="{{ route('admin.categories.index') }}" class="font-mono text-xs text-white/40 hover:text-white transition-colors">
                &larr; back to categories
            </a>
            <h1 class="font-sans text-2xl md:text-3xl font-bold text-white mt-2">Edit Category</h1>
            <p class="font-sans text-sm text-white/50 mt-1">{{ $category->slug }}</p>
        </div>

        <div class="glass-card-heavy border border-white/15 rounded-xl p-6 md:p-8">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="space-y-2">
                    <label class="font-sans font-medium text-xs text-white/60" for="name">NAME</label>
                    <input
                        class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans @error('name') border-red-500/60 @enderror"
                        id="name"
                        name="name"
                        value="{{ old('name', $category->name) }}"
                        required
                    />
                    @error('name')
                        <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="font-sans font-medium text-xs text-white/60" for="slug">SLUG</label>
                    <input
                        class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-mono text-sm @error('slug') border-red-500/60 @enderror"
                        id="slug"
                        name="slug"
                        value="{{ old('slug', $category->slug) }}"
                    />
                    @error('slug')
                        <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                    @enderror
                </div>

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
