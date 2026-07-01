<!-- resources/views/components/contact-form.blade.php -->
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
</div>