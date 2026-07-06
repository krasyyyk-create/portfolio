@props(['post' => null])

@php
    $defaultPublished = old('is_published', $post?->is_published ?? true);
    $defaultDate = old(
        'published_at',
        $post?->published_at?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')
    );
@endphp

<div
    x-data="{ published: @json((bool) $defaultPublished) }"
    class="space-y-4"
>
    <label class="flex items-center gap-3 cursor-pointer">
        <input
            type="checkbox"
            name="is_published"
            value="1"
            x-model="published"
            @checked($defaultPublished)
            class="w-4 h-4 rounded border-white/20 bg-slate-950/20 text-indigo-500 focus:ring-indigo-400/50"
        />
        <span class="font-sans text-sm text-white/70">
            {{ $post ? 'Published' : 'Publish immediately' }}
        </span>
    </label>

    <div x-show="published" x-cloak class="space-y-2">
        <label class="font-sans font-medium text-xs text-white/60" for="published_at">PUBLISHED DATE</label>
        <input
            type="datetime-local"
            class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-mono text-sm @error('published_at') border-red-500/60 @enderror"
            id="published_at"
            name="published_at"
            value="{{ $defaultDate }}"
        />
        @error('published_at')
            <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
        @enderror
    </div>
</div>
