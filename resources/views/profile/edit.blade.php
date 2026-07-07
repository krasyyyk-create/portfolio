<x-layouts.app title="DEV_ARCHITECT — Edit Profile">
    <div class="max-w-2xl mx-auto">
        <header class="mb-6 space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 border border-white/10 rounded-full font-mono text-xs text-white/95 backdrop-blur-md">
                <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>EDIT PROFILE</span>
            </div>

            <h1 class="font-sans text-3xl md:text-4xl font-bold tracking-tight text-white">
                Edit <span class="text-indigo-400">Profile</span>
            </h1>
            <p class="font-sans text-sm text-white/60">
                Customize your public profile, account email, and password.
            </p>
        </header>

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

            <div class="flex justify-end">
                <a
                    href="{{ route('users.show', $user) }}"
                    class="font-mono text-xs text-indigo-300 hover:text-white border border-indigo-400/30 hover:border-indigo-400/50 bg-indigo-500/10 hover:bg-indigo-500/20 px-3 py-1.5 rounded-lg transition-all"
                >
                    view public profile
                </a>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <h2 class="font-mono text-sm font-bold text-white flex items-center gap-2">
                        <span class="text-indigo-400">&gt;</span> APPEARANCE
                    </h2>

                    <x-profile.banner-upload :user="$user" />
                    <x-profile.avatar-upload :user="$user" />
                </div>

                <div class="space-y-4">
                    <h2 class="font-mono text-sm font-bold text-white flex items-center gap-2">
                        <span class="text-indigo-400">&gt;</span> IDENTITY
                    </h2>

                    <div class="space-y-2">
                        <label class="font-sans font-medium text-xs text-white/60" for="name">DISPLAY NAME</label>
                        <input
                            class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans placeholder:text-white/30 @error('name') border-red-500/60 @enderror"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            type="text"
                            required
                        />
                        @error('name')
                            <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="font-sans font-medium text-xs text-white/60" for="bio">BIO</label>
                        <textarea
                            class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans placeholder:text-white/30 resize-y min-h-[100px] @error('bio') border-red-500/60 @enderror"
                            id="bio"
                            name="bio"
                            maxlength="500"
                            placeholder="Tell others a bit about yourself..."
                        >{{ old('bio', $user->bio) }}</textarea>
                        <p class="font-mono text-[10px] text-white/30">Max 500 characters.</p>
                        @error('bio')
                            <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button
                    class="w-full bg-indigo-500/85 hover:bg-indigo-500 border border-white/10 text-white font-sans font-semibold py-4 rounded-lg active:scale-[0.98] transition-all cursor-pointer shadow-lg shadow-indigo-500/25"
                    type="submit"
                >
                    SAVE PROFILE
                </button>
            </form>

            <form action="{{ route('account.update') }}" method="POST" class="space-y-8 pt-8 border-t border-white/10">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <h2 class="font-mono text-sm font-bold text-white flex items-center gap-2">
                        <span class="text-indigo-400">&gt;</span> ACCOUNT
                    </h2>
                    <p class="font-sans text-xs text-white/40">
                        @if ($user->usesGoogleAuth())
                            You signed in with Google. Update your email here or set a password to also sign in with email.
                        @else
                            Manage your email and password. Changes here require your current password.
                        @endif
                    </p>

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

                @if ($user->hasPassword())
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
                @endif

                <button
                    class="w-full bg-indigo-500/85 hover:bg-indigo-500 border border-white/10 text-white font-sans font-semibold py-4 rounded-lg active:scale-[0.98] transition-all cursor-pointer shadow-lg shadow-indigo-500/25"
                    type="submit"
                >
                    SAVE ACCOUNT
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
