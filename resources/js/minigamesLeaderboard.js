export default function minigamesLeaderboard(config) {
    return {
        selectedGame: 'snake',
        entries: [],
        scoreLabel: 'Points',
        loading: false,
        games: [
            { id: 'snake', label: 'Snake', icon: '🐍' },
            { id: 'tic-tac-toe', label: 'Tic Tac Toe', icon: '⭕' },
            { id: 'pong', label: 'Pong', icon: '🏓' },
            { id: 'minesweeper', label: 'Minesweeper', icon: '💣' },
        ],
        ...config,

        init() {
            this.load();
            window.addEventListener('leaderboard-updated', () => this.load());
        },

        async selectGame(game) {
            this.selectedGame = game;

            await this.load();
        },

        async load() {
            this.loading = true;

            try {
                const response = await fetch(`${this.indexUrl}?game=${encodeURIComponent(this.selectedGame)}`, {
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) {
                    return;
                }

                const data = await response.json();
                this.entries = data.entries ?? [];
                this.scoreLabel = data.score_label ?? 'Score';
            } catch {
                // ignore transient load errors
            } finally {
                this.loading = false;
            }
        },

        gameIcon(gameId) {
            return this.games.find((game) => game.id === gameId)?.icon ?? '🎮';
        },
    };
}
