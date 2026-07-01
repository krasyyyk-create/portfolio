import React, { useState } from 'react';
import { AnimatePresence, motion } from 'motion/react';
import Navbar from './components/Navbar';
import Footer from './components/Footer';
import HomeView from './components/HomeView';
import ProjectsView from './components/ProjectsView';
import ServicesView from './components/ServicesView';
import ContactView from './components/ContactView';
import TerminalOverlay from './components/TerminalOverlay';
import BladeCompanion from './components/BladeCompanion';

export default function App() {
  const [activeScreen, setActiveScreen] = useState<'home' | 'projects' | 'services' | 'contact'>('contact');
  const [isTerminalOpen, setIsTerminalOpen] = useState(false);

  return (
    <div className="bg-transparent text-white min-h-screen flex flex-col selection:bg-indigo-500/30 selection:text-white antialiased">
      {/* Floating Header */}
      <Navbar 
        activeScreen={activeScreen} 
        setActiveScreen={setActiveScreen} 
        onTerminalToggle={() => setIsTerminalOpen(prev => !prev)} 
      />

      {/* Main Content Layout */}
      <main className="flex-grow pt-32 pb-24 px-6 md:px-12 max-w-[1200px] mx-auto w-full">
        <AnimatePresence mode="wait">
          {activeScreen === 'home' && (
            <HomeView setActiveScreen={setActiveScreen} />
          )}
          {activeScreen === 'projects' && (
            <ProjectsView />
          )}
          {activeScreen === 'services' && (
            <ServicesView />
          )}
          {activeScreen === 'contact' && (
            <ContactView />
          )}
        </AnimatePresence>
      </main>

      {/* Footer copyright and socials */}
      <Footer setActiveScreen={setActiveScreen} />

      {/* Interactive Terminal Overlay CLI Socket */}
      <TerminalOverlay 
        isOpen={isTerminalOpen} 
        onClose={() => setIsTerminalOpen(false)} 
        setActiveScreen={setActiveScreen} 
      />

      {/* Laravel Blade Bridge Export Utility Panel */}
      <BladeCompanion activeScreen={activeScreen} />
    </div>
  );
}
