export default function adminMinigamesHub(config) {
    return {
        openGame: null,
        activeGameId: null,
        incomingInvites: [],
        invitePollId: null,
        dismissedInviteIds: [],
        ...config,

        init() {
            this.pollInvites();
            this.invitePollId = window.setInterval(() => this.pollInvites(), 5000);
        },

        inviteBaseUrl(invite) {
            return invite.game === 'pong' ? this.pongGameBaseUrl : this.ticTacToeGameBaseUrl;
        },

        acceptUrl(invite) {
            return `${this.inviteBaseUrl(invite)}/${invite.id}/accept`;
        },

        declineUrl(invite) {
            return `${this.inviteBaseUrl(invite)}/${invite.id}/decline`;
        },

        inviteKey(invite) {
            return `${invite.game}-${invite.id}`;
        },

        destroy() {
            if (this.invitePollId !== null) {
                clearInterval(this.invitePollId);
            }
        },

        open(game, gameId = null) {
            this.openGame = game;
            this.activeGameId = gameId;
        },

        close() {
            this.openGame = null;
            this.activeGameId = null;
        },

        gameLabel(game) {
            if (game === 'snake') {
                return 'Snake';
            }

            if (game === 'pong') {
                return 'Pong';
            }

            if (game === 'minesweeper') {
                return 'Minesweeper';
            }

            return 'Tic Tac Toe';
        },

        gameIcon(game) {
            if (game === 'snake') {
                return '🐍';
            }

            if (game === 'pong') {
                return '🏓';
            }

            if (game === 'minesweeper') {
                return '💣';
            }

            return '⭕';
        },

        inviteLabel(game) {
            return game === 'pong' ? 'Pong invite' : 'Tic Tac Toe invite';
        },

        visibleInvites() {
            return this.incomingInvites.filter(
                (invite) => !this.dismissedInviteIds.includes(this.inviteKey(invite)),
            );
        },

        async pollInvites() {
            try {
                const [ticTacToeResponse, pongResponse] = await Promise.all([
                    fetch(this.ticTacToeInvitesUrl, { headers: { Accept: 'application/json' } }),
                    fetch(this.pongInvitesUrl, { headers: { Accept: 'application/json' } }),
                ]);

                const incoming = [];

                if (ticTacToeResponse.ok) {
                    const data = await ticTacToeResponse.json();
                    incoming.push(...(data.incoming ?? []));
                }

                if (pongResponse.ok) {
                    const data = await pongResponse.json();
                    incoming.push(...(data.incoming ?? []));
                }

                this.incomingInvites = incoming.sort(
                    (a, b) => new Date(b.created_at) - new Date(a.created_at),
                );
            } catch {
                // ignore transient poll errors
            }
        },

        async acceptInvite(invite) {
            try {
                const response = await fetch(this.acceptUrl(invite), {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                });

                if (!response.ok) {
                    return;
                }

                this.dismissInvite(invite);
                this.open(invite.game, invite.id);
            } catch {
                // ignore
            }
        },

        async declineInvite(invite) {
            try {
                await fetch(this.declineUrl(invite), {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                });
            } catch {
                // ignore
            }

            this.dismissInvite(invite);
        },

        dismissInvite(invite) {
            const key = this.inviteKey(invite);

            if (!this.dismissedInviteIds.includes(key)) {
                this.dismissedInviteIds.push(key);
            }

            this.incomingInvites = this.incomingInvites.filter(
                (item) => this.inviteKey(item) !== key,
            );
        },

        openInviteGame(invite) {
            this.acceptInvite(invite);
        },
    };
}
