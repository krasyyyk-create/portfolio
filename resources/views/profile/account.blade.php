<x-layouts.app title="DEV_ARCHITECT — Account Info">
    <div class="max-w-2xl mx-auto">
        <header class="mb-6 space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 border border-white/10 rounded-full font-mono text-xs text-white/95 backdrop-blur-md">
                <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>ACCOUNT INFO // SECURITY SETTINGS</span>
            </div>

            <h1 class="font-sans text-3xl md:text-4xl font-bold tracking-tight text-white">
                Account <span class="text-indigo-400">Info</span>
            </h1>
            <p class="font-sans text-sm text-white/60">
                Manage your email, password, and session. Changes here require your current password.
            </p>
        </header>

        <x-profile.nav active="account" />

        <div class="glass-card-heavy border border-white/15 p-6 md:p-10 rounded-2xl relative shadow-2xl space-y-8">
            @if (session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-lg font-mono text-sm text-emerald-400">
                    &gt; {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg font-mono text-xs text-red-400 space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>&gt; {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('account.update') }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <h2 class="font-mono text-sm font-bold text-white flex items-center gap-2">
                        <span class="text-indigo-400">&gt;</span> EMAIL
                    </h2>

                    <div class="space-y-2">
                        <label class="font-sans font-medium text-xs text-white/60" for="email">EMAIL ADDRESS</label>
                        <input
                            class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans placeholder:text-white/30 @error('email') border-red-500/60 @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            type="email"
                            required
                        />
                        @error('email')
                            <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-4 pt-4 border-t border-white/10">
                    <h2 class="font-mono text-sm font-bold text-white flex items-center gap-2">
                        <span class="text-indigo-400">&gt;</span> CHANGE PASSWORD
                    </h2>
                    <p class="font-sans text-xs text-white/40">Leave blank to keep your current password.</p>

                    <div class="space-y-2">
                        <label class="font-sans font-medium text-xs text-white/60" for="password">NEW PASSWORD</label>
                        <input
                            class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans placeholder:text-white/30 @error('password') border-red-500/60 @enderror"
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="new-password"
                        />
                        @error('password')
                            <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="font-sans font-medium text-xs text-white/60" for="password_confirmation">CONFIRM NEW PASSWORD</label>
                        <input
                            class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans placeholder:text-white/30"
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                        />
                    </div>
                </div>

                <div class="space-y-4 pt-4 border-t border-white/10">
                    <h2 class="font-mono text-sm font-bold text-white flex items-center gap-2">
                        <span class="text-indigo-400">&gt;</span> VERIFY CHANGES
                    </h2>
                    <p class="font-sans text-xs text-white/40">Enter your current password to save account changes.</p>

                    <div class="space-y-2">
                        <label class="font-sans font-medium text-xs text-white/60" for="current_password">CURRENT PASSWORD</label>
                        <input
                            class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans placeholder:text-white/30 @error('current_password') border-red-500/60 @enderror"
                            id="current_password"
                            name="current_password"
                            type="password"
                            autocomplete="current-password"
                            required
                        />
                        @error('current_password')
                            <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button
                    class="w-full bg-indigo-500/85 hover:bg-indigo-500 border border-white/10 text-white font-sans font-semibold py-4 rounded-lg active:scale-[0.98] transition-all cursor-pointer shadow-lg shadow-indigo-500/25"
                    type="submit"
                >
                    SAVE ACCOUNT INFO
                </button>
            </form>

            <div class="pt-6 border-t border-white/10">
                <h2 class="font-mono text-sm font-bold text-white flex items-center gap-2 mb-4">
                    <span class="text-indigo-400">&gt;</span> SESSION
                </h2>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="font-mono text-sm text-white/60 hover:text-white border border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10 px-6 py-3 rounded-lg transition-all cursor-pointer"
                    >
                        &gt; logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
