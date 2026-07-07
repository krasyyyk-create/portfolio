<x-layouts.app title="DEV_ARCHITECT — Register">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        {{-- Left: hero + terminal --}}
        <div class="lg:col-span-5 space-y-8">
            <header class="space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 border border-white/10 rounded-full font-mono text-xs text-white/95 backdrop-blur-md">
                    <svg class="w-3.5 h-3.5 animate-pulse text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span>IDENTITY PROVISIONING // NEW OPERATOR ONBOARDING</span>
                </div>

                <h1 class="font-sans text-4xl md:text-5xl font-bold tracking-tight text-white leading-none">
                    Provision Your <span class="text-indigo-400">Secure Identity</span>.
                </h1>

                <p class="font-sans text-base md:text-lg text-white/70 leading-relaxed max-w-md">
                    Register a new operator account to access protected cloud topology schematics, deployment controls, and secure directory services.
                </p>
            </header>

            <div class="hidden lg:block glass-card rounded-2xl overflow-hidden shadow-2xl">
                <div class="bg-white/5 p-4 flex justify-between items-center border-b border-white/10 select-none">
                    <div class="flex gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-red-500/40 border border-red-500/20"></span>
                        <span class="w-3 h-3 rounded-full bg-yellow-500/40 border border-yellow-500/20"></span>
                        <span class="w-3 h-3 rounded-full bg-green-500/40 border border-green-500/20"></span>
                    </div>
                    <div class="font-mono text-[11px] text-white/50 flex items-center gap-2">
                        <svg class="w-3 h-3 text-indigo-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <span>provision_identity.sh</span>
                    </div>
                </div>
                <div class="p-5 font-mono text-xs space-y-3 bg-slate-950/45 text-white/90">
                    <p class="text-white/40 italic">// Identity provisioning subsystem online</p>
                    <p><span class="text-indigo-400">protocol:</span> <span class="text-white/70">OIDC / Session Token</span></p>
                    <p><span class="text-indigo-400">encryption:</span> <span class="text-white/70">bcrypt + TLS 1.3</span></p>
                    <p><span class="text-indigo-400">status:</span> <span class="text-emerald-400">AWAITING_OPERATOR_DATA</span></p>
                    <p class="text-white/40 pt-2 border-t border-white/10">&gt; Complete the form to initialize a new secure identity...</p>
                </div>
            </div>
        </div>

        {{-- Right: registration form --}}
        <div class="lg:col-span-7">
            <div class="glass-card-heavy border border-white/15 p-6 md:p-10 rounded-2xl relative hover:border-indigo-400/25 transition-all duration-300 shadow-2xl">
                <div class="absolute top-4 left-4 flex gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500/60"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500/30"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500/30"></div>
                </div>
                <div class="absolute top-4 right-8 font-mono text-xs text-white/40 select-none">
                    register_form.blade.php
                </div>

                <div class="mt-8 space-y-6">
                    <div class="space-y-1">
                        <h2 class="font-mono text-sm font-bold text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            <span>IDENTITY_PROVISIONING</span>
                        </h2>
                        <p class="font-sans text-xs text-white/50">Create a new operator account to establish secure access</p>
                    </div>

                    @if ($errors->any())
                        <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg font-mono text-xs text-red-400 space-y-1">
                            @foreach ($errors->all() as $error)
                                <p>&gt; {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <x-auth.google-button />

                    <div class="relative">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-white/10"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="bg-transparent px-3 font-mono text-[11px] text-white/40">OR_EMAIL_PROVISION</span>
                        </div>
                    </div>

                    <form action="{{ route('register') }}" method="POST" class="space-y-5">
                        @csrf

                        <div class="space-y-2">
                            <label class="font-sans font-medium text-xs text-white/60" for="name">OPERATOR_NAME</label>
                            <input
                                class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans placeholder:text-white/30 @error('name') border-red-500/60 @enderror"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="e.g. Systems Architect"
                                type="text"
                                required
                                autofocus
                            />
                            @error('name')
                                <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="font-sans font-medium text-xs text-white/60" for="email">EMAIL_ADDRESS</label>
                            <input
                                class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans placeholder:text-white/30 @error('email') border-red-500/60 @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="e.g. architect@dev.null"
                                type="email"
                                required
                            />
                            @error('email')
                                <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="font-sans font-medium text-xs text-white/60" for="password">PASSWORD_KEY</label>
                            <input
                                class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans placeholder:text-white/30 @error('password') border-red-500/60 @enderror"
                                id="password"
                                name="password"
                                placeholder="••••••••••••"
                                type="password"
                                required
                            />
                            @error('password')
                                <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="font-sans font-medium text-xs text-white/60" for="password_confirmation">CONFIRM_PASSWORD_KEY</label>
                            <input
                                class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans placeholder:text-white/30"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="••••••••••••"
                                type="password"
                                required
                            />
                        </div>

                        <button
                            class="w-full bg-indigo-500/85 hover:bg-indigo-500 border border-white/10 text-white font-sans font-semibold py-4 rounded-lg active:scale-[0.98] transition-all flex items-center justify-center gap-3 group cursor-pointer shadow-lg shadow-indigo-500/25"
                            type="submit"
                        >
                            <span>PROVISION IDENTITY</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </button>
                    </form>

                    <p class="font-mono text-[11px] text-white/40 text-center pt-2 border-t border-white/10">
                        <span class="text-indigo-400/80">&gt;</span> Already provisioned?
                        <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 transition-colors">Authenticate here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
