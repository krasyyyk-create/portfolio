<div x-data class="glass-card rounded-xl p-4">
    <div class="flex items-center justify-between gap-3 mb-3">
        <div>
            <h2 class="font-mono text-sm font-bold text-white">Minigames</h2>
            <p class="font-sans text-xs text-white/40 mt-0.5">Quick break between deploys</p>
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        <button
            type="button"
            @click="$dispatch('open-minigame', { game: 'snake' })"
            class="font-mono text-xs px-3 py-2 rounded-lg border border-white/10 bg-white/5 text-white/70 hover:text-white hover:border-indigo-400/30 hover:bg-indigo-500/10 transition-all cursor-pointer flex items-center gap-2"
        >
            <span class="text-base leading-none">🐍</span>
            snake
        </button>

        <button
            type="button"
            @click="$dispatch('open-minigame', { game: 'tic-tac-toe' })"
            class="font-mono text-xs px-3 py-2 rounded-lg border border-white/10 bg-white/5 text-white/70 hover:text-white hover:border-indigo-400/30 hover:bg-indigo-500/10 transition-all cursor-pointer flex items-center gap-2"
        >
            <span class="text-base leading-none">⭕</span>
            tic-tac-toe
        </button>
        <button
            type="button"
            @click="$dispatch('open-minigame', { game: 'pong' })"
            class="font-mono text-xs px-3 py-2 rounded-lg border border-white/10 bg-white/5 text-white/70 hover:text-white hover:border-indigo-400/30 hover:bg-indigo-500/10 transition-all cursor-pointer flex items-center gap-2"
        >
            <span class="text-base leading-none">🏓</span>
            pong
        </button>
        <button
            type="button"
            @click="$dispatch('open-minigame', { game: 'minesweeper' })"
            class="font-mono text-xs px-3 py-2 rounded-lg border border-white/10 bg-white/5 text-white/70 hover:text-white hover:border-indigo-400/30 hover:bg-indigo-500/10 transition-all cursor-pointer flex items-center gap-2"
        >
            <span class="text-base leading-none">💣</span>
            minesweeper
        </button>
    </div>
</div>
