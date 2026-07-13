import Alpine from 'alpinejs';
import adminMinigamesHub from './adminMinigamesHub';
import avatarCropper from './avatarCropper';
import bannerCropper from './bannerCropper';
import minigamesLeaderboard from './minigamesLeaderboard';
import minesweeperGame from './minesweeperGame';
import pongGame from './pongGame';
import snakeGame from './snakeGame';
import ticTacToeGame from './ticTacToeGame';
import wysiwygEditor from './wysiwygEditor';
import 'cropperjs/dist/cropper.css';

window.Alpine = Alpine;

Alpine.data('adminMinigamesHub', adminMinigamesHub);
Alpine.data('avatarCropper', avatarCropper);
Alpine.data('bannerCropper', bannerCropper);
Alpine.data('minigamesLeaderboard', minigamesLeaderboard);
Alpine.data('minesweeperGame', minesweeperGame);
Alpine.data('pongGame', pongGame);
Alpine.data('snakeGame', snakeGame);
Alpine.data('ticTacToeGame', ticTacToeGame);
Alpine.data('wysiwygEditor', wysiwygEditor);

Alpine.data('sidebarNav', () => ({
    open: false,

    init() {
        const saved = localStorage.getItem('sidebarOpen');

        if (saved !== null) {
            this.open = saved === 'true';
        } else {
            this.open = window.innerWidth >= 768;
        }
    },

    toggle() {
        this.open = !this.open;
        localStorage.setItem('sidebarOpen', this.open);
    },

    close() {
        this.open = false;
        localStorage.setItem('sidebarOpen', 'false');
    },

    closeOnMobile() {
        if (window.innerWidth < 768) {
            this.close();
        }
    },
}));

Alpine.data('terminalOverlay', () => ({
    isOpen: false,
    inputVal: '',
    logs: [
        'VERTEX OS v4.12.1-stable',
        'Initializing secure systems socket...',
        'Authentication bypassed: Guest access granted.',
        'Type "help" to display available systems protocols.',
        '',
    ],

    toggle() {
        this.isOpen = !this.isOpen;
    },

    close() {
        this.isOpen = false;
    },

    submitCommand(e) {
        e.preventDefault();
        const cleanCmd = this.inputVal.trim().toLowerCase();
        if (!cleanCmd) return;

        const routes = {
            home: '/',
            projects: '/projects',
            services: '/services',
            contact: '/contact',
        };

        let response = [];

        switch (cleanCmd) {
            case 'help':
                response = [
                    'Available Protocols:',
                    '  whoami     - Display credentials profile',
                    '  neofetch   - Query kernel & system assets',
                    '  skills     - Inventory technical competencies',
                    '  home       - Navigate to main lobby',
                    '  projects   - Navigate to schematics dashboard',
                    '  services   - Navigate to cost capabilities planner',
                    '  contact    - Launch contact form transmission',
                    '  exit       - Close systems socket interface',
                    '  clear      - Wipe buffer history',
                ];
                break;
            case 'whoami':
                response = [
                    'User: Guest_Network_Node',
                    'Role: Potential Enterprise Client',
                    'IP: 127.0.0.1 (Loopback Client Tunnel)',
                    'Session Token: ' + Math.random().toString(36).substring(2, 10).toUpperCase(),
                ];
                break;
            case 'neofetch':
                response = [
                    'VERTEX System Core',
                    '------------------------',
                    'SLA Uptime: 99.999% continuous',
                    'Active Kubernetes Nodes: 240 Nodes',
                    'Global Average Latency: 12.4ms',
                    'Security Compliance: SOC-2 Type II Verified',
                    'Framework Base: Laravel / Vite / Tailwind v4',
                ];
                break;
            case 'skills':
                response = [
                    'SYSTEM_CAPABILITIES_INVENTORY:',
                    '  [A] AWS, GCP, Cloudflare Edge Workers',
                    '  [B] Kubernetes clustering, Istio, Linkerd',
                    '  [C] Drizzle ORM, PostgreSQL sync, Redis caching',
                    '  [D] High-concurrency engines in Go, Rust, TypeScript',
                ];
                break;
            case 'clear':
                this.logs = [];
                this.inputVal = '';
                return;
            case 'exit':
                this.close();
                this.inputVal = '';
                return;
            default:
                if (routes[cleanCmd]) {
                    window.location.href = routes[cleanCmd];
                    response = [`Navigating to: ${cleanCmd.toUpperCase()}_SCREEN. Terminal socket remaining active...`];
                } else {
                    response = [
                        `Command not found: "${this.inputVal}".`,
                        'Type "help" to display available systems protocols.',
                    ];
                }
        }

        this.logs = [...this.logs, `guest_node $ ${this.inputVal}`, ...response, ''];
        this.inputVal = '';
        this.$nextTick(() => {
            const el = this.$refs.logContainer;
            if (el) el.scrollTop = el.scrollHeight;
        });
    },
}));

Alpine.start();
