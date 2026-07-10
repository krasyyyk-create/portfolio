const WIN_LINES = [
    [0, 1, 2], [3, 4, 5], [6, 7, 8],
    [0, 3, 6], [1, 4, 7], [2, 5, 8],
    [0, 4, 8], [2, 4, 6],
];

function winningSymbol(board) {
    for (const [a, b, c] of WIN_LINES) {
        if (board[a] && board[a] === board[b] && board[b] === board[c]) {
            return board[a];
        }
    }

    return null;
}

function emptyBoard() {
    return Array(9).fill(null);
}

function botMove(board) {
    const tryCell = (symbol) => {
        for (let i = 0; i < 9; i++) {
            if (board[i] !== null) {
                continue;
            }

            const next = [...board];
            next[i] = symbol;

            if (winningSymbol(next) === symbol) {
                return i;
            }
        }

        return null;
    };

    const win = tryCell('o');
    if (win !== null) {
        return win;
    }

    const block = tryCell('x');
    if (block !== null) {
        return block;
    }

    const preferred = [4, 0, 2, 6, 8, 1, 3, 5, 7];
    const open = preferred.filter((i) => board[i] === null);

    return open[Math.floor(Math.random() * open.length)] ?? null;
}

export default function ticTacToeGame(config) {
    return {
        mode: 'menu',
        board: emptyBoard(),
        currentTurn: 'x',
        status: 'active',
        winner: null,
        mySymbol: 'x',
        game: null,
        admins: [],
        loadingAdmins: false,
        invitingId: null,
        waitingGame: null,
        pollId: null,
        error: null,
        gameBaseUrl: '/admin/tic-tac-toe',
        ...config,

        gameUrl(id) {
            return `${this.gameBaseUrl}/${id}`;
        },

        moveUrl(id) {
            return `${this.gameBaseUrl}/${id}/move`;
        },

        init() {
            if (this.initialGameId) {
                this.loadOnlineGame(this.initialGameId);
            }
        },

        destroy() {
            this.stopPolling();
        },

        resetBot() {
            this.mode = 'bot';
            this.board = emptyBoard();
            this.currentTurn = 'x';
            this.status = 'active';
            this.winner = null;
            this.mySymbol = 'x';
            this.error = null;
        },

        async openOnline() {
            this.mode = 'online';
            this.error = null;
            this.waitingGame = null;
            this.game = null;
            await this.loadAdmins();
        },

        async loadAdmins() {
            this.loadingAdmins = true;

            try {
                const response = await fetch(this.adminsUrl, {
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) {
                    throw new Error('Could not load admins.');
                }

                const data = await response.json();
                this.admins = data.admins ?? [];
            } catch (error) {
                this.error = error.message;
            } finally {
                this.loadingAdmins = false;
            }
        },

        async inviteAdmin(adminId) {
            this.invitingId = adminId;
            this.error = null;

            try {
                const response = await fetch(this.inviteUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                    body: JSON.stringify({ opponent_id: adminId }),
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message ?? 'Invite failed.');
                }

                this.waitingGame = data.game;
                this.game = data.game;
                this.mode = 'online-play';
                this.applyServerGame(data.game);
                this.startPolling();
            } catch (error) {
                this.error = error.message;
            } finally {
                this.invitingId = null;
            }
        },

        async loadOnlineGame(gameId) {
            this.mode = 'online-play';
            this.error = null;

            try {
                const response = await fetch(this.gameUrl(gameId), {
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) {
                    throw new Error('Could not load game.');
                }

                const data = await response.json();
                this.game = data.game;
                this.applyServerGame(data.game);

                if (data.game.status === 'pending' && data.game.my_symbol === 'o') {
                    this.mode = 'online';
                } else if (data.game.status === 'pending') {
                    this.waitingGame = data.game;
                    this.mode = 'online-play';
                } else {
                    this.mode = 'online-play';
                }

                this.startPolling();
            } catch (error) {
                this.error = error.message;
                this.mode = 'online';
            }
        },

        applyServerGame(game) {
            this.game = game;
            this.board = [...game.board];
            this.currentTurn = game.current_turn;
            this.status = game.status;
            this.mySymbol = game.my_symbol;
            this.winner = game.winner_id;
        },

        startPolling() {
            this.stopPolling();

            if (!this.game?.id) {
                return;
            }

            this.pollId = window.setInterval(() => this.pollGame(), 2000);
        },

        stopPolling() {
            if (this.pollId !== null) {
                clearInterval(this.pollId);
                this.pollId = null;
            }
        },

        async pollGame() {
            if (!this.game?.id) {
                return;
            }

            try {
                const response = await fetch(this.gameUrl(this.game.id), {
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) {
                    return;
                }

                const data = await response.json();
                this.applyServerGame(data.game);

                if (['won', 'draw', 'declined', 'cancelled'].includes(data.game.status)) {
                    this.stopPolling();
                }

                if (data.game.status === 'active' && this.mode !== 'online-play') {
                    this.mode = 'online-play';
                }
            } catch {
                // ignore transient poll errors
            }
        },

        async playCell(index) {
            if (this.mode === 'bot') {
                this.playBotCell(index);
                return;
            }

            if (this.mode !== 'online-play' || !this.game?.is_my_turn || this.board[index] !== null) {
                return;
            }

            if (!['active'].includes(this.status)) {
                return;
            }

            try {
                const response = await fetch(this.moveUrl(this.game.id), {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                    body: JSON.stringify({ cell: index }),
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message ?? 'Move failed.');
                }

                this.applyServerGame(data.game);

                if (['won', 'draw'].includes(data.game.status)) {
                    this.stopPolling();
                }

                if (data.game.status === 'won') {
                    window.dispatchEvent(new CustomEvent('leaderboard-updated'));
                }
            } catch (error) {
                this.error = error.message;
            }
        },

        playBotCell(index) {
            if (this.status !== 'active' || this.board[index] !== null || this.currentTurn !== 'x') {
                return;
            }

            const board = [...this.board];
            board[index] = 'x';

            let winner = winningSymbol(board);
            if (winner) {
                this.board = board;
                this.status = 'won';
                this.winner = 'x';
                return;
            }

            if (!board.includes(null)) {
                this.board = board;
                this.status = 'draw';
                return;
            }

            const botIndex = botMove(board);
            if (botIndex === null) {
                this.board = board;
                this.status = 'draw';
                return;
            }

            board[botIndex] = 'o';
            winner = winningSymbol(board);

            this.board = board;

            if (winner) {
                this.status = 'won';
                this.winner = 'o';
                return;
            }

            if (!board.includes(null)) {
                this.status = 'draw';
            }
        },

        cellLabel(value) {
            if (value === 'x') {
                return 'X';
            }

            if (value === 'o') {
                return 'O';
            }

            return '';
        },

        statusMessage() {
            if (this.mode === 'bot') {
                if (this.status === 'won') {
                    return this.winner === 'x' ? 'You win!' : 'Bot wins!';
                }

                if (this.status === 'draw') {
                    return 'Draw game.';
                }

                return 'You are X. Your turn.';
            }

            if (!this.game) {
                return '';
            }

            if (this.game.status === 'pending') {
                return this.game.my_symbol === 'x'
                    ? `Waiting for ${this.game.player_o?.name ?? 'opponent'}...`
                    : 'Invite pending.';
            }

            if (this.game.status === 'won') {
                if (this.game.winner_id === this.currentUserId) {
                    return 'You win!';
                }

                return 'You lose.';
            }

            if (this.game.status === 'draw') {
                return 'Draw game.';
            }

            return this.game.is_my_turn ? 'Your turn.' : "Opponent's turn.";
        },

        backToMenu() {
            this.stopPolling();
            this.mode = 'menu';
            this.game = null;
            this.waitingGame = null;
            this.error = null;
        },
    };
}
