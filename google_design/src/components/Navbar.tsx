import React from 'react';
import { Terminal, Cpu } from 'lucide-react';

interface NavbarProps {
  activeScreen: 'home' | 'projects' | 'services' | 'contact';
  setActiveScreen: (screen: 'home' | 'projects' | 'services' | 'contact') => void;
  onTerminalToggle: () => void;
}

export default function Navbar({ activeScreen, setActiveScreen, onTerminalToggle }: NavbarProps) {
  return (
    <nav className="fixed top-0 w-full z-40 bg-white/5 backdrop-blur-xl border-b border-white/10 shadow-lg shadow-black/5">
      <div className="flex justify-between items-center max-w-[1200px] mx-auto px-6 md:px-12 h-20">
        {/* Logo */}
        <div 
          onClick={() => setActiveScreen('home')}
          className="font-sans text-xl md:text-2xl font-bold tracking-tight text-white cursor-pointer flex items-center gap-2.5 group select-none"
        >
          <div className="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 font-bold group-hover:rotate-12 transition-transform duration-300">D</div>
          <span>DEV_ARCHITECT</span>
        </div>

        {/* Center Links */}
        <div className="hidden md:flex items-center gap-8">
          {(['home', 'projects', 'services', 'contact'] as const).map((screen) => (
            <button
              key={screen}
              onClick={() => setActiveScreen(screen)}
              className={`font-mono text-sm capitalize transition-all cursor-pointer relative py-1 ${
                activeScreen === screen
                  ? 'text-white font-bold'
                  : 'text-white/60 hover:text-white transition-colors'
              }`}
            >
              {screen}
              {activeScreen === screen && (
                <span className="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-400 rounded-full" />
              )}
            </button>
          ))}
        </div>

        {/* Right side controls */}
        <div className="flex items-center gap-4">
          {/* Terminal Toggle Button */}
          <button
            onClick={onTerminalToggle}
            className="p-2.5 text-white/80 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 transition-all duration-300 rounded-lg active:scale-95 cursor-pointer flex items-center justify-center"
            title="Toggle Systems Terminal"
          >
            <Terminal className="w-5 h-5" />
          </button>

          {/* Hire Me CTA */}
          <button
            onClick={() => setActiveScreen('contact')}
            className="hidden md:block bg-indigo-500/80 hover:bg-indigo-500 text-white border border-white/10 font-sans text-sm px-6 py-2 rounded-lg font-semibold active:scale-95 transition-all shadow-md shadow-indigo-500/10 hover:shadow-indigo-500/25 cursor-pointer"
          >
            Hire Me
          </button>
        </div>
      </div>
    </nav>
  );
}
