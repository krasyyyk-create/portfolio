import React, { useState } from 'react';
import { Terminal, Copy, Check, FileCode, Server, HelpCircle, Layers, X } from 'lucide-react';

interface BladeCompanionProps {
  activeScreen: 'home' | 'projects' | 'services' | 'contact';
}

export default function BladeCompanion({ activeScreen }: BladeCompanionProps) {
  const [isOpen, setIsOpen] = useState(false);
  const [activeTab, setActiveTab] = useState<'layout' | 'component' | 'integration'>('component');
  const [copied, setCopied] = useState(false);

  const handleCopy = (text: string) => {
    navigator.clipboard.writeText(text);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  const layoutCode = `<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'VERTEX' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (Vite or CDN) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom Theme Variables -->
    <style>
        :root {
            --color-background: #0b1326;
            --color-on-background: #dae2fd;
        }
        .custom-glow {
            box-shadow: 0 0 15px rgba(0, 240, 255, 0.3);
        }
        .input-focus-glow:focus {
            box-shadow: inset 0 0 8px rgba(0, 240, 255, 0.2), 0 0 10px rgba(0, 240, 255, 0.15);
        }
        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23849495'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1.5em;
        }
    </style>
</head>
<body class="bg-[#0b1326] text-[#dae2fd] min-h-screen flex flex-col antialiased selection:bg-[#00f0ff] selection:text-[#00363a]">
    <!-- Navbar Component -->
    <x-navbar />

    <!-- Main Content -->
    <main class="flex-grow pt-32 pb-24 px-4 md:px-12 max-w-[1200px] mx-auto w-full">
        {{ $slot }}
    </main>

    <!-- Footer Component -->
    <x-footer />
</body>
</html>`;

  const contactBladeCode = `<!-- resources/views/components/contact-form.blade.php -->
<div class="grid grid-cols-1 md:grid-cols-12 gap-12">
    <!-- Left Side: Messaging -->
    <div class="md:col-span-5 space-y-8">
        <header class="space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#222a3d] border border-[#3b494b] rounded-sm">
                <span class="w-2 h-2 bg-[#00f0ff] rounded-full animate-pulse"></span>
                <span class="font-mono text-[13px] text-[#00dbe9] uppercase tracking-widest">Available for hire</span>
            </div>
            <h1 class="font-mono text-4xl md:text-5xl font-bold tracking-tighter text-[#dae2fd] leading-tight">
                Architecting Your <span class="text-[#00f0ff]">Next Big Idea.</span>
            </h1>
            <p class="font-sans text-lg text-[#b9cacb] leading-relaxed max-w-md">
                Whether you're looking for a technical audit, a scalable cloud architecture, or a full-stack engineering partner, let's start the conversation. 
            </p>
        </header>

        <div class="hidden md:block p-4 bg-[#060e20] border border-[#3b494b] rounded-lg relative overflow-hidden group">
            <div class="absolute top-2 right-2 flex gap-1.5">
                <div class="w-2.5 h-2.5 rounded-full bg-[#ffb4ab]/40"></div>
                <div class="w-2.5 h-2.5 rounded-full bg-[#3131c0]/40"></div>
                <div class="w-2.5 h-2.5 rounded-full bg-[#00f0ff]/40"></div>
            </div>
            <div class="font-mono text-[13px] space-y-4 p-2">
                <p class="text-[#849495] italic">// Contact Metadata</p>
                <div class="space-y-2">
                    <div class="flex gap-4">
                        <span class="text-[#c0c1ff]">email:</span>
                        <a class="text-[#dbfcff] hover:underline transition-all" href="mailto:architect@dev.null">architect@dev.null</a>
                    </div>
                    <div class="flex gap-4">
                        <span class="text-[#c0c1ff]">location:</span>
                        <span class="text-[#b9cacb]">Remote / Global</span>
                    </div>
                    <div class="flex gap-4">
                        <span class="text-[#c0c1ff]">socials:</span>
                        <div class="flex gap-3">
                            <a class="text-[#b9cacb] hover:text-[#dbfcff] transition-colors" href="#">GitHub</a>
                            <span class="text-[#849495]">/</span>
                            <a class="text-[#b9cacb] hover:text-[#dbfcff] transition-colors" href="#">LinkedIn</a>
                            <span class="text-[#849495]">/</span>
                            <a class="text-[#b9cacb] hover:text-[#dbfcff] transition-colors" href="#">Twitter</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Decorative Map -->
        <div class="relative w-full h-48 rounded-lg overflow-hidden border border-[#3b494b] md:block hidden">
            <div class="w-full h-full bg-cover bg-center grayscale contrast-125 opacity-40" 
                 style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDjyPpuyf9DPkTRZyUa-bdin_E3Q_zlzT_E_e6z3jH69M2nmWkVsrdFVxf8VSdZDkqT8DPRJ9hjOPM10Y2_IxJx4IEzovfSVUZMDcGmFSlCwZiEV_XKogtW3qY6tQAZAu5z199LSBtgDXRXf4HNKnCQqvUdDD8JWRYNKPMFsbaqmjb95uuyoCqMhNWt291OmCCqCqY03WBRNcZ25-ROg_h7PdmHqrbmYS8-pdyjQSzc66kCY-QapI4LF6QwrixiwJ4iiTUJk3XyrGNm')">
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#0b1326] to-transparent"></div>
            <div class="absolute bottom-4 left-4 font-mono text-[13px] text-[#00f0ff]">
                > LOCATE_GLOBAL_NODE: ACTIVE
            </div>
        </div>
    </div>

    <!-- Right Side: Contact Form -->
    <div class="md:col-span-7">
        <div class="bg-[#171f33]/40 backdrop-blur-xl border border-[#3b494b] p-8 md:p-12 rounded-xl relative">
            <div class="absolute top-4 left-4 flex gap-2">
                <div class="w-3 h-3 rounded-full border border-[#3b494b]"></div>
                <div class="w-3 h-3 rounded-full border border-[#3b494b]"></div>
                <div class="w-3 h-3 rounded-full border border-[#3b494b]"></div>
            </div>
            <div class="absolute top-4 right-8 font-mono text-[13px] text-[#849495] select-none">
                contact_form.blade.php
            </div>

            <form action="{{ route('contact.submit') }}" method="POST" class="mt-8 space-y-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="font-mono text-[14px] text-[#b9cacb]" for="name">FULL_NAME</label>
                        <input class="w-full bg-[#060e20] border border-[#3b494b] text-[#dae2fd] px-4 py-3 rounded-sm focus:outline-none focus:border-[#00f0ff] input-focus-glow transition-all font-sans placeholder:text-[#849495]/50" 
                               id="name" name="name" placeholder="e.g. Alan Turing" required type="text" value="{{ old('name') }}">
                    </div>
                    <div class="space-y-2">
                        <label class="font-mono text-[14px] text-[#b9cacb]" for="email">EMAIL_ADDRESS</label>
                        <input class="w-full bg-[#060e20] border border-[#3b494b] text-[#dae2fd] px-4 py-3 rounded-sm focus:outline-none focus:border-[#00f0ff] input-focus-glow transition-all font-sans placeholder:text-[#849495]/50" 
                               id="email" name="email" placeholder="e.g. alan@bletchley.com" required type="email" value="{{ old('email') }}">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="font-mono text-[14px] text-[#b9cacb]" for="project-type">PROJECT_TYPE</label>
                    <select class="w-full bg-[#060e20] border border-[#3b494b] text-[#dae2fd] px-4 py-3 rounded-sm focus:outline-none focus:border-[#00f0ff] input-focus-glow transition-all font-sans cursor-pointer" 
                            id="project-type" name="project_type">
                        <option value="architecture">System Architecture</option>
                        <option value="fullstack">Full-Stack Development</option>
                        <option value="consultation">Technical Consultation</option>
                        <option value="audit">Code & Security Audit</option>
                        <option value="other">Other / Custom</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="font-mono text-[14px] text-[#b9cacb]" for="message">MESSAGE_PAYLOAD</label>
                    <textarea class="w-full bg-[#060e20] border border-[#3b494b] text-[#dae2fd] px-4 py-3 rounded-sm focus:outline-none focus:border-[#00f0ff] input-focus-glow transition-all font-sans placeholder:text-[#849495]/50 resize-none" 
                              id="message" name="message" placeholder="Describe your project scope and objectives..." required rows="5">{{ old('message') }}</textarea>
                </div>

                <button class="w-full bg-[#00f0ff] text-[#00363a] font-mono text-base py-4 rounded-sm font-bold custom-glow active:scale-[0.98] transition-all flex items-center justify-center gap-3 group" 
                        type="submit">
                    <span>INITIALIZE_CONTACT</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>`;

  const integrationHelp = `// Laravel Integration Guide:
1. Copy the "Layout Code" into: resources/views/layouts/app.blade.php
2. Copy the "Component Blade Code" into: resources/views/contact.blade.php
3. Define the Route in routes/web.php:
   Route::get('/contact', function () {
       return view('contact');
   })->name('contact');

   Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

4. Implement validation in your Controller:
   public function submit(Request $request) {
       $validated = $request->validate([
           'name' => 'required|string|max:255',
           'email' => 'required|email|max:255',
           'project_type' => 'required|string',
           'message' => 'required|string',
       ]);
       
       // Handle message transmission...
       return back()->with('success', 'REQUEST_SENT: Payload validated and logged.');
   }`;

  const getActiveCode = () => {
    switch (activeTab) {
      case 'layout':
        return layoutCode;
      case 'component':
        return contactBladeCode;
      case 'integration':
        return integrationHelp;
      default:
        return contactBladeCode;
    }
  };

  return (
    <>
      {/* Floating Action Button */}
      <button
        onClick={() => setIsOpen(true)}
        className="fixed bottom-6 right-6 z-50 bg-indigo-500 hover:bg-indigo-600 text-white px-5 py-3.5 rounded-full font-sans text-sm font-semibold flex items-center gap-2 shadow-lg shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all cursor-pointer group border border-white/10"
      >
        <Server className="w-4 h-4 animate-bounce text-indigo-200" />
        <span>Laravel Bridge</span>
      </button>

      {/* Sidebar Panel */}
      {isOpen && (
        <div className="fixed inset-0 z-50 flex justify-end">
          {/* Backdrop */}
          <div
            onClick={() => setIsOpen(false)}
            className="absolute inset-0 bg-slate-950/65 backdrop-blur-md transition-opacity"
          />

          {/* Sidebar */}
          <div className="relative w-full max-w-2xl glass-card-heavy h-full flex flex-col shadow-2xl border-l border-white/15">
            {/* Header */}
            <div className="p-6 border-b border-white/10 flex justify-between items-center bg-white/5">
              <div className="flex items-center gap-3">
                <div className="p-2.5 rounded-lg bg-indigo-500/10 border border-indigo-500/20">
                  <Terminal className="w-5 h-5 text-indigo-400" />
                </div>
                <div>
                  <h2 className="font-mono text-base font-bold text-white">Laravel Blade Bridge</h2>
                  <p className="font-sans text-xs text-white/50">Ready-to-use markup & Tailwind configurations</p>
                </div>
              </div>
              <button
                onClick={() => setIsOpen(false)}
                className="p-1.5 rounded-lg border border-white/10 text-white/60 hover:text-white hover:bg-white/5 transition-colors cursor-pointer"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            {/* Navigation Tabs */}
            <div className="flex bg-white/5 border-b border-white/10 px-4">
              <button
                onClick={() => setActiveTab('component')}
                className={`py-3 px-4 font-mono text-xs flex items-center gap-2 border-b-2 transition-all cursor-pointer ${
                  activeTab === 'component'
                    ? 'border-indigo-400 text-white font-bold'
                    : 'border-transparent text-white/50 hover:text-white'
                }`}
              >
                <FileCode className="w-3.5 h-3.5 text-indigo-300" />
                <span>Contact View</span>
              </button>
              <button
                onClick={() => setActiveTab('layout')}
                className={`py-3 px-4 font-mono text-xs flex items-center gap-2 border-b-2 transition-all cursor-pointer ${
                  activeTab === 'layout'
                    ? 'border-indigo-400 text-white font-bold'
                    : 'border-transparent text-white/50 hover:text-white'
                }`}
              >
                <Layers className="w-3.5 h-3.5 text-indigo-300" />
                <span>App Layout</span>
              </button>
              <button
                onClick={() => setActiveTab('integration')}
                className={`py-3 px-4 font-mono text-xs flex items-center gap-2 border-b-2 transition-all cursor-pointer ${
                  activeTab === 'integration'
                    ? 'border-indigo-400 text-white font-bold'
                    : 'border-transparent text-white/50 hover:text-white'
                }`}
              >
                <HelpCircle className="w-3.5 h-3.5 text-indigo-300" />
                <span>Integration Guide</span>
              </button>
            </div>

            {/* Code Output */}
            <div className="flex-grow p-6 overflow-y-auto bg-transparent font-mono text-xs text-white select-text">
              <div className="mb-4 flex justify-between items-center">
                <span className="text-white/40 italic">
                  {activeTab === 'layout' && '// app.blade.php'}
                  {activeTab === 'component' && '// contact_form.blade.php'}
                  {activeTab === 'integration' && '// laravel_routing.php'}
                </span>
                <button
                  onClick={() => handleCopy(getActiveCode())}
                  className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-white/10 hover:border-white/20 bg-white/5 text-white/60 hover:text-white transition-all cursor-pointer"
                >
                  {copied ? (
                    <>
                      <Check className="w-3.5 h-3.5 text-emerald-400" />
                      <span className="text-emerald-400 font-semibold">Copied!</span>
                    </>
                  ) : (
                    <>
                      <Copy className="w-3.5 h-3.5" />
                      <span>Copy Code</span>
                    </>
                  )}
                </button>
              </div>

              <pre className="p-5 bg-slate-950/40 rounded-2xl border border-white/10 overflow-x-auto text-emerald-400 whitespace-pre-wrap leading-relaxed max-h-[70vh] backdrop-blur-md">
                <code>{getActiveCode()}</code>
              </pre>
            </div>

            {/* Footer explanation */}
            <div className="p-6 border-t border-white/10 bg-white/5 text-xs text-white/60 leading-relaxed">
              <p>
                <strong>Compatibility:</strong> This markup is styled using pure Tailwind CSS classes configured in alignment with the Frosted Glass theme. Simply drop these files into your Laravel project directory, boot up <code>npm run dev</code> or compile assets, and it will render perfectly inside your Blade router.
              </p>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
