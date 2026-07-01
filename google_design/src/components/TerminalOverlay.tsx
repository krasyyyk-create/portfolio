import React, { useState, useEffect, useRef } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { Terminal, X, Play, ShieldAlert, Cpu } from 'lucide-react';

interface TerminalOverlayProps {
  isOpen: boolean;
  onClose: () => void;
  setActiveScreen: (screen: 'home' | 'projects' | 'services' | 'contact') => void;
}

export default function TerminalOverlay({ isOpen, onClose, setActiveScreen }: TerminalOverlayProps) {
  const [inputVal, setInputVal] = useState('');
  const [logs, setLogs] = useState<string[]>([
    'DEV_ARCHITECT OS v4.12.1-stable',
    'Initializing secure systems socket...',
    'Authentication bypassed: Guest access granted.',
    'Type "help" to display available systems protocols.',
    ''
  ]);
  const containerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (containerRef.current) {
      containerRef.current.scrollTop = containerRef.current.scrollHeight;
    }
  }, [logs, isOpen]);

  const handleCommandSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const cleanCmd = inputVal.trim().toLowerCase();
    if (!cleanCmd) return;

    let response: string[] = [];

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
          '  clear      - Wipe buffer history'
        ];
        break;
      case 'whoami':
        response = [
          'User: Guest_Network_Node',
          'Role: Potential Enterprise Client',
          'IP: 127.0.0.1 (Loopback Client Tunnel)',
          'Session Token: ' + Math.random().toString(36).substring(2, 10).toUpperCase()
        ];
        break;
      case 'neofetch':
        response = [
          'DEV_ARCHITECT System Core',
          '------------------------',
          'SLA Uptime: 99.999% continuous',
          'Active Kubernetes Nodes: 240 Nodes',
          'Global Average Latency: 12.4ms',
          'Security Compliance: SOC-2 Type II Verified',
          'Framework Base: React 19 / Vite 6 / Tailwind v4'
        ];
        break;
      case 'skills':
        response = [
          'SYSTEM_CAPABILITIES_INVENTORY:',
          '  [A] AWS, GCP, Cloudflare Edge Workers',
          '  [B] Kubernetes clustering, Istio, Linkerd',
          '  [C] Drizzle ORM, PostgreSQL sync, Redis caching',
          '  [D] High-concurrency engines in Go, Rust, TypeScript'
        ];
        break;
      case 'home':
        setActiveScreen('home');
        response = ['Navigating to: HOME_SCREEN. Terminal socket remaining active...'];
        break;
      case 'projects':
        setActiveScreen('projects');
        response = ['Navigating to: PROJECTS_SCHEMATICS. Terminal socket remaining active...'];
        break;
      case 'services':
        setActiveScreen('services');
        response = ['Navigating to: SERVICES_CAPABILITIES. Terminal socket remaining active...'];
        break;
      case 'contact':
        setActiveScreen('contact');
        response = ['Navigating to: CONTACT_TRANSMISSION. Terminal socket remaining active...'];
        break;
      case 'clear':
        setLogs([]);
        setInputVal('');
        return;
      case 'exit':
        onClose();
        setInputVal('');
        return;
      default:
        response = [
          `Command not found: "${inputVal}".`,
          'Type "help" to display available systems protocols.'
        ];
    }

    setLogs(prev => [...prev, `guest_node $ ${inputVal}`, ...response, '']);
    setInputVal('');
  };

  return (
    <AnimatePresence>
      {isOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          {/* Backdrop */}
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={onClose}
            className="absolute inset-0 bg-slate-950/65 backdrop-blur-md"
          />

          {/* Terminal Box */}
          <motion.div
            initial={{ opacity: 0, scale: 0.95, y: 15 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.95, y: 15 }}
            className="relative w-full max-w-3xl glass-card-heavy border border-white/15 rounded-2xl overflow-hidden shadow-2xl flex flex-col h-[70vh]"
          >
            {/* Header / Tabs */}
            <div className="bg-white/5 p-4 flex justify-between items-center border-b border-white/10 select-none">
              <div className="flex gap-1.5">
                <button
                  onClick={onClose}
                  className="w-3 h-3 rounded-full bg-red-500/60 cursor-pointer"
                  title="Close Terminal"
                ></button>
                <span className="w-3 h-3 rounded-full bg-yellow-500/30"></span>
                <span className="w-3 h-3 rounded-full bg-green-500/30"></span>
              </div>
              <div className="font-mono text-xs text-white/40 flex items-center gap-1.5">
                <Terminal className="w-4 h-4 text-indigo-400 animate-pulse" />
                <span>root@dev_architect:~/gateway_node</span>
              </div>
            </div>

            {/* Logs Screen */}
            <div
              ref={containerRef}
              className="flex-grow p-6 overflow-y-auto font-mono text-xs text-indigo-200 space-y-2 bg-slate-950/40 backdrop-blur-md selection:bg-indigo-500/30 selection:text-white select-text"
            >
              {logs.map((log, index) => (
                <p key={index} className="leading-relaxed whitespace-pre-wrap select-text">
                  {log.startsWith('guest_node $') ? (
                    <span className="text-white font-bold">{log}</span>
                  ) : log.startsWith('User:') || log.startsWith('Role:') || log.startsWith('SLA') ? (
                    <span className="text-indigo-300 font-medium">{log}</span>
                  ) : log.startsWith('Command not found') ? (
                    <span className="text-red-400">{log}</span>
                  ) : (
                    <span>{log}</span>
                  )}
                </p>
              ))}
            </div>

            {/* Input Form */}
            <form
              onSubmit={handleCommandSubmit}
              className="p-4 bg-white/5 border-t border-white/10 flex items-center gap-3"
            >
              <span className="font-mono text-xs text-indigo-400 font-bold select-none">guest_node $</span>
              <input
                type="text"
                value={inputVal}
                onChange={(e) => setInputVal(e.target.value)}
                autoFocus
                placeholder="Type systems command (e.g., 'help', 'skills', 'projects')..."
                className="flex-grow bg-transparent border-none text-white font-mono text-xs focus:outline-none placeholder:text-white/20"
              />
              <span className="font-mono text-[10px] text-white/40 select-none">SOCKET_CONNECTED</span>
            </form>
          </motion.div>
        </div>
      )}
    </AnimatePresence>
  );
}
