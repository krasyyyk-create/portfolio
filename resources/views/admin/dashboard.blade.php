<x-layouts.admin title="Admin — Dashboard" header="dashboard">
    <div class="space-y-8">
        <div>
            <h1 class="font-sans text-2xl md:text-3xl font-bold text-white">Control Center</h1>
            <p class="font-sans text-sm text-white/50 mt-1">System overview and management console</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="glass-card rounded-xl p-6 space-y-2">
                <p class="font-mono text-xs text-white/40 uppercase tracking-wider">Total Users</p>
                <p class="font-sans text-3xl font-bold text-white">{{ $stats['users'] }}</p>
            </div>

            <div class="glass-card rounded-xl p-6 space-y-2">
                <p class="font-mono text-xs text-white/40 uppercase tracking-wider">Administrators</p>
                <p class="font-sans text-3xl font-bold text-indigo-400">{{ $stats['admins'] }}</p>
            </div>

            <div class="glass-card rounded-xl p-6 space-y-2">
                <p class="font-mono text-xs text-white/40 uppercase tracking-wider">API Status</p>
                <p class="font-sans text-lg font-semibold text-emerald-400 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    {{ config('services.api_sports.key') ? 'Configured' : 'Not configured' }}
                </p>
            </div>
        </div>

        <div class="glass-card rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
                <h2 class="font-mono text-sm font-bold text-white">Recent Users</h2>
                <a href="{{ route('admin.users.index') }}" class="font-mono text-xs text-indigo-400 hover:text-indigo-300 transition-colors">
                    view all &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($stats['recent'] as $user)
                            <tr class="hover:bg-white/5 transition-colors">
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
                                <td class="px-6 py-3 font-mono text-xs text-white/40">{{ $user->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center font-mono text-sm text-white/40">No users found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="glass-card rounded-xl p-6">
            <h2 class="font-mono text-sm font-bold text-white mb-4">Quick Links</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach ([
                    ['route' => 'races.index', 'label' => 'Races'],
                    ['route' => 'teams.index', 'label' => 'Teams'],
                    ['route' => 'drivers.index', 'label' => 'Drivers'],
                    ['route' => 'circuits.index', 'label' => 'Circuits'],
                ] as $link)
                    <a
                        href="{{ route($link['route']) }}"
                        class="font-mono text-xs text-center text-white/60 hover:text-white border border-white/10 hover:border-indigo-400/30 bg-white/5 hover:bg-indigo-500/10 px-4 py-3 rounded-lg transition-all"
                    >
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.admin>
