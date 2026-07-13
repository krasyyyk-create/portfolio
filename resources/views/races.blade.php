<x-layouts.app title="VERTEX — F1 Races">
    <div class="space-y-10">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div class="space-y-3">
                <h1 class="font-sans text-3xl font-bold text-white flex items-center gap-2.5">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6H8.5l-1 1H5a2 2 0 00-2 2zm9-13.5V9"/>
                    </svg>
                    <span>RACE_CALENDAR</span>
                </h1>
                <p class="font-sans text-white/70 max-w-xl text-sm leading-relaxed">
                    Live race schedule from the API-Sports Formula 1 feed. Filter by season and session type.
                </p>
            </div>

            <form method="GET" action="{{ route('races.index') }}" class="flex flex-wrap items-end gap-3">
                <div class="space-y-1">
                    <label for="season" class="font-mono text-[10px] text-white/50 uppercase tracking-wider">Season</label>
                    <input
                        type="number"
                        id="season"
                        name="season"
                        value="{{ $meta['season'] }}"
                        min="1950"
                        max="2100"
                        class="w-28 bg-slate-950/20 border border-white/10 text-white px-3 py-2 rounded-lg focus:outline-none focus:border-indigo-400 font-mono text-sm"
                    />
                </div>
                <div class="space-y-1">
                    <label for="type" class="font-mono text-[10px] text-white/50 uppercase tracking-wider">Type</label>
                    <select
                        id="type"
                        name="type"
                        class="bg-slate-950/20 border border-white/10 text-white px-3 py-2 rounded-lg focus:outline-none focus:border-indigo-400 font-mono text-sm cursor-pointer"
                    >
                        @foreach (['Race', 'Qualifying', 'Practice 1', 'Practice 2', 'Practice 3', 'Sprint'] as $raceType)
                            <option value="{{ $raceType }}" @selected($meta['type'] === $raceType) class="bg-slate-900">{{ $raceType }}</option>
                        @endforeach
                    </select>
                </div>
                <button
                    type="submit"
                    class="bg-indigo-500/80 hover:bg-indigo-500 text-white border border-white/10 font-mono text-xs px-4 py-2.5 rounded-lg font-semibold transition-all"
                >
                    FETCH_DATA
                </button>
            </form>
        </div>

        @if ($error)
            <div class="glass-card border border-red-500/30 bg-red-500/10 p-6 rounded-2xl">
                <p class="font-mono text-sm text-red-300">&gt; {{ $error }}</p>
            </div>
        @elseif (empty($races))
            <div class="glass-card p-10 rounded-2xl text-center">
                <p class="font-mono text-sm text-white/60">No races found for {{ $meta['season'] }} ({{ $meta['type'] }}).</p>
            </div>
        @else
            <div class="flex items-center justify-between">
                <p class="font-mono text-xs text-white/50">
                    // {{ $meta['results'] }} result(s) — season {{ $meta['season'] }}, type {{ $meta['type'] }}
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach ($races as $race)
                    @php
                        $competition = $race['competition'] ?? [];
                        $circuit = $race['circuit'] ?? [];
                        $location = $competition['location'] ?? [];
                        $laps = $race['laps'] ?? [];
                        $raceDate = isset($race['date']) ? \Illuminate\Support\Carbon::parse($race['date']) : null;
                    @endphp

                    <article class="glass-card rounded-2xl overflow-hidden hover:bg-white/15 hover:border-indigo-400/30 transition-all duration-300 shadow-xl">
                        @if (! empty($circuit['image']))
                            <div class="h-40 overflow-hidden border-b border-white/10 bg-slate-950/40">
                                <img
                                    src="{{ $circuit['image'] }}"
                                    alt="{{ $circuit['name'] ?? 'Circuit' }}"
                                    class="w-full h-full object-cover opacity-80"
                                />
                            </div>
                        @endif

                        <div class="p-6 space-y-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-1">
                                    <span class="font-mono text-[10px] text-indigo-300 uppercase tracking-wider">
                                        {{ $race['type'] ?? 'Race' }}
                                    </span>
                                    <h2 class="font-sans text-lg font-bold text-white">
                                        {{ $competition['name'] ?? 'Unknown Grand Prix' }}
                                    </h2>
                                </div>
                                @if (! empty($race['status']))
                                    <span class="font-mono text-[10px] text-purple-300 bg-purple-500/10 border border-purple-500/20 px-2.5 py-0.5 rounded-full uppercase">
                                        {{ $race['status'] }}
                                    </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-3 font-mono text-xs">
                                <div class="bg-slate-950/25 border border-white/10 rounded-lg p-3 space-y-1">
                                    <span class="text-white/40 block">Circuit</span>
                                    <span class="text-white/90">{{ $circuit['name'] ?? '—' }}</span>
                                </div>
                                <div class="bg-slate-950/25 border border-white/10 rounded-lg p-3 space-y-1">
                                    <span class="text-white/40 block">Location</span>
                                    <span class="text-white/90">
                                        @if (! empty($location['city']) || ! empty($location['country']))
                                            {{ trim(($location['city'] ?? '').', '.($location['country'] ?? ''), ', ') }}
                                        @else
                                            —
                                        @endif
                                    </span>
                                </div>
                                <div class="bg-slate-950/25 border border-white/10 rounded-lg p-3 space-y-1">
                                    <span class="text-white/40 block">Date</span>
                                    <span class="text-indigo-300">
                                        {{ $raceDate?->format('M j, Y H:i') ?? ($race['date'] ?? '—') }}
                                    </span>
                                </div>
                                <div class="bg-slate-950/25 border border-white/10 rounded-lg p-3 space-y-1">
                                    <span class="text-white/40 block">Timezone</span>
                                    <span class="text-white/90">{{ $race['timezone'] ?? '—' }}</span>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2 pt-2 border-t border-white/10">
                                @if (! empty($race['season']))
                                    <span class="font-mono text-[10px] text-white/80 bg-white/5 px-2.5 py-1 rounded-md border border-white/10">
                                        Season {{ $race['season'] }}
                                    </span>
                                @endif
                                @if (! empty($laps['total']))
                                    <span class="font-mono text-[10px] text-white/80 bg-white/5 px-2.5 py-1 rounded-md border border-white/10">
                                        {{ $laps['total'] }} laps
                                    </span>
                                @endif
                                @if (! empty($race['distance']))
                                    <span class="font-mono text-[10px] text-white/80 bg-white/5 px-2.5 py-1 rounded-md border border-white/10">
                                        {{ $race['distance'] }} km
                                    </span>
                                @endif
                                @if (! empty($race['id']))
                                    <span class="font-mono text-[10px] text-white/50 bg-white/5 px-2.5 py-1 rounded-md border border-white/10">
                                        ID {{ $race['id'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
