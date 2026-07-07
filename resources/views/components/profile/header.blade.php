@props(['user', 'isOwner' => false])

<div class="rounded-2xl border border-white/10 overflow-visible bg-slate-950/30 shadow-xl">
    <div @class([
        'h-48 md:h-60 rounded-t-2xl overflow-hidden',
        'bg-gradient-to-br from-indigo-600/50 via-slate-800/70 to-indigo-900/50' => ! $user->banner_url,
    ])>
        @if ($user->banner_url)
            <img
                src="{{ $user->banner_url }}"
                alt=""
                class="w-full h-full object-cover"
            />
        @endif
    </div>

    <div class="px-6 md:px-8 pb-6 md:pb-8">
        <div class="flex items-start gap-4 md:gap-5">
            <div class="shrink-0 -mt-14 md:-mt-16">
                @if ($user->avatar_url)
                    <img
                        src="{{ $user->avatar_url }}"
                        alt="{{ $user->name }}"
                        class="w-28 h-28 md:w-32 md:h-32 rounded-full object-cover border-[5px] border-slate-950 shadow-xl"
                    />
                @else
                    <div class="w-28 h-28 md:w-32 md:h-32 rounded-full bg-indigo-500/40 border-[5px] border-slate-950 shadow-xl flex items-center justify-center font-sans text-3xl md:text-4xl font-bold text-indigo-200">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <div class="flex-1 min-w-0 pt-5 md:pt-7 space-y-2">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <h1 class="font-sans text-2xl md:text-3xl font-bold text-white">{{ $user->name }}</h1>
                    @if ($isOwner)
                        <a
                            href="{{ route('profile.edit') }}"
                            class="font-mono text-xs text-indigo-300 hover:text-white border border-indigo-400/30 hover:border-indigo-400/50 bg-indigo-500/10 hover:bg-indigo-500/20 px-3 py-1.5 rounded-lg transition-all shrink-0"
                        >
                            edit profile
                        </a>
                    @elseif (auth()->check())
                        <x-report-button
                            :action="route('users.report', $user)"
                            label="Report this profile"
                            heading="Report profile"
                            description="Tell us why this profile should be reviewed. Reports are visible to admins only."
                            size="md"
                        />
                    @endif
                </div>

                @if ($user->bio)
                    <p class="font-sans text-sm md:text-base text-white/70 leading-relaxed max-w-2xl whitespace-pre-line">{{ $user->bio }}</p>
                @elseif ($isOwner)
                    <p class="font-mono text-xs text-white/40">No bio yet. <a href="{{ route('profile.edit') }}" class="text-indigo-300 hover:text-white transition-colors">Add one</a></p>
                @endif
            </div>
        </div>
    </div>
</div>
