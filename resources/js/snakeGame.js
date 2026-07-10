export default function snakeGame() {
    const GRID = 20;
    const CELL = 16;

    return {
        grid: GRID,
        cell: CELL,
        snake: [],
        direction: { x: 1, y: 0 },
        queuedDirection: { x: 1, y: 0 },
        food: { x: 0, y: 0 },
        score: 0,
        highScore: 0,
        status: 'idle',
        loopId: null,
        tickMs: 130,
        ctx: null,

        init() {
            this.highScore = parseInt(localStorage.getItem('admin-snake-high-score') || '0', 10);

            this.$nextTick(() => {
                const canvas = this.$refs.canvas;
                this.ctx = canvas.getContext('2d');
                this.resetBoard();
                this.draw();
            });
        },

        resetBoard() {
            const mid = Math.floor(this.grid / 2);

            this.snake = [
                { x: mid - 1, y: mid },
                { x: mid, y: mid },
                { x: mid + 1, y: mid },
            ];
            this.direction = { x: -1, y: 0 };
            this.queuedDirection = { x: -1, y: 0 };
            this.score = 0;
            this.tickMs = 130;
            this.spawnFood();
        },

        spawnFood() {
            let spot;

            do {
                spot = {
                    x: Math.floor(Math.random() * this.grid),
                    y: Math.floor(Math.random() * this.grid),
                };
            } while (this.snake.some((segment) => segment.x === spot.x && segment.y === spot.y));

            this.food = spot;
        },

        startGame() {
            if (this.status === 'playing') {
                return;
            }

            this.stopLoop();
            this.resetBoard();
            this.status = 'playing';
            this.$refs.gameArea?.focus();
            this.scheduleTick();
        },

        togglePause() {
            if (this.status === 'playing') {
                this.status = 'paused';
                this.stopLoop();
                this.draw();
                return;
            }

            if (this.status === 'paused') {
                this.status = 'playing';
                this.scheduleTick();
            }
        },

        scheduleTick() {
            this.stopLoop();
            this.loopId = window.setTimeout(() => this.tick(), this.tickMs);
        },

        stopLoop() {
            if (this.loopId !== null) {
                clearTimeout(this.loopId);
                this.loopId = null;
            }
        },

        tick() {
            if (this.status !== 'playing') {
                return;
            }

            this.direction = { ...this.queuedDirection };

            const head = {
                x: this.snake[0].x + this.direction.x,
                y: this.snake[0].y + this.direction.y,
            };

            if (this.isCollision(head)) {
                this.gameOver();
                return;
            }

            this.snake.unshift(head);

            if (head.x === this.food.x && head.y === this.food.y) {
                this.score += 10;
                this.tickMs = Math.max(70, this.tickMs - 3);
                this.spawnFood();
            } else {
                this.snake.pop();
            }

            this.draw();
            this.scheduleTick();
        },

        isCollision(point) {
            if (point.x < 0 || point.y < 0 || point.x >= this.grid || point.y >= this.grid) {
                return true;
            }

            return this.snake.some((segment) => segment.x === point.x && segment.y === point.y);
        },

        gameOver() {
            this.stopLoop();
            this.status = 'gameover';

            if (this.score > this.highScore) {
                this.highScore = this.score;
                localStorage.setItem('admin-snake-high-score', String(this.highScore));
            }

            this.submitScore();
            this.draw();
        },

        async submitScore() {
            if (this.score <= 0) {
                return;
            }

            try {
                await fetch('/admin/leaderboard', {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content ?? '',
                    },
                    body: JSON.stringify({
                        game: 'snake',
                        score: this.score,
                    }),
                });

                window.dispatchEvent(new CustomEvent('leaderboard-updated'));
            } catch {
                // ignore transient submit errors
            }
        },

        queueDirection(x, y) {
            const opposite = this.direction.x + x === 0 && this.direction.y + y === 0;

            if (opposite) {
                return;
            }

            this.queuedDirection = { x, y };
        },

        handleKey(event) {
            if (!['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'w', 'a', 's', 'd', ' '].includes(event.key)) {
                return;
            }

            event.preventDefault();

            if (this.status === 'idle' || this.status === 'gameover') {
                this.startGame();
            }

            switch (event.key) {
                case 'ArrowUp':
                case 'w':
                    this.queueDirection(0, -1);
                    break;
                case 'ArrowDown':
                case 's':
                    this.queueDirection(0, 1);
                    break;
                case 'ArrowLeft':
                case 'a':
                    this.queueDirection(-1, 0);
                    break;
                case 'ArrowRight':
                case 'd':
                    this.queueDirection(1, 0);
                    break;
                case ' ':
                    this.togglePause();
                    break;
            }
        },

        draw() {
            if (!this.ctx) {
                return;
            }

            const size = this.grid * this.cell;

            this.ctx.fillStyle = '#0f172a';
            this.ctx.fillRect(0, 0, size, size);

            this.ctx.strokeStyle = 'rgba(255, 255, 255, 0.04)';
            this.ctx.lineWidth = 1;

            for (let i = 0; i <= this.grid; i++) {
                this.ctx.beginPath();
                this.ctx.moveTo(i * this.cell, 0);
                this.ctx.lineTo(i * this.cell, size);
                this.ctx.stroke();

                this.ctx.beginPath();
                this.ctx.moveTo(0, i * this.cell);
                this.ctx.lineTo(size, i * this.cell);
                this.ctx.stroke();
            }

            this.ctx.fillStyle = '#34d399';
            this.ctx.shadowColor = 'rgba(52, 211, 153, 0.6)';
            this.ctx.shadowBlur = 8;
            this.ctx.fillRect(
                this.food.x * this.cell + 2,
                this.food.y * this.cell + 2,
                this.cell - 4,
                this.cell - 4,
            );
            this.ctx.shadowBlur = 0;

            this.snake.forEach((segment, index) => {
                const alpha = 1 - index * 0.03;
                this.ctx.fillStyle = index === 0 ? '#818cf8' : `rgba(129, 140, 248, ${Math.max(0.4, alpha)})`;
                this.ctx.shadowColor = index === 0 ? 'rgba(129, 140, 248, 0.8)' : 'transparent';
                this.ctx.shadowBlur = index === 0 ? 10 : 0;
                this.ctx.fillRect(
                    segment.x * this.cell + 1,
                    segment.y * this.cell + 1,
                    this.cell - 2,
                    this.cell - 2,
                );
            });

            this.ctx.shadowBlur = 0;

            if (this.status === 'idle' || this.status === 'paused' || this.status === 'gameover') {
                this.ctx.fillStyle = 'rgba(15, 23, 42, 0.75)';
                this.ctx.fillRect(0, 0, size, size);

                this.ctx.fillStyle = '#ffffff';
                this.ctx.font = '600 13px "JetBrains Mono", monospace';
                this.ctx.textAlign = 'center';

                if (this.status === 'idle') {
                    this.ctx.fillText('Press Start or any arrow key', size / 2, size / 2 - 8);
                    this.ctx.fillStyle = 'rgba(255, 255, 255, 0.5)';
                    this.ctx.font = '11px "JetBrains Mono", monospace';
                    this.ctx.fillText('WASD / arrows · space to pause', size / 2, size / 2 + 14);
                } else if (this.status === 'paused') {
                    this.ctx.fillText('Paused', size / 2, size / 2);
                } else {
                    this.ctx.fillText('Game Over', size / 2, size / 2 - 8);
                    this.ctx.fillStyle = 'rgba(255, 255, 255, 0.5)';
                    this.ctx.font = '11px "JetBrains Mono", monospace';
                    this.ctx.fillText(`Score: ${this.score}`, size / 2, size / 2 + 14);
                }
            }
        },
    };
}
