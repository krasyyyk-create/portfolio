<x-layouts.admin title="Admin — Users" header="users">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-sans text-2xl md:text-3xl font-bold text-white">Users</h1>
                <p class="font-sans text-sm text-white/50 mt-1">Manage registered accounts and roles</p>
            </div>
        </div>

        <div class="glass-card rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Joined</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($users as $user)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-3 font-mono text-xs text-white/40">{{ $user->id }}</td>
                                <td class="px-6 py-3 font-sans text-sm text-white">{{ $user->name }}</td>
                                <td class="px-6 py-3 font-mono text-xs text-white/60">{{ $user->email }}</td>
                                <td class="px-6 py-3">
                                    <span @class([
                                        'inline-flex font-mono text-[11px] px-2 py-0.5 rounded-full border',
                                        'bg-indigo-500/20 text-indigo-300 border-indigo-400/30' => $user->isAdmin(),
                                        'bg-white/5 text-white/50 border-white/10' => ! $user->isAdmin(),
                                    ])>
                                        {{ $user->role->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 font-mono text-xs text-white/40">{{ $user->created_at->format('M j, Y') }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-3">
                                        <a
                                            href="{{ route('admin.users.edit', $user) }}"
                                            class="font-mono text-xs text-indigo-400 hover:text-indigo-300 transition-colors"
                                        >
                                            edit
                                        </a>
                                        @if ($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-mono text-xs text-red-400 hover:text-red-300 transition-colors cursor-pointer">
                                                    delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center font-mono text-sm text-white/40">No users found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="px-6 py-4 border-t border-white/10">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
