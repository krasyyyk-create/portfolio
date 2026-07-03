<x-layouts.admin title="Admin — Edit User" header="users/edit">
    <div class="max-w-2xl space-y-6">
        <div>
            <a href="{{ route('admin.users.index') }}" class="font-mono text-xs text-white/40 hover:text-white transition-colors">
                &larr; back to users
            </a>
            <h1 class="font-sans text-2xl md:text-3xl font-bold text-white mt-2">Edit User</h1>
            <p class="font-sans text-sm text-white/50 mt-1">{{ $user->email }}</p>
        </div>

        <div class="glass-card-heavy border border-white/15 rounded-xl p-6 md:p-8">
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="space-y-2">
                    <label class="font-sans font-medium text-xs text-white/60" for="name">NAME</label>
                    <input
                        class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans @error('name') border-red-500/60 @enderror"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                    />
                    @error('name')
                        <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="font-sans font-medium text-xs text-white/60" for="email">EMAIL</label>
                    <input
                        class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans @error('email') border-red-500/60 @enderror"
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $user->email) }}"
                        required
                    />
                    @error('email')
                        <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="font-sans font-medium text-xs text-white/60" for="role">ROLE</label>
                    <select
                        class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-mono text-sm @error('role') border-red-500/60 @enderror"
                        id="role"
                        name="role"
                        required
                    >
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}" @selected(old('role', $user->role->value) === $role->value)>
                                {{ $role->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-white/10 pt-6 space-y-4">
                    <p class="font-mono text-xs text-white/40">Leave password fields blank to keep current password</p>

                    <div class="space-y-2">
                        <label class="font-sans font-medium text-xs text-white/60" for="password">NEW PASSWORD</label>
                        <input
                            class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans @error('password') border-red-500/60 @enderror"
                            id="password"
                            name="password"
                            type="password"
                        />
                        @error('password')
                            <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="font-sans font-medium text-xs text-white/60" for="password_confirmation">CONFIRM PASSWORD</label>
                        <input
                            class="w-full bg-slate-950/20 border border-white/10 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-slate-950/30 transition-all font-sans"
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                        />
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full bg-indigo-500/85 hover:bg-indigo-500 border border-white/10 text-white font-sans font-semibold py-3 rounded-lg active:scale-[0.98] transition-all cursor-pointer shadow-lg shadow-indigo-500/25"
                >
                    Save Changes
                </button>
            </form>
        </div>
    </div>
</x-layouts.admin>
