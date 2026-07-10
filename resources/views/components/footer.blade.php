<footer class="w-full mt-auto bg-white/5 border-t border-white/10 backdrop-blur-md">
    <div class="max-w-[1200px] mx-auto px-6 md:px-12 py-12 flex flex-col md:flex-row justify-between items-center gap-6">
        <a
            href="{{ route('home') }}"
            class="font-mono text-xl font-bold text-white hover:text-indigo-400 transition-colors select-none"
        >
            DEV_ARCHITECT
        </a>

        <div
            x-data="{
                clicks: 0,
                showEasterEgg: false,
                carPhase: 'idle',
                carLaunchStyle: '',
                racesUrl: '{{ route('races.index', ['season' => 2024, 'type' => 'Race']) }}',
                triggerEasterEgg() {
                    this.showEasterEgg = true;
                    this.carPhase = 'vibrate';
                    setTimeout(() => {
                        const rect = this.$refs.carWrap.getBoundingClientRect();
                        this.carLaunchStyle = `top: ${rect.top}px; width: ${rect.width}px;`;
                        this.carPhase = 'launch';
                    }, 1000);
                    setTimeout(() => {
                        window.location.href = this.racesUrl;
                    }, 3000);
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

            <template x-teleport="body">
                <div
                    x-show="showEasterEgg"
                    x-cloak
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    class="fixed inset-0 z-[60] flex items-center justify-center p-4"
                    :class="carPhase === 'launch' ? 'overflow-visible' : 'overflow-hidden'"
                >
                    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-md"></div>

                    <div
                        x-show="showEasterEgg"
                        x-transition:enter="transition ease-out duration-400"
                        x-transition:enter-start="opacity-0 scale-90 translate-y-6"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        class="relative z-10 w-full max-w-md glass-card-heavy border border-red-500/40 rounded-2xl shadow-2xl shadow-red-500/20 text-center"
                        :class="carPhase === 'launch' ? 'overflow-visible' : 'overflow-hidden'"
                    >
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-red-600 via-yellow-400 to-red-600 animate-pulse"></div>

                        <div class="px-8 pt-10 pb-8 space-y-6">
                            <div class="relative h-32 flex items-end justify-center">
                                <div class="absolute inset-x-6 bottom-5 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                                <div class="absolute inset-x-0 bottom-4 flex justify-between px-10 opacity-25">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                </div>
                                <div
                                    x-ref="carWrap"
                                    class="easter-egg-car-wrap"
                                    :class="{
                                        'easter-egg-car-wrap--vibrate': carPhase === 'vibrate',
                                        'easter-egg-car-wrap--launch': carPhase === 'launch',
                                    }"
                                    :style="carLaunchStyle"
                                >
                                    <img
                                        src="{{ asset('images/easter-egg-f1-car.png') }}"
                                        alt=""
                                        class="easter-egg-car-img block w-full h-auto"
                                        aria-hidden="true"
                                    >
                                </div>
                            </div>

                            <div>
                                <h2 class="text-3xl md:text-4xl font-bold tracking-wide text-white leading-tight">
                                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 via-yellow-300 to-red-500">Off track limits?</span>
                                </h2>
                            </div>

                            <p class="font-mono text-sm text-white/50">Launching race data feed...</p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

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
