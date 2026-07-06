<footer class="w-full mt-auto bg-white/5 border-t border-white/10 backdrop-blur-md">
    <div class="max-w-[1200px] mx-auto px-6 md:px-12 py-12 flex flex-col md:flex-row justify-between items-center gap-6">
        <a
            href="{{ route('home') }}"
            class="font-mono text-xl font-bold text-white hover:text-indigo-400 transition-colors select-none"
        >
            DEV_ARCHITECT
        </a>

        @if (request()->routeIs('home'))
            <div
                x-data="{
                    clicks: 0,
                    showEasterEgg: false,
                    racesUrl: '{{ route('races.index', ['season' => 2024, 'type' => 'Race']) }}',
                    triggerEasterEgg() {
                        this.showEasterEgg = true;
                        setTimeout(() => { window.location.href = this.racesUrl; }, 3200);
                    },
                }"
                class="contents"
            >
                <p class="font-sans text-sm text-white/60 text-center md:text-left">
                    &copy; {{ date('Y') }} DEV_ARCHITECT. Built with Systemic
                    <button
                        type="button"
                        @click="
                            clicks++;
                            if (clicks >= 5) triggerEasterEgg();
                        "
                        :style="{ fontSize: (0.875 + clicks * 0.25) + 'rem' }"
                        class="inline bg-transparent border-0 p-0 font-inherit text-white/60 hover:text-white/80 transition-all duration-200 cursor-pointer"
                    >Precision</button>.
                </p>

                <div
                    x-show="showEasterEgg"
                    x-cloak
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    class="fixed inset-0 z-[60] flex items-center justify-center p-4"
                >
                    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-md"></div>

                    <div
                        x-show="showEasterEgg"
                        x-transition:enter="transition ease-out duration-400"
                        x-transition:enter-start="opacity-0 scale-90 translate-y-6"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        class="relative w-full max-w-md glass-card-heavy border border-red-500/40 rounded-2xl overflow-hidden shadow-2xl shadow-red-500/20 text-center"
                    >
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-red-600 via-yellow-400 to-red-600 animate-pulse"></div>

                        <div class="px-8 pt-10 pb-8 space-y-6">
                            <div class="relative h-28 flex items-center justify-center overflow-hidden">
                                <div class="absolute inset-x-4 bottom-3 h-px bg-white/10"></div>
                                <div class="absolute inset-x-0 bottom-2 flex justify-between px-6 opacity-20">
                                    <span class="w-2 h-2 rounded-full bg-white"></span>
                                    <span class="w-2 h-2 rounded-full bg-white"></span>
                                    <span class="w-2 h-2 rounded-full bg-white"></span>
                                    <span class="w-2 h-2 rounded-full bg-white"></span>
                                </div>
                                <svg
                                    class="w-44 h-auto drop-shadow-[0_8px_24px_rgba(239,68,68,0.45)] animate-[easter-egg-drive_1.2s_ease-in-out_infinite_alternate]"
                                    viewBox="0 0 200 60"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    aria-hidden="true"
                                >
                                    <ellipse cx="34" cy="46" rx="11" ry="11" fill="#111827" stroke="#374151" stroke-width="2"/>
                                    <ellipse cx="34" cy="46" rx="5" ry="5" fill="#6B7280"/>
                                    <ellipse cx="148" cy="46" rx="11" ry="11" fill="#111827" stroke="#374151" stroke-width="2"/>
                                    <ellipse cx="148" cy="46" rx="5" ry="5" fill="#6B7280"/>
                                    <path d="M18 38 L42 30 L78 24 L132 24 L168 30 L182 36 L176 40 L150 38 L42 40 Z" fill="#DC2626"/>
                                    <path d="M78 24 L92 14 L118 14 L132 24 Z" fill="#B91C1C"/>
                                    <path d="M18 38 L8 40 L6 36 L16 34 Z" fill="#991B1B"/>
                                    <path d="M168 30 L188 28 L192 32 L176 40 Z" fill="#EF4444"/>
                                    <rect x="92" y="18" width="28" height="4" rx="1" fill="#FBBF24"/>
                                    <path d="M6 36 L2 34 L4 30 L10 32 Z" fill="#F3F4F6"/>
                                    <path d="M188 28 L196 26 L198 30 L192 32 Z" fill="#F3F4F6"/>
                                </svg>
                            </div>

                            <div class="space-y-2">
                                <p class="font-mono text-xs uppercase tracking-[0.35em] text-red-400 animate-pulse">Pit Lane Unlocked</p>
                                <h2 class="text-3xl md:text-4xl font-bold uppercase tracking-wide text-white leading-tight">
                                    YOU FOUND AN<br>
                                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 via-yellow-300 to-red-500">EASTER EGG!</span>
                                </h2>
                            </div>

                            <p class="font-mono text-sm text-white/50">Launching race data feed...</p>

                            <button
                                type="button"
                                @click="window.location.href = racesUrl"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-red-600 hover:bg-red-500 text-white font-mono text-sm font-semibold uppercase tracking-wider transition-colors cursor-pointer shadow-lg shadow-red-600/30"
                            >
                                Full Throttle
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <p class="font-sans text-sm text-white/60 text-center md:text-left">
                &copy; {{ date('Y') }} DEV_ARCHITECT. Built with Systemic Precision.
            </p>
        @endif

        <div class="flex flex-wrap justify-center items-center gap-6">
            <a href="https://github.com" target="_blank" rel="noreferrer" class="font-mono text-xs text-white/60 hover:text-white transition-colors">GitHub</a>
            <a href="https://linkedin.com" target="_blank" rel="noreferrer" class="font-mono text-xs text-white/60 hover:text-white transition-colors">LinkedIn</a>
            <a href="https://twitter.com" target="_blank" rel="noreferrer" class="font-mono text-xs text-white/60 hover:text-white transition-colors">Twitter</a>
            <button
                type="button"
                onclick="window.scrollTo({ top: 0, behavior: 'smooth' })"
                class="font-mono text-xs text-white/60 hover:text-indigo-400 transition-colors flex items-center gap-1 cursor-pointer"
            >
                <span>Back to Top</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
            </button>
        </div>
    </div>
</footer>
