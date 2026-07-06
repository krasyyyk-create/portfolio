@props(['notifications' => collect()])

@if ($notifications->isNotEmpty())
    <div
        x-data="{
            open: true,
            current: 0,
            notifications: @js($notifications->map(fn ($n) => [
                'id' => $n->id,
                'contentType' => $n->content_type,
                'contentLabel' => $n->content_label,
                'reason' => $n->reason,
                'readUrl' => route('moderation-notifications.read', $n),
            ])->values()->all()),
            get notification() {
                return this.notifications[this.current] ?? null;
            },
            async dismiss() {
                const item = this.notification;
                if (! item) return;

                await fetch(item.readUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content ?? '',
                        'Accept': 'application/json',
                    },
                });

                if (this.current < this.notifications.length - 1) {
                    this.current++;
                } else {
                    this.open = false;
                }
            },
        }"
        x-show="open && notification"
        x-cloak
        class="fixed inset-0 z-[110] flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
    >
        <div class="absolute inset-0 bg-slate-950/85 backdrop-blur-sm"></div>

        <div
            x-show="open && notification"
            x-transition
            class="relative w-full max-w-lg glass-card border border-amber-400/30 bg-amber-500/5 rounded-2xl p-6 md:p-8 space-y-5 shadow-2xl shadow-black/50"
        >
            <div class="flex items-start gap-4">
                <div class="shrink-0 w-10 h-10 rounded-full bg-amber-500/20 border border-amber-400/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="space-y-2 min-w-0">
                    <p class="font-mono text-xs text-amber-300 uppercase tracking-wider">Content moderation notice</p>
                    <h3 class="font-sans text-xl font-bold text-white">
                        Your <span x-text="notification?.contentType"></span> was reported and drafted
                    </h3>
                    <p class="font-sans text-sm text-white/70 leading-relaxed">
                        <span class="text-white/90 font-medium" x-text="notification?.contentLabel"></span>
                        was removed from public view after a report was reviewed by our team.
                    </p>
                </div>
            </div>

            <div class="rounded-xl border border-white/10 bg-white/5 p-4 space-y-1">
                <p class="font-mono text-[10px] text-white/40 uppercase tracking-wider">Reason</p>
                <p class="font-sans text-sm text-white/90 leading-relaxed" x-text="notification?.reason"></p>
            </div>

            <div class="flex items-center justify-between gap-4">
                <p
                    x-show="notifications.length > 1"
                    class="font-mono text-[10px] text-white/40"
                    x-text="`${current + 1} of ${notifications.length}`"
                ></p>
                <div class="flex justify-end flex-1">
                    <button
                        type="button"
                        @click="dismiss()"
                        class="font-mono text-sm text-white bg-indigo-500/85 hover:bg-indigo-500 border border-white/10 px-5 py-2 rounded-lg transition-all"
                    >
                        I understand
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
