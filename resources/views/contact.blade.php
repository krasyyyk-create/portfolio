<x-layouts.app title="VERTEX — Contact">
    <div
        x-data="{
            formStatus: {{ session('success') ? "'success'" : "'idle'" }},
            submittedData: {
                name: {{ Js::from(old('name', '')) }},
                email: {{ Js::from(old('email', '')) }},
                projectType: {{ Js::from(old('project_type', 'architecture')) }},
            }
        }"
        class="grid grid-cols-1 md:grid-cols-12 gap-12 items-start"
    >
        <div class="md:col-span-5 space-y-8">
            <header class="space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 border border-white/20 text-white font-semibold backdrop-blur-md rounded-full">
                    <span class="w-2.5 h-2.5 bg-indigo-400 rounded-full animate-pulse"></span>
                    <span class="font-mono text-[10px] text-indigo-200 uppercase tracking-widest font-bold">Available for hire</span>
                </div>

                <h1 class="font-sans text-4xl md:text-5xl font-bold tracking-tighter text-white leading-tight">
                    Architecting Your <span class="text-indigo-400">Next Big Idea.</span>
                </h1>

                <p class="font-sans text-base md:text-lg text-white/70 max-w-md leading-relaxed">
                    Whether you're looking for a technical audit, a scalable cloud architecture, or a full-stack engineering partner, let's start the conversation.
                </p>
            </header>

            <div class="hidden md:block p-5 glass-card border border-white/10 rounded-2xl relative overflow-hidden group hover:border-indigo-400/40 transition-colors duration-300">
                <div class="absolute top-4 right-4 flex gap-1.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-red-500/60"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-yellow-500/30"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-green-500/30"></div>
                </div>
                <div class="font-mono text-xs space-y-4 p-2">
                    <p class="text-white/40 italic">// Contact Metadata</p>
                    <div class="space-y-3">
                        <div class="flex gap-4">
                            <span class="text-indigo-400 select-none">email:</span>
                            <a class="text-indigo-300 hover:text-white hover:underline transition-all" href="mailto:{{ config('services.contact.email') }}">{{ config('services.contact.email') }}</a>
                        </div>
                        <div class="flex gap-4">
                            <span class="text-indigo-400 select-none">location:</span>
                            <span class="text-white/80">Remote / Global</span>
                        </div>
                        <div class="flex gap-4">
                            <span class="text-indigo-400 select-none">socials:</span>
                            <div class="flex gap-3">
                                <a class="text-white/60 hover:text-white transition-colors font-sans" href="#github">GitHub</a>
                                <span class="text-white/20">/</span>
                                <a class="text-white/60 hover:text-white transition-colors font-sans" href="#linkedin">LinkedIn</a>
                                <span class="text-white/20">/</span>
                                <a class="text-white/60 hover:text-white transition-colors font-sans" href="#twitter">Twitter</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative w-full h-48 rounded-2xl overflow-hidden border border-white/10 md:block hidden">
                <div
                    class="w-full h-full bg-cover bg-center grayscale contrast-125 opacity-40 hover:scale-105 transition-transform duration-500"
                    style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDjyPpuyf9DPkTRZyUa-bdin_E3Q_zlzT_E_e6z3jH69M2nmWkVsrdFVxf8VSdZDkqT8DPRJ9hjOPM10Y2_IxJx4IEzovfSVUZMDcGmFSlCwZiEV_XKogtW3qY6tQAZAu5z199LSBtgDXRXf4HNKnCQqvUdDD8JWRYNKPMFsbaqmjb95uuyoCqMhNWt291OmCCqCqY03WBRNcZ25-ROg_h7PdmHqrbmYS8-pdyjQSzc66kCY-QapI4LF6QwrixiwJ4iiTUJk3XyrGNm')"
                ></div>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-4 left-4 font-mono text-xs text-indigo-400 flex items-center gap-2">
                    <span class="w-2 h-2 bg-indigo-400 rounded-full animate-ping"></span>
                    <span>&gt; LOCATE_GLOBAL_NODE: ACTIVE</span>
                </div>
            </div>
        </div>

        <div class="md:col-span-7">
            <div class="glass-card-heavy border border-white/15 p-6 md:p-10 rounded-2xl relative hover:border-indigo-400/25 transition-all duration-300 shadow-2xl">
                <div class="absolute top-4 left-4 flex gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500/60"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500/30"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500/30"></div>
                </div>
                <div class="absolute top-4 right-8 font-mono text-xs text-white/40 select-none">
                    contact_form.blade.php
                </div>

                <div x-show="formStatus === 'idle'" class="mt-8 space-y-6">
                    @if ($errors->any())
                        <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg font-mono text-xs text-red-400 space-y-1">
                            @foreach ($errors->all() as $error)
                                <p>&gt; {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form
                        action="{{ route('contact.submit') }}"
                        method="POST"
                        class="space-y-6"
                        @submit="submittedData = { name: $refs.nameInput.value, email: $refs.emailInput.value, projectType: $refs.projectTypeSelect.value }"
                    >
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="font-sans font-medium text-xs text-white/60" for="name">FULL_NAME</label>
                                <input
                                    x-ref="nameInput"
                                    class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans placeholder:text-white/30 @error('name') border-red-500/60 @enderror"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="e.g. Alan Turing"
                                    type="text"
                                    required
                                />
                                @error('name')
                                    <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="font-sans font-medium text-xs text-white/60" for="email">EMAIL_ADDRESS</label>
                                <input
                                    x-ref="emailInput"
                                    class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans placeholder:text-white/30 @error('email') border-red-500/60 @enderror"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="e.g. alan@bletchley.com"
                                    type="email"
                                    required
                                />
                                @error('email')
                                    <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="font-sans font-medium text-xs text-white/60" for="project-type">PROJECT_TYPE</label>
                            <select
                                x-ref="projectTypeSelect"
                                class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans cursor-pointer"
                                id="project-type"
                                name="project_type"
                            >
                                @foreach (['architecture' => 'System Architecture', 'fullstack' => 'Full-Stack Development', 'consultation' => 'Technical Consultation', 'audit' => 'Code & Security Audit', 'other' => 'Other / Custom'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('project_type', 'architecture') === $value) class="bg-slate-900 text-white">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="font-sans font-medium text-xs text-white/60" for="message">MESSAGE_PAYLOAD</label>
                            <textarea
                                class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans placeholder:text-white/30 resize-none @error('message') border-red-500/60 @enderror"
                                id="message"
                                name="message"
                                placeholder="Describe your project scope and objectives..."
                                rows="5"
                                required
                            >{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            class="w-full bg-indigo-500/85 hover:bg-indigo-500 border border-white/10 text-white font-sans font-semibold py-4 rounded-lg active:scale-[0.98] transition-all flex items-center justify-center gap-3 group cursor-pointer shadow-lg shadow-indigo-500/25"
                            type="submit"
                        >
                            <span>INITIALIZE_CONTACT</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>
                    </form>
                </div>

                <div x-show="formStatus === 'success'" x-cloak class="mt-8 space-y-6 text-center py-6">
                    <div class="inline-flex items-center justify-center p-4 bg-emerald-500/10 rounded-full border border-emerald-500/30">
                        <svg class="w-12 h-12 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <h3 class="font-mono text-lg font-bold text-white">CONTACT_INITIALIZATION: SUCCESS</h3>
                        <p class="font-sans text-sm text-white/70 max-w-sm mx-auto">
                            {{ session('success', 'Payload received and validated. Our architect will review the system metrics and respond within 24 hours.') }}
                        </p>
                    </div>
                    <div class="text-left bg-slate-950/40 border border-white/10 p-4 rounded-xl font-mono text-[11px] text-white/70 space-y-1">
                        <span class="text-white/40 italic">// Transmitted Payload Log</span>
                        <p><span class="text-indigo-400">name:</span> "<span x-text="submittedData.name || '{{ old('name') }}'"></span>"</p>
                        <p><span class="text-indigo-400">email:</span> "<span x-text="submittedData.email || '{{ old('email') }}'"></span>"</p>
                        <p><span class="text-indigo-400">type:</span> "<span x-text="submittedData.projectType || '{{ old('project_type') }}'"></span>"</p>
                        <p><span class="text-indigo-400">status:</span> "QUEUED_FOR_REVIEW"</p>
                    </div>
                    <a href="{{ route('contact.index') }}" class="inline-block px-6 py-3 bg-white/5 border border-white/10 rounded-lg font-sans text-xs text-white hover:bg-white/10 hover:border-white/20 transition-all">
                        &gt; Send Another Message
                    </a>
                </div>
            </div>
        </div>

        <div class="md:hidden mt-8 pt-8 border-t border-white/10 space-y-6 col-span-full">
            <div class="space-y-1">
                <p class="font-mono text-xs text-indigo-400 uppercase tracking-wider">Direct Channel</p>
                <a class="font-mono text-xl font-semibold text-indigo-300 hover:text-white hover:underline" href="mailto:{{ config('services.contact.email') }}">{{ config('services.contact.email') }}</a>
            </div>
            <div class="flex gap-6">
                <a class="font-sans text-sm text-white/60 hover:text-white" href="#github">GitHub</a>
                <a class="font-sans text-sm text-white/60 hover:text-white" href="#linkedin">LinkedIn</a>
                <a class="font-sans text-sm text-white/60 hover:text-white" href="#twitter">Twitter</a>
            </div>
        </div>
    </div>
</x-layouts.app>
