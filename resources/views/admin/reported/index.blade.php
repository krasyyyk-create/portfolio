<x-layouts.admin title="Admin — Reported Content" header="reported">
    <div class="space-y-6">
        <div>
            <h1 class="font-sans text-2xl md:text-3xl font-bold text-white">Reported Content</h1>
            <p class="font-sans text-sm text-white/50 mt-1">
                Review user reports on posts and comments
                @if ($pendingCount > 0)
                    <span class="text-amber-300">— {{ $pendingCount }} pending</span>
                @endif
            </p>
        </div>

        <div class="glass-card rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Content</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Reported by</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Reason</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider">Submitted</th>
                            <th class="px-6 py-3 font-mono text-xs text-white/40 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($reports as $report)
                            @php
                                $reportable = $report->reportable;
                                $author = $report->contentAuthor();
                            @endphp
                            <tr class="hover:bg-white/5 transition-colors align-top">
                                <td class="px-6 py-4">
                                    <span class="inline-flex font-mono text-[11px] px-2 py-0.5 rounded-full border bg-amber-500/10 text-amber-300 border-amber-400/20 uppercase">
                                        {{ $report->contentTypeLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 max-w-xs">
                                    @if ($reportable)
                                        <p class="font-sans text-sm text-white leading-snug">{{ $report->contentSummary() }}</p>
                                        @if ($author)
                                            <p class="font-mono text-[10px] text-white/40 mt-1">by {{ $author->name }}</p>
                                        @endif
                                        @if ($reportable instanceof \App\Models\Post && $reportable->is_published)
                                            <a
                                                href="{{ route('posts.show', $reportable) }}"
                                                target="_blank"
                                                class="inline-block mt-2 font-mono text-[10px] text-indigo-400 hover:text-indigo-300"
                                            >
                                                view post
                                            </a>
                                        @elseif ($reportable instanceof \App\Models\Comment)
                                            <a
                                                href="{{ route('posts.show', $reportable->post) }}#comment-{{ $reportable->id }}"
                                                target="_blank"
                                                class="inline-block mt-2 font-mono text-[10px] text-indigo-400 hover:text-indigo-300"
                                            >
                                                view comment
                                            </a>
                                        @endif
                                    @else
                                        <p class="font-mono text-xs text-white/40">Content unavailable</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-white/60 whitespace-nowrap">
                                    {{ $report->reporter->name }}
                                </td>
                                <td class="px-6 py-4 max-w-sm">
                                    <p class="font-sans text-sm text-white/80 leading-relaxed">{{ $report->reason }}</p>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-white/40 whitespace-nowrap">
                                    {{ $report->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-right space-y-3 min-w-[12rem]">
                                    @if ($reportable)
                                        <form
                                            action="{{ route('admin.reported.draft', $report) }}"
                                            method="POST"
                                            class="space-y-2 text-left"
                                            onsubmit="return confirm('Draft this content and notify the author?')"
                                        >
                                            @csrf
                                            <label class="block font-mono text-[10px] text-white/40 uppercase tracking-wider">
                                                Reason for author (optional)
                                            </label>
                                            <textarea
                                                name="moderation_reason"
                                                rows="2"
                                                placeholder="{{ $report->reason }}"
                                                class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 font-sans text-xs text-white placeholder:text-white/30 focus:outline-none focus:border-indigo-400/50"
                                            >{{ old('moderation_reason') }}</textarea>
                                            <button
                                                type="submit"
                                                class="w-full font-mono text-xs text-amber-300 hover:text-amber-200 border border-amber-400/30 hover:border-amber-400/50 bg-amber-500/10 hover:bg-amber-500/20 px-3 py-1.5 rounded-lg transition-all cursor-pointer"
                                            >
                                                draft
                                            </button>
                                        </form>

                                        <form
                                            action="{{ route('admin.reported.destroy', $report) }}"
                                            method="POST"
                                            onsubmit="return confirm('Permanently delete this content?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="w-full font-mono text-xs text-red-400 hover:text-red-300 border border-red-400/20 hover:border-red-400/40 bg-red-500/10 hover:bg-red-500/20 px-3 py-1.5 rounded-lg transition-all cursor-pointer"
                                            >
                                                delete
                                            </button>
                                        </form>

                                        <form
                                            action="{{ route('admin.reported.dismiss', $report) }}"
                                            method="POST"
                                            onsubmit="return confirm('Dismiss this report and take no action?')"
                                        >
                                            @csrf
                                            <button
                                                type="submit"
                                                class="w-full font-mono text-xs text-white/60 hover:text-white border border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10 px-3 py-1.5 rounded-lg transition-all cursor-pointer"
                                            >
                                                take no action
                                            </button>
                                        </form>
                                    @else
                                        <form
                                            action="{{ route('admin.reported.dismiss', $report) }}"
                                            method="POST"
                                            onsubmit="return confirm('Dismiss this report and take no action?')"
                                        >
                                            @csrf
                                            <button
                                                type="submit"
                                                class="w-full font-mono text-xs text-white/60 hover:text-white border border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10 px-3 py-1.5 rounded-lg transition-all cursor-pointer"
                                            >
                                                take no action
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center font-mono text-sm text-white/40">
                                    No pending reports
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($reports->hasPages())
                <div class="px-6 py-4 border-t border-white/10">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
