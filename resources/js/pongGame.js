const WIDTH = 480;
const HEIGHT = 270;
const PADDLE_WIDTH = 12;
const PADDLE_HEIGHT = 72;
const BALL_RADIUS = 7;
const WIN_SCORE = 5;
const BALL_SPEED = 140;
const PADDLE_MARGIN = 15;
const BOT_SPEED = 48;

function clampPaddleY(y) {
    const half = PADDLE_HEIGHT / 2;

    return Math.max(half, Math.min(HEIGHT - half, y));
}

function initialBotState() {
    const direction = Math.random() > 0.5 ? 1 : -1;

    return {
        leftPaddleY: HEIGHT / 2,
        rightPaddleY: HEIGHT / 2,
        ballX: WIDTH / 2,
        ballY: HEIGHT / 2,
        ballVx: BALL_SPEED * direction,
        ballVy: (Math.random() * 0.7 - 0.35) * BALL_SPEED,
        scoreLeft: 0,
        scoreRight: 0,
        status: 'active',
        winner: null,
    };
}

export default function pongGame(config) {
    return {
        mode: 'menu',
        width: WIDTH,
        height: HEIGHT,
        leftPaddleY: HEIGHT / 2,
        rightPaddleY: HEIGHT / 2,
        ballX: WIDTH / 2,
        ballY: HEIGHT / 2,
        ballVx: BALL_SPEED,
        ballVy: 0,
        scoreLeft: 0,
        scoreRight: 0,
        status: 'active',
        winner: null,
        game: null,
        admins: [],
        loadingAdmins: false,
        invitingId: null,
        pollId: null,
        loopId: null,
        lastFrame: null,
        ctx: null,
        error: null,
        keys: { up: false, down: false },
        gameBaseUrl: '/admin/pong',
        ...config,

        gameUrl(id) {
            return `${this.gameBaseUrl}/${id}`;
        },

        paddleUrl(id) {
            return `${this.gameBaseUrl}/${id}/paddle`;
        },

        init() {
            if (this.initialGameId) {
                this.loadOnlineGame(this.initialGameId);
            }
        },

        destroy() {
            this.stopLoop();
            this.stopPolling();
            this.teardownInput();
        },

        setupInput() {
            this.teardownInput();

            this._keyDown = (event) => {
                if (event.key === 'w' || event.key === 'ArrowUp') {
                    this.keys.up = true;
                    event.preventDefault();
                }
                if (event.key === 's' || event.key === 'ArrowDown') {
                    this.keys.down = true;
                    event.preventDefault();
                }
            };

            this._keyUp = (event) => {
                if (event.key === 'w' || event.key === 'ArrowUp') {
                    this.keys.up = false;
                }
                if (event.key === 's' || event.key === 'ArrowDown') {
                    this.keys.down = false;
                }
            };

            window.addEventListener('keydown', this._keyDown);
            window.addEventListener('keyup', this._keyUp);
        },

        teardownInput() {
            if (this._keyDown) {
                window.removeEventListener('keydown', this._keyDown);
                window.removeEventListener('keyup', this._keyUp);
                this._keyDown = null;
                this._keyUp = null;
            }
        },

        bootCanvas() {
            const canvas = this.$refs.canvas;

            if (!canvas) {
                this.$nextTick(() => this.bootCanvas());

                return;
            }

            this.ctx = canvas.getContext('2d');
            this.lastFrame = performance.now();
            this.draw();

            if (!this.loopId) {
                this.startLoop();
            }
        },

        startBot() {
            this.mode = 'bot';
            this.error = null;
            this.stopLoop();
            this.stopPolling();
            this.ctx = null;
            Object.assign(this, initialBotState());
            this.setupInput();
        },

        async openOnline() {
            this.mode = 'online';
            this.error = null;
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

                this.game = data.game;
                this.mode = 'online-play';
                this.applyServerGame(data.game);
                this.setupInput();
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
                this.applyServerGame(data.game);
                this.setupInput();
            } catch (error) {
                this.error = error.message;
                this.mode = 'online';
            }
        },

        applyServerGame(game) {
            this.game = game;
            this.leftPaddleY = game.left_paddle_y;
            this.rightPaddleY = game.right_paddle_y;
            this.ballX = game.ball_x;
            this.ballY = game.ball_y;
            this.scoreLeft = game.score_left;
            this.scoreRight = game.score_right;
            this.status = game.status;
            this.winner = game.winner_id;
        },

        startPolling() {
            this.stopPolling();

            if (!this.game?.id) {
                return;
            }

            this.pollId = window.setInterval(() => this.pollGame(), 80);
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

                if (['won', 'declined', 'cancelled'].includes(data.game.status)) {
                    this.stopPolling();
                    this.stopLoop();
                }

                if (data.game.status === 'won') {
                    window.dispatchEvent(new CustomEvent('leaderboard-updated'));
                }
            } catch {
                // ignore transient poll errors
            }
        },

        async syncPaddle(y) {
            if (!this.game?.id || this.game.status !== 'active') {
                return;
            }

            try {
                await fetch(this.paddleUrl(this.game.id), {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                    body: JSON.stringify({ y }),
                });
            } catch {
                // ignore transient sync errors
            }
        },

        startLoop() {
            this.stopLoop();

            const frame = (timestamp) => {
                const delta = Math.min(0.05, (timestamp - (this.lastFrame ?? timestamp)) / 1000);
                this.lastFrame = timestamp;

                if (this.mode === 'bot') {
                    this.tickBot(delta);
                } else if (this.mode === 'online-play') {
                    this.tickOnline(delta);
                }

                this.draw();
                this.loopId = requestAnimationFrame(frame);
            };

            this.loopId = requestAnimationFrame(frame);
        },

        stopLoop() {
            if (this.loopId !== null) {
                cancelAnimationFrame(this.loopId);
                this.loopId = null;
            }
        },

        tickBot(delta) {
            if (this.status !== 'active') {
                return;
            }

            const paddleSpeed = 220 * delta;

            if (this.keys.up) {
                this.leftPaddleY -= paddleSpeed;
            }
            if (this.keys.down) {
                this.leftPaddleY += paddleSpeed;
            }

            this.leftPaddleY = clampPaddleY(this.leftPaddleY);

            if (this.ballVx > 0) {
                const aimError = (Math.random() - 0.5) * 48;
                const targetY = this.ballY + aimError;
                const botDiff = targetY - this.rightPaddleY;

                if (Math.abs(botDiff) > 14) {
                    const speed = BOT_SPEED * (0.65 + Math.random() * 0.35);
                    this.rightPaddleY += Math.sign(botDiff) * Math.min(Math.abs(botDiff), speed * delta);
                }
            } else {
                const centerDiff = (HEIGHT / 2) - this.rightPaddleY;
                this.rightPaddleY += Math.sign(centerDiff) * Math.min(Math.abs(centerDiff), 28 * delta);
            }

            this.rightPaddleY = clampPaddleY(this.rightPaddleY);

            this.ballX += this.ballVx * delta;
            this.ballY += this.ballVy * delta;

            if (this.ballY - BALL_RADIUS <= 0) {
                this.ballY = BALL_RADIUS;
                this.ballVy = Math.abs(this.ballVy);
            } else if (this.ballY + BALL_RADIUS >= HEIGHT) {
                this.ballY = HEIGHT - BALL_RADIUS;
                this.ballVy = -Math.abs(this.ballVy);
            }

            this.handleBotPaddleCollision('left');
            this.handleBotPaddleCollision('right');

            if (this.ballX < -BALL_RADIUS) {
                this.scoreRight++;
                this.resetBotBall(1);
            } else if (this.ballX > WIDTH + BALL_RADIUS) {
                this.scoreLeft++;
                this.resetBotBall(-1);
            }

            if (this.scoreLeft >= WIN_SCORE) {
                this.status = 'won';
                this.winner = 'left';
                this.stopLoop();
            } else if (this.scoreRight >= WIN_SCORE) {
                this.status = 'won';
                this.winner = 'right';
                this.stopLoop();
            }
        },

        handleBotPaddleCollision(side) {
            const paddleX = side === 'left'
                ? PADDLE_MARGIN
                : WIDTH - PADDLE_MARGIN - PADDLE_WIDTH;
            const paddleY = side === 'left' ? this.leftPaddleY : this.rightPaddleY;
            const paddleTop = paddleY - PADDLE_HEIGHT / 2;
            const paddleBottom = paddleY + PADDLE_HEIGHT / 2;
            const movingToward = side === 'left' ? this.ballVx < 0 : this.ballVx > 0;

            if (!movingToward) {
                return;
            }

            const ballLeft = this.ballX - BALL_RADIUS;
            const ballRight = this.ballX + BALL_RADIUS;
            const ballTop = this.ballY - BALL_RADIUS;
            const ballBottom = this.ballY + BALL_RADIUS;
            const paddleRight = paddleX + PADDLE_WIDTH;

            const overlapY = ballBottom >= paddleTop && ballTop <= paddleBottom;
            const overlapX = side === 'left'
                ? ballLeft <= paddleRight && ballRight >= paddleX
                : ballRight >= paddleX && ballLeft <= paddleRight;

            if (!overlapX || !overlapY) {
                return;
            }

            const relative = Math.max(-1, Math.min(1, (this.ballY - paddleY) / (PADDLE_HEIGHT / 2)));
            this.ballVx = (side === 'left' ? 1 : -1) * Math.abs(this.ballVx);
            this.ballVy = relative * BALL_SPEED * 0.75;
            this.ballX = side === 'left'
                ? paddleRight + BALL_RADIUS
                : paddleX - BALL_RADIUS;
        },

        resetBotBall(direction) {
            this.ballX = WIDTH / 2;
            this.ballY = HEIGHT / 2;
            this.ballVx = BALL_SPEED * direction;
            this.ballVy = (Math.random() * 0.7 - 0.35) * BALL_SPEED;
        },

        tickOnline(delta) {
            if (!this.game || this.game.status !== 'active') {
                return;
            }

            const paddleSpeed = 220 * delta;
            let myY = this.game.my_side === 'left' ? this.leftPaddleY : this.rightPaddleY;

            if (this.keys.up) {
                myY -= paddleSpeed;
            }
            if (this.keys.down) {
                myY += paddleSpeed;
            }

            myY = clampPaddleY(myY);

            if (this.game.my_side === 'left') {
                this.leftPaddleY = myY;
            } else {
                this.rightPaddleY = myY;
            }

            if (!this._lastSync || performance.now() - this._lastSync > 50) {
                this._lastSync = performance.now();
                this.syncPaddle(myY);
            }
        },

        handleCanvasMove(event) {
            if (this.mode !== 'bot' && (this.mode !== 'online-play' || !this.game)) {
                return;
            }

            const rect = event.currentTarget.getBoundingClientRect();
            const y = ((event.clientY - rect.top) / rect.height) * HEIGHT;

            if (this.mode === 'bot') {
                this.leftPaddleY = clampPaddleY(y);
                return;
            }

            if (this.game.my_side === 'left') {
                this.leftPaddleY = clampPaddleY(y);
            } else {
                this.rightPaddleY = clampPaddleY(y);
            }

            if (!this._lastSync || performance.now() - this._lastSync > 50) {
                this._lastSync = performance.now();
                this.syncPaddle(clampPaddleY(y));
            }
        },

        draw() {
            if (!this.ctx) {
                return;
            }

            this.ctx.fillStyle = '#0f172a';
            this.ctx.fillRect(0, 0, WIDTH, HEIGHT);

            this.ctx.strokeStyle = 'rgba(255, 255, 255, 0.08)';
            this.ctx.setLineDash([4, 8]);
            this.ctx.beginPath();
            this.ctx.moveTo(WIDTH / 2, 0);
            this.ctx.lineTo(WIDTH / 2, HEIGHT);
            this.ctx.stroke();
            this.ctx.setLineDash([]);

            this.ctx.fillStyle = '#818cf8';
            this.ctx.fillRect(
                PADDLE_MARGIN,
                this.leftPaddleY - PADDLE_HEIGHT / 2,
                PADDLE_WIDTH,
                PADDLE_HEIGHT,
            );

            this.ctx.fillStyle = '#34d399';
            this.ctx.fillRect(
                WIDTH - PADDLE_MARGIN - PADDLE_WIDTH,
                this.rightPaddleY - PADDLE_HEIGHT / 2,
                PADDLE_WIDTH,
                PADDLE_HEIGHT,
            );

            this.ctx.beginPath();
            this.ctx.fillStyle = '#ffffff';
            this.ctx.arc(this.ballX, this.ballY, BALL_RADIUS, 0, Math.PI * 2);
            this.ctx.fill();

            this.ctx.fillStyle = 'rgba(255, 255, 255, 0.7)';
            this.ctx.font = '600 16px "JetBrains Mono", monospace';
            this.ctx.fillText(String(this.scoreLeft), WIDTH / 2 - 28, 22);
            this.ctx.fillText(String(this.scoreRight), WIDTH / 2 + 16, 22);

            const gameWon = this.status === 'won' || this.game?.status === 'won';

            if (gameWon) {
                this.ctx.fillStyle = 'rgba(15, 23, 42, 0.75)';
                this.ctx.fillRect(0, 0, WIDTH, HEIGHT);
                this.ctx.fillStyle = '#ffffff';
                this.ctx.font = '600 14px "JetBrains Mono", monospace';
                this.ctx.textAlign = 'center';
                this.ctx.fillText(this.statusMessage(), WIDTH / 2, HEIGHT / 2);
                this.ctx.textAlign = 'left';
            } else if (this.mode === 'online-play' && this.game?.status === 'pending') {
                this.ctx.fillStyle = 'rgba(15, 23, 42, 0.75)';
                this.ctx.fillRect(0, 0, WIDTH, HEIGHT);
                this.ctx.fillStyle = '#ffffff';
                this.ctx.font = '600 12px "JetBrains Mono", monospace';
                this.ctx.textAlign = 'center';
                this.ctx.fillText('Waiting for opponent...', WIDTH / 2, HEIGHT / 2);
                this.ctx.textAlign = 'left';
            }
        },

        statusMessage() {
            if (this.mode === 'bot') {
                if (this.status === 'won') {
                    return this.winner === 'left' ? 'You win!' : 'Bot wins!';
                }

                return 'W/S or mouse to move';
            }

            if (!this.game) {
                return '';
            }

            if (this.game.status === 'pending') {
                return this.game.my_side === 'left'
                    ? `Waiting for ${this.game.player_right?.name ?? 'opponent'}...`
                    : 'Invite pending.';
            }

            if (this.game.status === 'won') {
                return this.game.winner_id === this.currentUserId ? 'You win!' : 'You lose.';
            }

            return 'W/S or mouse to move';
        },

        backToMenu() {
            this.stopLoop();
            this.stopPolling();
            this.teardownInput();
            this.ctx = null;
            this.mode = 'menu';
            this.game = null;
            this.error = null;
        },

        playAgain() {
            if (this.mode === 'bot') {
                this.stopLoop();
                this.ctx = null;
                Object.assign(this, initialBotState());
                this.bootCanvas();
            }
        },
    };
}
