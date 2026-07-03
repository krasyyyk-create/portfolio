@props(['categories', 'selectedIds' => []])

@php
    $selectedIds = collect(old('category_ids', $selectedIds))->map(fn ($id) => (int) $id);
@endphp

<div class="space-y-2">
    <label class="font-sans font-medium text-xs text-white/60">CATEGORIES</label>

    @if ($categories->isEmpty())
        <p class="font-mono text-xs text-white/40">
            No categories available yet.
            @if (auth()->user()?->isAdmin())
                <a href="{{ route('admin.categories.create') }}" class="text-indigo-400 hover:text-indigo-300">Create one</a>
            @endif
        </p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach ($categories as $category)
                <label class="flex items-center gap-3 cursor-pointer rounded-lg border border-white/10 bg-slate-950/20 px-4 py-3 hover:border-indigo-400/30 transition-colors">
                    <input
                        type="checkbox"
                        name="category_ids[]"
                        value="{{ $category->id }}"
                        @checked($selectedIds->contains($category->id))
                        class="w-4 h-4 rounded border-white/20 bg-slate-950/20 text-indigo-500 focus:ring-indigo-400/50"
                    />
                    <span class="font-sans text-sm text-white/80">{{ $category->name }}</span>
                </label>
            @endforeach
        </div>
    @endif

    @error('category_ids')
        <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
    @enderror
    @error('category_ids.*')
        <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
    @enderror
</div>
