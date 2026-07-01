import React from 'react';
import { ArrowUp } from 'lucide-react';

interface FooterProps {
  setActiveScreen: (screen: 'home' | 'projects' | 'services' | 'contact') => void;
}

export default function Footer({ setActiveScreen }: FooterProps) {
  const scrollToTop = (e: React.MouseEvent) => {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  return (
    <footer className="w-full mt-auto bg-white/5 border-t border-white/10 backdrop-blur-md">
      <div className="max-w-[1200px] mx-auto px-6 md:px-12 py-12 flex flex-col md:flex-row justify-between items-center gap-6">
        {/* Brand name */}
        <div 
          onClick={() => {
            setActiveScreen('home');
            window.scrollTo({ top: 0, behavior: 'smooth' });
          }}
          className="font-mono text-xl font-bold text-white cursor-pointer hover:text-indigo-400 transition-colors select-none"
        >
          DEV_ARCHITECT
        </div>

        {/* Copyright notice */}
        <p className="font-sans text-sm text-white/60 text-center md:text-left">
          &copy; 2024 DEV_ARCHITECT. Built with Systemic Precision.
        </p>

        {/* Links & Back to Top */}
        <div className="flex flex-wrap justify-center items-center gap-6">
          <a 
            href="https://github.com" 
            target="_blank" 
            rel="noreferrer"
            className="font-mono text-xs text-white/60 hover:text-white transition-colors focus:outline-none focus:underline"
          >
            GitHub
          </a>
          <a 
            href="https://linkedin.com" 
            target="_blank" 
            rel="noreferrer"
            className="font-mono text-xs text-white/60 hover:text-white transition-colors focus:outline-none focus:underline"
          >
            LinkedIn
          </a>
          <a 
            href="https://twitter.com" 
            target="_blank" 
            rel="noreferrer"
            className="font-mono text-xs text-white/60 hover:text-white transition-colors focus:outline-none focus:underline"
          >
            Twitter
          </a>
          <button 
            onClick={scrollToTop}
            className="font-mono text-xs text-white/60 hover:text-indigo-400 transition-colors focus:outline-none flex items-center gap-1 cursor-pointer"
          >
            <span>Back to Top</span>
            <ArrowUp className="w-3 h-3" />
          </button>
        </div>
      </div>
    </footer>
  );
}
