<x-layouts.app title="DEV_ARCHITECT — Reservations">
    <div
        x-data="{
            selectedDate: {{ Js::from(old('starts_at') ? \Carbon\Carbon::parse(old('starts_at'))->toDateString() : now(config('services.google_calendar.timezone', config('app.timezone')))->toDateString()) }},
            selectedSlot: {{ Js::from(old('starts_at', '')) }},
            slots: [],
            loadingSlots: false,
            slotError: null,
            timezone: {{ Js::from($timezone) }},
            slotDurationMinutes: {{ $slotDurationMinutes }},
            formStatus: {{ session('success') ? "'success'" : "'idle'" }},
            successMessage: {{ Js::from(session('success', '')) }},
            warningMessage: {{ Js::from(session('warning', '')) }},
            async loadSlots() {
                this.loadingSlots = true;
                this.slotError = null;
                this.selectedSlot = '';
                try {
                    const response = await fetch(`{{ route('reservations.slots') }}?date=${this.selectedDate}`, {
                        headers: { 'Accept': 'application/json' },
                    });
                    if (!response.ok) {
                        throw new Error('Could not load available times.');
                    }
                    const data = await response.json();
                    this.slots = data.slots ?? [];
                    if (this.slots.length === 0) {
                        this.slotError = 'No open slots on this day. Try another weekday within the next 30 days.';
                    }
                } catch (error) {
                    this.slotError = error.message || 'Could not load available times.';
                    this.slots = [];
                } finally {
                    this.loadingSlots = false;
                }
            },
            formatSelectedSlot() {
                if (!this.selectedSlot) return '';
                const date = new Date(this.selectedSlot);
                return date.toLocaleString(undefined, {
                    weekday: 'short',
                    month: 'short',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                });
            },
            init() {
                this.loadSlots();
            }
        }"
        class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start"
    >
        <div class="lg:col-span-5 space-y-8">
            <header class="space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 border border-white/20 text-white font-semibold backdrop-blur-md rounded-full">
                    <span class="w-2.5 h-2.5 bg-emerald-400 rounded-full animate-pulse"></span>
                    <span class="font-mono text-[10px] text-emerald-200 uppercase tracking-widest font-bold">1-hour consultations</span>
                </div>

                <h1 class="font-sans text-4xl md:text-5xl font-bold tracking-tighter text-white leading-tight">
                    Book a <span class="text-indigo-400">Live Session.</span>
                </h1>

                <p class="font-sans text-base md:text-lg text-white/70 max-w-md leading-relaxed">
                    Pick an open hour for architecture reviews, technical consultations, or project planning. Sessions are one hour by default.
                </p>
            </header>

            <div class="glass-card border border-white/10 rounded-2xl p-6 space-y-4">
                <h2 class="font-mono text-xs text-indigo-300 uppercase tracking-widest">Availability Rules</h2>
                <ul class="space-y-3 font-sans text-sm text-white/70">
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-indigo-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Weekdays only, {{ config('services.google_calendar.start_hour', 9) }}:00 – {{ config('services.google_calendar.end_hour', 17) }}:00 ({{ $timezone }})</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-indigo-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Each reservation blocks a {{ $slotDurationMinutes }}-minute slot</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-indigo-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Book up to {{ config('services.google_calendar.advance_days', 30) }} days ahead</span>
                    </li>
                    @if ($googleCalendarConnected)
                        <li class="flex items-start gap-3">
                            <svg class="w-4 h-4 text-emerald-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-emerald-300/90">Synced with Google Calendar ({{ $googleCalendarLabel }}) for real-time availability</span>
                        </li>
                    @endif
                </ul>
            </div>

            @auth
                @php
                    $upcoming = auth()->user()->reservations()
                        ->where('status', \App\Enums\ReservationStatus::Confirmed)
                        ->where('starts_at', '>=', now())
                        ->orderBy('starts_at')
                        ->limit(5)
                        ->get();
                @endphp

                @if ($upcoming->isNotEmpty())
                    <div class="glass-card border border-white/10 rounded-2xl p-6 space-y-4">
                        <h2 class="font-mono text-xs text-indigo-300 uppercase tracking-widest">Your Upcoming Sessions</h2>
                        <div class="space-y-3">
                            @foreach ($upcoming as $reservation)
                                <div class="flex items-center justify-between gap-4 p-3 rounded-xl bg-slate-950/30 border border-white/10">
                                    <div>
                                        <p class="font-sans text-sm text-white font-medium">
                                            {{ $reservation->starts_at->timezone($timezone)->format('M j, Y') }}
                                        </p>
                                        <p class="font-mono text-xs text-white/50">
                                            {{ $reservation->starts_at->timezone($timezone)->format('g:i A') }} – {{ $reservation->ends_at->timezone($timezone)->format('g:i A') }}
                                        </p>
                                    </div>
                                    <form method="POST" action="{{ route('reservations.destroy', $reservation) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-mono text-[11px] text-red-300 hover:text-red-200 border border-red-400/30 hover:border-red-400/50 px-3 py-1.5 rounded-lg transition-colors">
                                            cancel
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endauth
        </div>

        <div class="lg:col-span-7">
            <div
                x-show="formStatus === 'success'"
                x-cloak
                class="glass-card-heavy border border-emerald-400/30 rounded-2xl p-8 md:p-10 space-y-4 text-center"
            >
                <div class="w-14 h-14 mx-auto rounded-full bg-emerald-500/20 border border-emerald-400/40 flex items-center justify-center">
                    <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h2 class="font-sans text-2xl font-bold text-white">Reservation Confirmed</h2>
                <p class="font-sans text-white/70" x-text="successMessage"></p>
                <p x-show="warningMessage" x-text="warningMessage" class="font-sans text-sm text-amber-300/90 bg-amber-500/10 border border-amber-400/20 rounded-xl px-4 py-3"></p>
                <button
                    type="button"
                    @click="formStatus = 'idle'; loadSlots()"
                    class="font-mono text-sm text-indigo-300 hover:text-white border border-indigo-400/30 hover:border-indigo-400/50 px-4 py-2 rounded-lg transition-colors"
                >
                    book another slot
                </button>
            </div>

            <div
                x-show="formStatus !== 'success'"
                class="glass-card-heavy border border-white/15 p-6 md:p-10 rounded-2xl space-y-8 shadow-2xl"
            >
                <div class="space-y-2 border-b border-white/10 pb-4">
                    <h2 class="font-sans text-xl font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>SCHEDULE_SESSION</span>
                    </h2>
                    <p class="font-sans text-xs text-white/50">Choose a date, pick an open hour, and confirm your details.</p>
                </div>

                <div class="space-y-3">
                    <label for="reservation-date" class="font-mono text-xs text-white/60 uppercase tracking-wider">Select Date</label>
                    <input
                        id="reservation-date"
                        type="date"
                        x-model="selectedDate"
                        @change="loadSlots()"
                        min="{{ now($timezone)->toDateString() }}"
                        max="{{ now($timezone)->addDays((int) config('services.google_calendar.advance_days', 30))->toDateString() }}"
                        class="w-full bg-slate-950/40 border border-white/15 rounded-xl px-4 py-3 font-mono text-sm text-white focus:outline-none focus:border-indigo-400/60 focus:ring-1 focus:ring-indigo-400/30"
                    />
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs text-white/60 uppercase tracking-wider">Available Hours</span>
                        <span x-show="loadingSlots" class="font-mono text-[10px] text-indigo-300 animate-pulse">loading...</span>
                    </div>

                    <template x-if="slotError && !loadingSlots">
                        <p class="font-sans text-sm text-amber-300/90 bg-amber-500/10 border border-amber-400/20 rounded-xl px-4 py-3" x-text="slotError"></p>
                    </template>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3" x-show="!loadingSlots && slots.length > 0">
                        <template x-for="slot in slots" :key="slot.starts_at">
                            <button
                                type="button"
                                @click="selectedSlot = slot.starts_at"
                                :class="selectedSlot === slot.starts_at ? 'bg-indigo-500/20 border-indigo-400 text-white' : 'bg-slate-950/30 border-white/10 text-white/70 hover:border-indigo-400/40 hover:text-white'"
                                class="border rounded-xl px-3 py-3 font-mono text-xs transition-all"
                                x-text="slot.label"
                            ></button>
                        </template>
                    </div>
                </div>

                <form method="POST" action="{{ route('reservations.store') }}" class="space-y-5 border-t border-white/10 pt-6">
                    @csrf
                    <input type="hidden" name="starts_at" :value="selectedSlot">

                    @guest
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="name" class="font-mono text-xs text-white/60 uppercase tracking-wider">Name</label>
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name') }}"
                                    required
                                    class="w-full bg-slate-950/40 border border-white/15 rounded-xl px-4 py-3 font-sans text-sm text-white focus:outline-none focus:border-indigo-400/60"
                                />
                                @error('name')
                                    <p class="font-mono text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-2">
                                <label for="email" class="font-mono text-xs text-white/60 uppercase tracking-wider">Email</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    required
                                    class="w-full bg-slate-950/40 border border-white/15 rounded-xl px-4 py-3 font-sans text-sm text-white focus:outline-none focus:border-indigo-400/60"
                                />
                                @error('email')
                                    <p class="font-mono text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-slate-950/30 border border-white/10 font-sans text-sm text-white/70">
                            Booking as <span class="text-white font-medium">{{ auth()->user()->name }}</span>
                            (<span class="text-indigo-300">{{ auth()->user()->email }}</span>)
                        </div>
                    @endguest

                    <div class="space-y-2">
                        <label for="notes" class="font-mono text-xs text-white/60 uppercase tracking-wider">Notes (optional)</label>
                        <textarea
                            id="notes"
                            name="notes"
                            rows="3"
                            placeholder="Briefly describe what you'd like to discuss..."
                            class="w-full bg-slate-950/40 border border-white/15 rounded-xl px-4 py-3 font-sans text-sm text-white placeholder:text-white/30 focus:outline-none focus:border-indigo-400/60 resize-none"
                        >{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="font-mono text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    @error('starts_at')
                        <p class="font-mono text-xs text-red-400">{{ $message }}</p>
                    @enderror

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2">
                        <p class="font-mono text-[11px] text-white/45" x-show="selectedSlot">
                            Selected: <span class="text-indigo-300" x-text="formatSelectedSlot()"></span>
                        </p>
                        <button
                            type="submit"
                            :disabled="!selectedSlot"
                            :class="!selectedSlot ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-500 active:scale-95'"
                            class="bg-indigo-500/90 text-white border border-white/10 font-sans text-sm px-8 py-3 rounded-xl font-semibold transition-all shadow-md shadow-indigo-500/10"
                        >
                            Confirm Reservation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
