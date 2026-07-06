@props([
    'action',
    'label' => 'Report content',
    'size' => 'sm',
])

@php
    $buttonSize = $size === 'md'
        ? 'w-16 h-16 rounded-2xl'
        : 'w-12 h-12 rounded-xl';

    $iconSize = $size === 'md'
        ? 'w-7 h-7'
        : 'w-6 h-6';
@endphp

@auth
    <div x-data="{ open: false }" class="inline-flex shrink-0">
        <button
            type="button"
            @click="open = true"
            title="{{ $label }}"
            aria-label="{{ $label }}"
            @class([
                'group relative inline-flex items-center justify-center border-2 transition-all active:scale-95',
                'bg-transparent border-white/70 text-white/80',
                'hover:bg-white hover:border-white hover:text-slate-950',
                'focus:outline-none focus-visible:ring-2 focus-visible:ring-white/50 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950',
                $buttonSize,
            ])
        >
            <svg
                @class([$iconSize, 'transition-transform group-hover:scale-105'])
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.75"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="M6 3v18"/>
                <path d="M6 5.5c2.8-.8 4.6.6 7.2.2 1.8-.3 3.4-1 5.8-.7v7.5c-2.4-.3-4 .4-5.8.7-2.6.4-4.4-1-7.2-.2"/>
            </svg>

            <span class="sr-only">{{ $label }}</span>
        </button>

        <div
            x-show="open"
            x-cloak
            class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="report-dialog-title-{{ md5($action) }}"
        >
            <div
                class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"
                @click="open = false"
            ></div>

            <div
                x-show="open"
                x-transition
                class="relative w-full max-w-md glass-card border border-white/15 rounded-2xl p-6 space-y-4 shadow-2xl shadow-black/40"
            >
                <div class="space-y-1">
                    <h3 id="report-dialog-title-{{ md5($action) }}" class="font-sans text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-white shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 3v18"/>
                            <path d="M6 5.5c2.8-.8 4.6.6 7.2.2 1.8-.3 3.4-1 5.8-.7v7.5c-2.4-.3-4 .4-5.8.7-2.6.4-4.4-1-7.2-.2"/>
                        </svg>
                        Report content
                    </h3>
                    <p class="font-sans text-sm text-white/60">
                        Tell us why this content should be reviewed. Reports are visible to admins only.
                    </p>
                </div>

                <form action="{{ $action }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label for="report-reason-{{ md5($action) }}" class="font-mono text-xs text-white/50 uppercase tracking-wider">
                            Reason
                        </label>
                        <textarea
                            id="report-reason-{{ md5($action) }}"
                            name="reason"
                            rows="4"
                            required
                            minlength="10"
                            maxlength="1000"
                            placeholder="Describe the issue (min. 10 characters)..."
                            class="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 font-sans text-sm text-white placeholder:text-white/30 focus:outline-none focus:border-indigo-400/50 @error('reason') border-red-500/60 @enderror"
                        >{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="font-mono text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            @click="open = false"
                            class="font-mono text-xs text-white/50 hover:text-white px-4 py-2 rounded-lg border border-white/10 hover:border-white/20 transition-all"
                        >
                            cancel
                        </button>
                        <button
                            type="submit"
                            class="font-mono text-xs text-white bg-amber-500/80 hover:bg-amber-500 border border-white/10 px-4 py-2 rounded-lg transition-all"
                        >
                            submit report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endauth
