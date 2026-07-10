const ROWS = 9;
const COLS = 9;
const MINES = 10;

function index(row, col) {
    return row * COLS + col;
}

function neighbors(row, col) {
    const cells = [];

    for (let dr = -1; dr <= 1; dr++) {
        for (let dc = -1; dc <= 1; dc++) {
            if (dr === 0 && dc === 0) {
                continue;
            }

            const nr = row + dr;
            const nc = col + dc;

            if (nr >= 0 && nr < ROWS && nc >= 0 && nc < COLS) {
                cells.push([nr, nc]);
            }
        }
    }

    return cells;
}

export default function minesweeperGame() {
    return {
        rows: ROWS,
        cols: COLS,
        mineCount: MINES,
        cells: [],
        status: 'idle',
        flags: 0,
        revealedCount: 0,
        startedAt: null,
        elapsed: 0,
        timerId: null,
        bestTime: null,

        init() {
            this.bestTime = parseInt(localStorage.getItem('admin-minesweeper-best') || '0', 10) || null;
            this.newGame();
        },

        destroy() {
            this.stopTimer();
        },

        newGame() {
            this.stopTimer();
            this.status = 'idle';
            this.flags = 0;
            this.revealedCount = 0;
            this.startedAt = null;
            this.elapsed = 0;
            this.cells = Array.from({ length: ROWS * COLS }, () => ({
                mine: false,
                adjacent: 0,
                revealed: false,
                flagged: false,
            }));
        },

        refreshCells() {
            this.cells = this.cells.map((cell) => ({ ...cell }));
        },

        placeMines(safeRow, safeCol) {
            const forbidden = new Set(
                [[safeRow, safeCol], ...neighbors(safeRow, safeCol)].map(([r, c]) => index(r, c)),
            );

            let placed = 0;

            while (placed < MINES) {
                const spot = Math.floor(Math.random() * ROWS * COLS);

                if (forbidden.has(spot) || this.cells[spot].mine) {
                    continue;
                }

                this.cells[spot].mine = true;
                placed++;
            }

            for (let row = 0; row < ROWS; row++) {
                for (let col = 0; col < COLS; col++) {
                    const cell = this.cells[index(row, col)];

                    if (cell.mine) {
                        continue;
                    }

                    cell.adjacent = neighbors(row, col).filter(([r, c]) => this.cells[index(r, c)].mine).length;
                }
            }
        },

        startTimer() {
            this.stopTimer();
            this.startedAt = Date.now();
            this.timerId = window.setInterval(() => {
                this.elapsed = Math.floor((Date.now() - this.startedAt) / 1000);
            }, 1000);
        },

        stopTimer() {
            if (this.timerId !== null) {
                clearInterval(this.timerId);
                this.timerId = null;
            }
        },

        minesLeft() {
            return Math.max(0, MINES - this.flags);
        },

        reveal(row, col) {
            if (this.status === 'won' || this.status === 'lost') {
                return;
            }

            const idx = index(row, col);
            const cell = this.cells[idx];

            if (cell.revealed || cell.flagged) {
                return;
            }

            if (this.status === 'idle') {
                this.placeMines(row, col);
                this.status = 'playing';
                this.startTimer();
            }

            if (cell.mine) {
                this.revealAllMines();
                this.status = 'lost';
                this.stopTimer();
                this.refreshCells();

                return;
            }

            this.floodReveal(row, col);
            this.refreshCells();
            this.checkWin();
        },

        floodReveal(row, col) {
            const queue = [[row, col]];
            const visited = new Set();

            while (queue.length > 0) {
                const [r, c] = queue.shift();
                const idx = index(r, c);

                if (visited.has(idx)) {
                    continue;
                }

                visited.add(idx);

                const cell = this.cells[idx];

                if (cell.flagged || cell.revealed) {
                    continue;
                }

                cell.revealed = true;
                this.revealedCount++;

                if (cell.adjacent === 0) {
                    for (const [nr, nc] of neighbors(r, c)) {
                        const neighbor = this.cells[index(nr, nc)];

                        if (!neighbor.revealed && !neighbor.flagged) {
                            queue.push([nr, nc]);
                        }
                    }
                }
            }
        },

        toggleFlag(row, col, event) {
            event?.preventDefault();

            if (this.status === 'won' || this.status === 'lost') {
                return;
            }

            const cell = this.cells[index(row, col)];

            if (cell.revealed) {
                return;
            }

            cell.flagged = !cell.flagged;
            this.flags += cell.flagged ? 1 : -1;
            this.refreshCells();
        },

        revealAllMines() {
            this.cells.forEach((cell) => {
                if (cell.mine) {
                    cell.revealed = true;
                }
            });
        },

        checkWin() {
            const safeCells = ROWS * COLS - MINES;

            if (this.revealedCount < safeCells) {
                return;
            }

            this.status = 'won';
            this.stopTimer();

            this.cells.forEach((cell) => {
                if (cell.mine) {
                    cell.flagged = true;
                }
            });

            if (!this.bestTime || this.elapsed < this.bestTime) {
                this.bestTime = this.elapsed;
                localStorage.setItem('admin-minesweeper-best', String(this.bestTime));
            }

            this.refreshCells();
            this.submitWin();
        },

        async submitWin() {
            try {
                await fetch('/admin/leaderboard', {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content ?? '',
                    },
                    body: JSON.stringify({
                        game: 'minesweeper',
                    }),
                });

                window.dispatchEvent(new CustomEvent('leaderboard-updated'));
            } catch {
                // ignore transient submit errors
            }
        },

        cellLabel(cell) {
            if (!cell.revealed) {
                return cell.flagged ? '🚩' : '';
            }

            if (cell.mine) {
                return '💣';
            }

            return cell.adjacent > 0 ? String(cell.adjacent) : '';
        },

        cellClasses(cell) {
            if (!cell.revealed && !cell.flagged) {
                return 'bg-white/10 hover:bg-indigo-500/20 border-white/15 text-transparent';
            }

            if (cell.flagged) {
                return 'bg-amber-500/15 border-amber-400/30 text-amber-300';
            }

            if (cell.mine) {
                return 'bg-red-500/20 border-red-400/40 text-red-300';
            }

            if (cell.adjacent === 0) {
                return 'bg-slate-900 border-white/5 text-transparent';
            }

            const colors = [
                '',
                'text-indigo-300',
                'text-emerald-300',
                'text-red-300',
                'text-purple-300',
                'text-amber-300',
                'text-cyan-300',
                'text-white',
                'text-white/50',
            ];

            return `bg-slate-900 border-white/10 ${colors[cell.adjacent] ?? 'text-white'}`;
        },

        statusMessage() {
            if (this.status === 'idle') {
                return 'Click a cell to start';
            }

            if (this.status === 'playing') {
                return 'Left click reveal · right click flag';
            }

            if (this.status === 'won') {
                return `Cleared in ${this.elapsed}s!`;
            }

            return 'BOOM — mine hit';
        },

        formatTime(seconds) {
            return `${String(seconds).padStart(2, '0')}s`;
        },
    };
}
