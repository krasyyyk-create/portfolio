@props([
    'name' => 'content',
    'id' => null,
    'value' => '',
    'label' => null,
    'hint' => null,
])

@php
    $fieldId = $id ?? $name;
    $resolvedValue = old($name, $value);
    $hasError = $errors->has($name);
@endphp

<div class="space-y-2" x-data="wysiwygEditor(@js($resolvedValue))">
    @if ($label)
        <label class="font-sans font-medium text-xs text-white/60" for="{{ $fieldId }}">
            {!! $label !!}
        </label>
    @endif

    <div @class([
        'rounded-lg border overflow-hidden bg-slate-950/20 focus-within:border-indigo-400 focus-within:bg-slate-950/30 transition-all',
        'border-white/10' => ! $hasError,
        'border-red-500/60' => $hasError,
    ])>
        <div class="flex flex-wrap items-center gap-0.5 p-2 border-b border-white/10 bg-white/5">
            <div class="flex flex-wrap items-center gap-0.5" x-show="mode === 'visual'">
            <button type="button" @mousedown.prevent @click="exec('bold')" class="wysiwyg-btn" title="Bold">
                <span class="font-bold">B</span>
            </button>
            <button type="button" @mousedown.prevent @click="exec('italic')" class="wysiwyg-btn" title="Italic">
                <span class="italic">I</span>
            </button>
            <button type="button" @mousedown.prevent @click="exec('underline')" class="wysiwyg-btn" title="Underline">
                <span class="underline">U</span>
            </button>

            <span class="w-px h-5 bg-white/10 mx-1"></span>

            <button type="button" @mousedown.prevent @click="formatBlock('h2')" class="wysiwyg-btn font-mono text-[11px]" title="Heading 2">H2</button>
            <button type="button" @mousedown.prevent @click="formatBlock('h3')" class="wysiwyg-btn font-mono text-[11px]" title="Heading 3">H3</button>
            <button type="button" @mousedown.prevent @click="formatBlock('p')" class="wysiwyg-btn font-mono text-[11px]" title="Paragraph">P</button>

            <span class="w-px h-5 bg-white/10 mx-1"></span>

            <button type="button" @mousedown.prevent @click="exec('insertUnorderedList')" class="wysiwyg-btn" title="Bullet list">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
            </button>
            <button type="button" @mousedown.prevent @click="exec('insertOrderedList')" class="wysiwyg-btn" title="Numbered list">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 6h13M7 12h13M7 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
            </button>
            <button type="button" @mousedown.prevent @click="exec('formatBlock', 'blockquote')" class="wysiwyg-btn font-mono text-[11px]" title="Quote">&ldquo;</button>

            <span class="w-px h-5 bg-white/10 mx-1"></span>

            <button type="button" @mousedown.prevent @click="insertLink()" class="wysiwyg-btn font-mono text-[11px]" title="Link">link</button>
            <button type="button" @mousedown.prevent @click="exec('removeFormat')" class="wysiwyg-btn font-mono text-[11px]" title="Clear formatting">clear</button>
            </div>

            <button
                type="button"
                @click="toggleMode()"
                class="wysiwyg-btn ml-auto font-mono text-[11px]"
                :class="mode === 'html' && 'wysiwyg-btn-active'"
                :title="mode === 'visual' ? 'Switch to HTML source' : 'Switch to visual editor'"
                x-text="mode === 'visual' ? 'HTML' : 'Visual'"
            ></button>
        </div>

        <div
            x-show="mode === 'visual'"
            x-ref="editor"
            contenteditable="true"
            @input="sync()"
            class="wysiwyg-content min-h-[280px] max-h-[480px] overflow-y-auto px-4 py-3 font-sans text-sm text-white/90 leading-relaxed outline-none"
            data-placeholder="Start writing..."
        ></div>

        <textarea
            x-show="mode === 'html'"
            x-ref="htmlEditor"
            x-model="htmlSource"
            @input="sync()"
            spellcheck="false"
            class="wysiwyg-html-source w-full min-h-[280px] max-h-[480px] overflow-y-auto px-4 py-3 font-mono text-xs text-white/90 leading-relaxed outline-none resize-y bg-transparent border-0"
            placeholder="<p>Edit raw HTML here...</p>"
        ></textarea>

        <textarea
            x-ref="textarea"
            id="{{ $fieldId }}"
            name="{{ $name }}"
            class="hidden"
            rows="14"
        ></textarea>
    </div>

    @if ($hint)
        <p class="font-mono text-[10px] text-white/30">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
    @enderror
</div>
