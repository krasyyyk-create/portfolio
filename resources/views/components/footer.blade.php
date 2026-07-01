<footer class="w-full mt-auto bg-white/5 border-t border-white/10 backdrop-blur-md">
    <div class="max-w-[1200px] mx-auto px-6 md:px-12 py-12 flex flex-col md:flex-row justify-between items-center gap-6">
        <a
            href="{{ route('home') }}"
            class="font-mono text-xl font-bold text-white hover:text-indigo-400 transition-colors select-none"
        >
            DEV_ARCHITECT
        </a>

        <p class="font-sans text-sm text-white/60 text-center md:text-left">
            &copy; {{ date('Y') }} DEV_ARCHITECT. Built with Systemic Precision.
        </p>

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
