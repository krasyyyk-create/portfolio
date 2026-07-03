<x-layouts.app title="DEV_ARCHITECT — Profile">
    <div class="max-w-2xl mx-auto">
        <header class="mb-6 space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 border border-white/10 rounded-full font-mono text-xs text-white/95 backdrop-blur-md">
                <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>USER PROFILE</span>
            </div>

            <h1 class="font-sans text-3xl md:text-4xl font-bold tracking-tight text-white">
                Your <span class="text-indigo-400">Profile</span>
            </h1>
            <p class="font-sans text-sm text-white/60">
                Update your display name and profile photo.
            </p>
        </header>

        <x-profile.nav active="profile" />

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

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <h2 class="font-mono text-sm font-bold text-white flex items-center gap-2">
                        <span class="text-indigo-400">&gt;</span> IDENTITY
                    </h2>

                    <x-profile.avatar-upload :user="$user" />

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
                </div>

                <button
                    class="w-full bg-indigo-500/85 hover:bg-indigo-500 border border-white/10 text-white font-sans font-semibold py-4 rounded-lg active:scale-[0.98] transition-all cursor-pointer shadow-lg shadow-indigo-500/25"
                    type="submit"
                >
                    SAVE PROFILE
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
