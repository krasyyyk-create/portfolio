<x-layouts.admin title="Admin — Edit User" header="users/edit">
    <div class="max-w-2xl space-y-6">
        <div>
            <a href="{{ route('admin.users.index') }}" class="font-mono text-xs text-white/40 hover:text-white transition-colors">
                &larr; back to users
            </a>
            <h1 class="font-sans text-2xl md:text-3xl font-bold text-white mt-2">Edit User</h1>
            <p class="font-sans text-sm text-white/50 mt-1">{{ $user->email }}</p>
        </div>

        <div class="glass-card-heavy border border-white/15 rounded-xl p-6 md:p-8 space-y-6">
            <div class="space-y-4">
                <h2 class="font-mono text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-indigo-400">&gt;</span> ADMIN ACCESS
                </h2>

                <div class="flex flex-wrap items-center justify-between gap-4 p-4 bg-slate-950/20 border border-white/10 rounded-lg">
                    <div class="space-y-1">
                        <p class="font-sans text-sm text-white/60">Current role</p>
                        <span @class([
                            'inline-flex font-mono text-[11px] px-2 py-0.5 rounded-full border',
                            'bg-indigo-500/20 text-indigo-300 border-indigo-400/30' => $user->isAdmin(),
                            'bg-white/5 text-white/50 border-white/10' => ! $user->isAdmin(),
                        ])>
                            {{ $user->role->label() }}
                        </span>
                    </div>

                    @if ($user->id === auth()->id())
                        <p class="font-mono text-xs text-white/40">You cannot remove your own admin role.</p>
                    @elseif ($user->isAdmin())
                        <form action="{{ route('admin.users.update-role', $user) }}" method="POST" onsubmit="return confirm('Remove admin access from {{ $user->name }}?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="role" value="user" />
                            <button
                                type="submit"
                                class="font-mono text-xs text-amber-400 hover:text-amber-300 border border-amber-400/30 hover:border-amber-400/50 bg-amber-500/10 hover:bg-amber-500/20 px-4 py-2 rounded-lg transition-all cursor-pointer"
                            >
                                revoke admin
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.users.update-role', $user) }}" method="POST" onsubmit="return confirm('Grant admin access to {{ $user->name }}?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="role" value="admin" />
                            <button
                                type="submit"
                                class="font-mono text-xs text-emerald-400 hover:text-emerald-300 border border-emerald-400/30 hover:border-emerald-400/50 bg-emerald-500/10 hover:bg-emerald-500/20 px-4 py-2 rounded-lg transition-all cursor-pointer"
                            >
                                make admin
                            </button>
                        </form>
                    @endif
                </div>

                @error('role')
                    <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
                @enderror
            </div>
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
