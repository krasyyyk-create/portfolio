@props(['post' => null])

<div class="space-y-2">
    <label class="font-sans font-medium text-xs text-white/60" for="image">FEATURED IMAGE</label>

    @if ($post?->image_url)
        <div class="rounded-lg overflow-hidden border border-white/10">
            <img
                src="{{ $post->image_url }}"
                alt="{{ $post->title }}"
                class="w-full max-h-48 object-cover"
            />
        </div>
        <label class="flex items-center gap-3 cursor-pointer">
            <input
                type="checkbox"
                name="remove_image"
                value="1"
                @checked(old('remove_image'))
                class="w-4 h-4 rounded border-white/20 bg-slate-950/20 text-indigo-500 focus:ring-indigo-400/50"
            />
            <span class="font-sans text-sm text-white/70">Remove current image</span>
        </label>
    @endif

    <input
        type="file"
        id="image"
        name="image"
        accept="image/jpeg,image/png,image/webp,image/gif"
        class="w-full text-sm text-white/70 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-500/85 file:text-white file:font-sans file:font-semibold file:cursor-pointer hover:file:bg-indigo-500 @error('image') border border-red-500/60 rounded-lg @enderror"
    />
    <p class="font-mono text-[10px] text-white/30">JPEG, PNG, WebP, or GIF — max 2 MB</p>
    @error('image')
        <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
    @enderror
</div>
