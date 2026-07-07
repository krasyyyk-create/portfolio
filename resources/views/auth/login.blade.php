<x-layouts.app title="DEV_ARCHITECT — Login">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        {{-- Left: hero + terminal --}}
        <div class="lg:col-span-5 space-y-8">
            <header class="space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 border border-white/10 rounded-full font-mono text-xs text-white/95 backdrop-blur-md">
                    <svg class="w-3.5 h-3.5 animate-pulse text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>SECURE ACCESS // AUTHENTICATION GATEWAY</span>
                </div>

                <h1 class="font-sans text-4xl md:text-5xl font-bold tracking-tight text-white leading-none">
                    Authenticate to <span class="text-indigo-400">Secure Directories</span>.
                </h1>

                <p class="font-sans text-base md:text-lg text-white/70 leading-relaxed max-w-md">
                    Authorized personnel only. Enter your credentials to access protected cloud topology schematics and deployment controls.
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>auth_gateway.sh</span>
                    </div>
                </div>
                <div class="p-5 font-mono text-xs space-y-3 bg-slate-950/45 text-white/90">
                    <p class="text-white/40 italic">// Authentication subsystem online</p>
                    <p><span class="text-indigo-400">protocol:</span> <span class="text-white/70">OIDC / Session Token</span></p>
                    <p><span class="text-indigo-400">encryption:</span> <span class="text-white/70">TLS 1.3 + RSA-4096</span></p>
                    <p><span class="text-indigo-400">status:</span> <span class="text-emerald-400">AWAITING_CREDENTIALS</span></p>
                    <p class="text-white/40 pt-2 border-t border-white/10">&gt; Enter email and password to initialize session...</p>
                </div>
            </div>
        </div>

        {{-- Right: login form --}}
        <div class="lg:col-span-7">
            <div class="glass-card-heavy border border-white/15 p-6 md:p-10 rounded-2xl relative hover:border-indigo-400/25 transition-all duration-300 shadow-2xl">
                <div class="absolute top-4 left-4 flex gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500/60"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500/30"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500/30"></div>
                </div>
                <div class="absolute top-4 right-8 font-mono text-xs text-white/40 select-none">
                    login_form.blade.php
                </div>

                <div class="mt-8 space-y-6">
                    <div class="space-y-1">
                        <h2 class="font-mono text-sm font-bold text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            <span>SESSION_INITIALIZATION</span>
                        </h2>
                        <p class="font-sans text-xs text-white/50">Provide credentials to establish a secure session</p>
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
                            <span class="bg-transparent px-3 font-mono text-[11px] text-white/40">OR_EMAIL_AUTH</span>
                        </div>
                    </div>

                    <form action="{{ route('login') }}" method="POST" class="space-y-6">
                        @csrf

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
                                autofocus
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

                        <div class="flex items-center gap-3">
                            <input
                                class="w-4 h-4 rounded border-white/20 bg-slate-950/20 text-indigo-500 focus:ring-indigo-400 focus:ring-offset-0 cursor-pointer"
                                id="remember"
                                name="remember"
                                type="checkbox"
                                @checked(old('remember'))
                            />
                            <label class="font-mono text-xs text-white/60 cursor-pointer select-none" for="remember">
                                PERSIST_SESSION (remember me)
                            </label>
                        </div>

                        <button
                            class="w-full bg-indigo-500/85 hover:bg-indigo-500 border border-white/10 text-white font-sans font-semibold py-4 rounded-lg active:scale-[0.98] transition-all flex items-center justify-center gap-3 group cursor-pointer shadow-lg shadow-indigo-500/25"
                            type="submit"
                        >
                            <span>AUTHENTICATE</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </button>
                    </form>

                    <p class="font-mono text-[11px] text-white/40 text-center pt-2 border-t border-white/10">
                        <span class="text-indigo-400/80">&gt;</span> No identity yet?
                        <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300 transition-colors">Provision one here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
