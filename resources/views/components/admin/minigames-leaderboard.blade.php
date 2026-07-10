<div
    x-data="minigamesLeaderboard({
        indexUrl: @js(route('admin.leaderboard.index')),
    })"
    x-init="init()"
    class="glass-card rounded-xl overflow-hidden"
>
    <div class="px-6 py-4 border-b border-white/10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-mono text-sm font-bold text-white">Minigame Leaderboard</h2>
            <p class="font-sans text-xs text-white/40 mt-0.5">One entry per admin — best score or total wins</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <template x-for="game in games" :key="game.id">
                <button
                    type="button"
                    @click="selectGame(game.id)"
                    :class="selectedGame === game.id
                        ? 'bg-indigo-500/20 text-indigo-300 border-indigo-400/30'
                        : 'bg-white/5 text-white/50 border-white/10 hover:text-white hover:border-white/20'"
                    class="font-mono text-xs px-3 py-1.5 rounded-lg border transition-all cursor-pointer flex items-center gap-1.5"
                >
                    <span x-text="game.icon"></span>
                    <span x-text="game.label"></span>
                </button>
            </template>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-white/10">
                    <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider w-16">Rank</th>
                    <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Player</th>
                    <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider text-right" x-text="scoreLabel"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <template x-if="loading">
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center font-mono text-sm text-white/40">Loading leaderboard...</td>
                    </tr>
                </template>

                <template x-if="!loading && entries.length === 0">
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center font-mono text-sm text-white/40">No scores yet — be the first!</td>
                    </tr>
                </template>

                <template x-for="entry in entries" :key="entry.user_id">
                    <tr
                        class="transition-colors"
                        :class="entry.is_me ? 'bg-indigo-500/10 hover:bg-indigo-500/15' : 'hover:bg-white/5'"
                    >
                        <td class="px-6 py-3 font-mono text-sm text-white/60" x-text="entry.rank"></td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <template x-if="entry.avatar_url">
                                    <img :src="entry.avatar_url" :alt="entry.name" class="w-7 h-7 rounded-full object-cover shrink-0" />
                                </template>
                                <template x-if="!entry.avatar_url">
                                    <div class="w-7 h-7 rounded-full bg-indigo-500/20 border border-indigo-400/30 shrink-0"></div>
                                </template>
                                <span class="font-sans text-sm text-white" x-text="entry.name"></span>
                                <span
                                    x-show="entry.is_me"
                                    class="font-mono text-[10px] px-1.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-400/30"
                                >you</span>
                            </div>
                        </td>
                        <td class="px-6 py-3 font-mono text-sm text-right font-bold text-indigo-300" x-text="entry.score"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
