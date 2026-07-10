<div
    x-data="adminMinigamesHub({
        ticTacToeInvitesUrl: @js(route('admin.tic-tac-toe.invites')),
        pongInvitesUrl: @js(route('admin.pong.invites')),
        ticTacToeGameBaseUrl: @js(url('/admin/tic-tac-toe')),
        pongGameBaseUrl: @js(url('/admin/pong')),
        csrfToken: @js(csrf_token()),
    })"
    x-init="init()"
    @open-minigame.window="open($event.detail?.game, $event.detail?.gameId ?? null)"
    @keydown.escape.window="close()"
    class="fixed inset-0 z-[90] pointer-events-none"
>
    <template x-for="invite in visibleInvites()" :key="invite.id">
        <div
            class="fixed bottom-6 right-6 z-[90] w-full max-w-sm glass-card border border-indigo-400/30 rounded-xl p-4 shadow-2xl shadow-black/40 pointer-events-auto"
            :style="`bottom: ${6 + ($index * 5.5)}rem`"
        >
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center shrink-0 text-lg" x-text="gameIcon(invite.game)"></div>
                <div class="flex-1 min-w-0 space-y-1">
                    <p class="font-mono text-[10px] text-indigo-300 uppercase tracking-wider" x-text="inviteLabel(invite.game)"></p>
                    <p class="font-sans text-sm text-white">
                        <span class="font-medium" x-text="invite.opponent?.name ?? 'An admin'"></span>
                        challenged you to a match
                    </p>
                </div>
            </div>

            <div class="flex gap-2 mt-3 justify-end">
                <button
                    type="button"
                    @click.stop="declineInvite(invite)"
                    class="font-mono text-xs px-3 py-1.5 rounded-lg border border-white/10 text-white/60 hover:text-white hover:bg-white/5 transition-all cursor-pointer"
                >
                    decline
                </button>
                <button
                    type="button"
                    @click.stop="openInviteGame(invite)"
                    class="font-mono text-xs px-3 py-1.5 rounded-lg border border-indigo-400/30 bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/30 transition-all cursor-pointer"
                >
                    play
                </button>
            </div>
        </div>
    </template>

    <div
        x-show="openGame"
        x-cloak
        class="fixed inset-0 z-[95] flex items-center justify-center p-4 pointer-events-auto"
    >
        <div
            x-show="openGame === 'tic-tac-toe' || openGame === 'pong' || openGame === 'minesweeper'"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-slate-950/90 backdrop-blur-sm"
            @click="close()"
        ></div>

        <div
            x-show="openGame"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative z-10 pointer-events-auto"
            :class="openGame === 'tic-tac-toe' ? 'w-[min(100vw-2rem,24rem)]' : 'w-[min(100vw-2rem,20rem)]'"
        >
        <div class="glass-card border border-white/15 rounded-2xl shadow-2xl shadow-black/50 overflow-hidden bg-slate-950/95">
            <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between gap-3 bg-white/5">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="text-lg leading-none" x-text="gameIcon(openGame)"></span>
                    <p class="font-mono text-sm font-bold text-white truncate" x-text="gameLabel(openGame)"></p>
                </div>
                <button
                    type="button"
                    @click="close()"
                    class="font-mono text-xs text-white/50 hover:text-white border border-white/10 hover:border-white/20 w-7 h-7 rounded-lg transition-all cursor-pointer shrink-0"
                    aria-label="Close minigame"
                >
                    ✕
                </button>
            </div>

            <div class="p-4 max-h-[70vh] overflow-y-auto">
                <template x-if="openGame === 'snake'">
                    <div
                        x-data="snakeGame()"
                        x-init="init()"
                        class="space-y-3"
                    >
                        <div
                            x-ref="gameArea"
                            tabindex="0"
                            @keydown="handleKey($event)"
                            @click="$refs.gameArea.focus()"
                            class="rounded-lg border border-white/10 overflow-hidden outline-none focus:ring-2 focus:ring-indigo-400/40 mx-auto w-fit"
                        >
                            <canvas
                                x-ref="canvas"
                                :width="grid * cell"
                                :height="grid * cell"
                                class="block bg-slate-950"
                            ></canvas>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-center">
                                <p class="font-mono text-[10px] text-white/40 uppercase">Score</p>
                                <p class="font-sans text-lg font-bold text-white" x-text="score"></p>
                            </div>
                            <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-center">
                                <p class="font-mono text-[10px] text-white/40 uppercase">Best</p>
                                <p class="font-sans text-lg font-bold text-indigo-400" x-text="highScore"></p>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button
                                type="button"
                                @click="startGame()"
                                class="flex-1 font-mono text-xs px-3 py-2 rounded-lg border border-indigo-400/30 bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/30 transition-all cursor-pointer"
                            >
                                <span x-text="status === 'gameover' ? 'again' : 'start'"></span>
                            </button>
                            <button
                                type="button"
                                @click="togglePause()"
                                :disabled="status !== 'playing' && status !== 'paused'"
                                :class="(status !== 'playing' && status !== 'paused') && 'opacity-40 cursor-not-allowed'"
                                class="flex-1 font-mono text-xs px-3 py-2 rounded-lg border border-white/10 bg-white/5 text-white/70 hover:text-white transition-all cursor-pointer"
                            >
                                pause
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="openGame === 'tic-tac-toe'">
                    <div
                        x-data="ticTacToeGame({
                            adminsUrl: @js(route('admin.tic-tac-toe.admins')),
                            inviteUrl: @js(route('admin.tic-tac-toe.invite')),
                            gameBaseUrl: @js(url('/admin/tic-tac-toe')),
                            csrfToken: @js(csrf_token()),
                            currentUserId: @js(auth()->id()),
                            initialGameId: activeGameId,
                        })"
                        x-init="init()"
                        class="space-y-3"
                    >
                        <template x-if="mode === 'menu'">
                            <div class="space-y-2">
                                <button
                                    type="button"
                                    @click="resetBot()"
                                    class="w-full font-mono text-xs px-4 py-3 rounded-lg border border-indigo-400/30 bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/30 transition-all cursor-pointer text-left"
                                >
                                    vs Bot
                                    <span class="block text-[10px] text-white/40 mt-0.5">Practice against the CPU</span>
                                </button>
                                <button
                                    type="button"
                                    @click="openOnline()"
                                    class="w-full font-mono text-xs px-4 py-3 rounded-lg border border-white/10 bg-white/5 text-white/80 hover:text-white hover:border-indigo-400/30 hover:bg-indigo-500/10 transition-all cursor-pointer text-left"
                                >
                                    Online queue
                                    <span class="block text-[10px] text-white/40 mt-0.5">Invite another admin to play 1v1</span>
                                </button>
                            </div>
                        </template>

                        <template x-if="mode === 'online'">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-mono text-xs text-white/60">Invite an admin</p>
                                    <button type="button" @click="backToMenu()" class="font-mono text-[10px] text-white/40 hover:text-white cursor-pointer">back</button>
                                </div>

                                <template x-if="loadingAdmins">
                                    <p class="font-mono text-xs text-white/40 text-center py-4">Loading admins...</p>
                                </template>

                                <template x-if="!loadingAdmins && admins.length === 0">
                                    <p class="font-mono text-xs text-white/40 text-center py-4">No other admins online to invite.</p>
                                </template>

                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    <template x-for="admin in admins" :key="admin.id">
                                        <div class="flex items-center gap-3 rounded-lg border border-white/10 bg-white/5 px-3 py-2">
                                            <template x-if="admin.avatar_url">
                                                <img :src="admin.avatar_url" :alt="admin.name" class="w-7 h-7 rounded-full object-cover shrink-0" />
                                            </template>
                                            <template x-if="!admin.avatar_url">
                                                <div class="w-7 h-7 rounded-full bg-indigo-500/20 border border-indigo-400/30 shrink-0"></div>
                                            </template>
                                            <span class="font-sans text-sm text-white flex-1 truncate" x-text="admin.name"></span>
                                            <button
                                                type="button"
                                                @click="inviteAdmin(admin.id)"
                                                :disabled="invitingId === admin.id"
                                                :class="invitingId === admin.id && 'opacity-50 cursor-wait'"
                                                class="font-mono text-[10px] px-2.5 py-1 rounded-md border border-indigo-400/30 bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/30 transition-all cursor-pointer shrink-0"
                                            >
                                                invite
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="mode === 'bot' || mode === 'online-play'">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-mono text-xs" :class="game?.is_my_turn ? 'text-emerald-400' : 'text-white/50'" x-text="statusMessage()"></p>
                                    <button
                                        type="button"
                                        @click="backToMenu()"
                                        class="font-mono text-[10px] text-white/40 hover:text-white cursor-pointer shrink-0"
                                    >
                                        menu
                                    </button>
                                </div>

                                <div class="grid grid-cols-3 gap-2 w-fit mx-auto">
                                    <template x-for="(cell, index) in board" :key="index">
                                        <button
                                            type="button"
                                            @click="playCell(index)"
                                            :disabled="mode === 'online-play' && (!game?.is_my_turn || game?.status !== 'active')"
                                            class="w-16 h-16 rounded-lg border border-white/10 bg-slate-950 font-mono text-2xl font-bold transition-all cursor-pointer hover:border-indigo-400/30 disabled:cursor-default disabled:hover:border-white/10"
                                            :class="{
                                                'text-indigo-400': cell === 'x',
                                                'text-emerald-400': cell === 'o',
                                                'text-white/20 hover:text-white/30': !cell,
                                            }"
                                            x-text="cellLabel(cell)"
                                        ></button>
                                    </template>
                                </div>

                                <template x-if="mode === 'bot' && (status === 'won' || status === 'draw')">
                                    <button
                                        type="button"
                                        @click="resetBot()"
                                        class="w-full font-mono text-xs px-3 py-2 rounded-lg border border-indigo-400/30 bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/30 transition-all cursor-pointer"
                                    >
                                        play again
                                    </button>
                                </template>

                                <template x-if="mode === 'online-play' && game && ['won', 'draw'].includes(game.status)">
                                    <button
                                        type="button"
                                        @click="backToMenu()"
                                        class="w-full font-mono text-xs px-3 py-2 rounded-lg border border-indigo-400/30 bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/30 transition-all cursor-pointer"
                                    >
                                        back to menu
                                    </button>
                                </template>
                            </div>
                        </template>

                        <p x-show="error" x-text="error" class="font-mono text-xs text-red-400"></p>
                    </div>
                </template>

                <template x-if="openGame === 'pong'">
                    <div
                        x-data="pongGame({
                            adminsUrl: @js(route('admin.pong.admins')),
                            inviteUrl: @js(route('admin.pong.invite')),
                            gameBaseUrl: @js(url('/admin/pong')),
                            csrfToken: @js(csrf_token()),
                            currentUserId: @js(auth()->id()),
                            initialGameId: activeGameId,
                        })"
                        x-init="init()"
                        class="space-y-3"
                    >
                        <template x-if="mode === 'menu'">
                            <div class="space-y-2">
                                <button
                                    type="button"
                                    @click="startBot()"
                                    class="w-full font-mono text-xs px-4 py-3 rounded-lg border border-indigo-400/30 bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/30 transition-all cursor-pointer text-left"
                                >
                                    vs Bot
                                    <span class="block text-[10px] text-white/40 mt-0.5">First to 5 points wins</span>
                                </button>
                                <button
                                    type="button"
                                    @click="openOnline()"
                                    class="w-full font-mono text-xs px-4 py-3 rounded-lg border border-white/10 bg-white/5 text-white/80 hover:text-white hover:border-indigo-400/30 hover:bg-indigo-500/10 transition-all cursor-pointer text-left"
                                >
                                    Online queue
                                    <span class="block text-[10px] text-white/40 mt-0.5">Invite another admin to play 1v1</span>
                                </button>
                            </div>
                        </template>

                        <template x-if="mode === 'online'">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-mono text-xs text-white/60">Invite an admin</p>
                                    <button type="button" @click="backToMenu()" class="font-mono text-[10px] text-white/40 hover:text-white cursor-pointer">back</button>
                                </div>

                                <template x-if="loadingAdmins">
                                    <p class="font-mono text-xs text-white/40 text-center py-4">Loading admins...</p>
                                </template>

                                <template x-if="!loadingAdmins && admins.length === 0">
                                    <p class="font-mono text-xs text-white/40 text-center py-4">No other admins to invite.</p>
                                </template>

                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    <template x-for="admin in admins" :key="admin.id">
                                        <div class="flex items-center gap-3 rounded-lg border border-white/10 bg-white/5 px-3 py-2">
                                            <template x-if="admin.avatar_url">
                                                <img :src="admin.avatar_url" :alt="admin.name" class="w-7 h-7 rounded-full object-cover shrink-0" />
                                            </template>
                                            <template x-if="!admin.avatar_url">
                                                <div class="w-7 h-7 rounded-full bg-indigo-500/20 border border-indigo-400/30 shrink-0"></div>
                                            </template>
                                            <span class="font-sans text-sm text-white flex-1 truncate" x-text="admin.name"></span>
                                            <button
                                                type="button"
                                                @click="inviteAdmin(admin.id)"
                                                :disabled="invitingId === admin.id"
                                                :class="invitingId === admin.id && 'opacity-50 cursor-wait'"
                                                class="font-mono text-[10px] px-2.5 py-1 rounded-md border border-indigo-400/30 bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/30 transition-all cursor-pointer shrink-0"
                                            >
                                                invite
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="mode === 'bot' || mode === 'online-play'">
                            <div
                                x-init="bootCanvas(); if (mode === 'online-play') startPolling()"
                                class="space-y-3"
                            >
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-mono text-xs text-white/50" x-text="statusMessage()"></p>
                                    <button
                                        type="button"
                                        @click="backToMenu()"
                                        class="font-mono text-[10px] text-white/40 hover:text-white cursor-pointer shrink-0"
                                    >
                                        menu
                                    </button>
                                </div>

                                <div
                                    x-ref="gameArea"
                                    tabindex="0"
                                    @mousemove="handleCanvasMove($event)"
                                    @click="$refs.gameArea.focus()"
                                    class="rounded-lg border border-white/10 overflow-hidden outline-none focus:ring-2 focus:ring-indigo-400/40 mx-auto w-fit cursor-crosshair"
                                >
                                    <canvas
                                        x-ref="canvas"
                                        :width="width"
                                        :height="height"
                                        class="block bg-slate-950"
                                    ></canvas>
                                </div>

                                <p class="font-mono text-[10px] text-white/40 text-center">W/S, arrows, or mouse to move your paddle</p>

                                <template x-if="mode === 'bot' && status === 'won'">
                                    <button
                                        type="button"
                                        @click="playAgain()"
                                        class="w-full font-mono text-xs px-3 py-2 rounded-lg border border-indigo-400/30 bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/30 transition-all cursor-pointer"
                                    >
                                        play again
                                    </button>
                                </template>

                                <template x-if="mode === 'online-play' && game && game.status === 'won'">
                                    <button
                                        type="button"
                                        @click="backToMenu()"
                                        class="w-full font-mono text-xs px-3 py-2 rounded-lg border border-indigo-400/30 bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/30 transition-all cursor-pointer"
                                    >
                                        back to menu
                                    </button>
                                </template>
                            </div>
                        </template>

                        <p x-show="error" x-text="error" class="font-mono text-xs text-red-400"></p>
                    </div>
                </template>

                <template x-if="openGame === 'minesweeper'">
                    <div
                        x-data="minesweeperGame()"
                        x-init="init()"
                        class="space-y-3"
                    >
                        <div class="grid grid-cols-3 gap-2">
                            <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-center">
                                <p class="font-mono text-[10px] text-white/40 uppercase">Mines</p>
                                <p class="font-sans text-lg font-bold text-amber-300" x-text="minesLeft()"></p>
                            </div>
                            <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-center">
                                <p class="font-mono text-[10px] text-white/40 uppercase">Time</p>
                                <p class="font-sans text-lg font-bold text-white" x-text="formatTime(elapsed)"></p>
                            </div>
                            <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-center">
                                <p class="font-mono text-[10px] text-white/40 uppercase">Best</p>
                                <p class="font-sans text-lg font-bold text-indigo-400" x-text="bestTime ? formatTime(bestTime) : '—'"></p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <p class="font-mono text-xs text-white/50" x-text="statusMessage()"></p>
                            <button
                                type="button"
                                @click="newGame()"
                                class="font-mono text-[10px] px-2.5 py-1 rounded-md border border-white/10 text-white/60 hover:text-white hover:border-white/20 transition-all cursor-pointer shrink-0"
                            >
                                new game
                            </button>
                        </div>

                        <div class="grid grid-cols-9 gap-1 w-fit mx-auto select-none">
                            <template x-for="(cell, i) in cells" :key="i">
                                <button
                                    type="button"
                                    @click="reveal(Math.floor(i / cols), i % cols)"
                                    @contextmenu.prevent="toggleFlag(Math.floor(i / cols), i % cols, $event)"
                                    class="w-7 h-7 rounded border font-mono text-[11px] font-bold transition-all cursor-pointer flex items-center justify-center"
                                    :class="cellClasses(cell)"
                                    x-text="cellLabel(cell)"
                                ></button>
                            </template>
                        </div>

                        <p class="font-mono text-[10px] text-white/40 text-center">Left click reveal · right click flag</p>

                        <div
                            x-show="status === 'won'"
                            x-cloak
                            class="rounded-lg border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-center space-y-1"
                        >
                            <p class="font-mono text-sm font-bold text-emerald-300">You win!</p>
                            <p class="font-mono text-xs text-white/60" x-text="`Board cleared in ${formatTime(elapsed)}`"></p>
                        </div>

                        <div
                            x-show="status === 'lost'"
                            x-cloak
                            class="rounded-lg border border-red-400/30 bg-red-500/10 px-4 py-3 text-center"
                        >
                            <p class="font-mono text-sm font-bold text-red-300">BOOM — mine hit</p>
                        </div>

                        <template x-if="status === 'won' || status === 'lost'">
                            <button
                                type="button"
                                @click="newGame()"
                                class="w-full font-mono text-xs px-3 py-2 rounded-lg border border-indigo-400/30 bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/30 transition-all cursor-pointer"
                            >
                                play again
                            </button>
                        </template>
                    </div>
                </template>
            </div>
        </div>
        </div>
    </div>
</div>
