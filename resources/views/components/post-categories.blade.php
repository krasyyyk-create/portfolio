@props(['categories'])

@if ($categories->isNotEmpty())
    <div class="flex flex-wrap gap-2">
        @foreach ($categories as $category)
            <a
                href="{{ route('posts.index', ['category' => $category->slug]) }}"
                class="relative z-10 inline-flex font-mono text-[10px] px-2.5 py-1 rounded-full border bg-indigo-500/10 text-indigo-300 border-indigo-400/20 hover:bg-indigo-500/20 hover:text-white transition-colors"
            >
                {{ $category->name }}
            </a>
        @endforeach
    </div>
@endif
