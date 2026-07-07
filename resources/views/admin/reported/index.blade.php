<x-layouts.admin title="Admin — Reported Content" header="reported">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h1 class="font-sans text-2xl md:text-3xl font-bold text-white">Reported Content</h1>
                <p class="font-sans text-sm text-white/50 mt-1">
                    Review user reports on posts, comments, and profiles
                    @if ($pendingCount > 0)
                        <span class="text-amber-300">— {{ $pendingCount }} pending</span>
                    @endif
                </p>
            </div>

            <form action="{{ route('admin.reported.index') }}" method="GET" class="shrink-0">
                <label for="report-type-filter" class="sr-only">Filter reports by type</label>
                <select
                    id="report-type-filter"
                    name="type"
                    onchange="this.form.submit()"
                    class="bg-white/5 border border-white/10 text-white font-mono text-sm px-4 py-2.5 rounded-lg focus:outline-none focus:border-indigo-400/50 focus:bg-white/10 transition-all cursor-pointer"
                >
                    <option value="all" @selected($type === 'all')>All reports ({{ $pendingCount }})</option>
                    <option value="posts" @selected($type === 'posts')>Posts ({{ $pendingPostCount }})</option>
                    <option value="comments" @selected($type === 'comments')>Comments ({{ $pendingCommentCount }})</option>
                    <option value="profiles" @selected($type === 'profiles')>Profiles ({{ $pendingProfileCount }})</option>
                </select>
            </form>
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
                                $isProfile = $reportable instanceof \App\Models\User;
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
                                        @if ($author && ! $isProfile)
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
                                        @elseif ($isProfile)
                                            <a
                                                href="{{ route('users.show', $reportable) }}"
                                                target="_blank"
                                                class="inline-block mt-2 font-mono text-[10px] text-indigo-400 hover:text-indigo-300"
                                            >
                                                view profile
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
                                            onsubmit="return confirm('{{ $isProfile ? 'Clear this profile and notify the user?' : 'Draft this content and notify the author?' }}')"
                                        >
                                            @csrf
                                            <input type="hidden" name="type" value="{{ $type }}">
                                            <label class="block font-mono text-[10px] text-white/40 uppercase tracking-wider">
                                                Reason for {{ $isProfile ? 'user' : 'author' }} (optional)
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
                                                {{ $isProfile ? 'clear profile' : 'draft' }}
                                            </button>
                                        </form>

                                        <form
                                            action="{{ route('admin.reported.destroy', $report) }}"
                                            method="POST"
                                            onsubmit="return confirm('{{ $isProfile ? 'Permanently delete this user account?' : 'Permanently delete this content?' }}')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="type" value="{{ $type }}">
                                            <button
                                                type="submit"
                                                class="w-full font-mono text-xs text-red-400 hover:text-red-300 border border-red-400/20 hover:border-red-400/40 bg-red-500/10 hover:bg-red-500/20 px-3 py-1.5 rounded-lg transition-all cursor-pointer"
                                            >
                                                {{ $isProfile ? 'delete user' : 'delete' }}
                                            </button>
                                        </form>

                                        <form
                                            action="{{ route('admin.reported.dismiss', $report) }}"
                                            method="POST"
                                            onsubmit="return confirm('Dismiss this report and take no action?')"
                                        >
                                            @csrf
                                            <input type="hidden" name="type" value="{{ $type }}">
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
                                            <input type="hidden" name="type" value="{{ $type }}">
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
                                    @if ($type === 'profiles')
                                        No pending profile reports
                                    @elseif ($type === 'posts')
                                        No pending post reports
                                    @elseif ($type === 'comments')
                                        No pending comment reports
                                    @else
                                        No pending reports
                                    @endif
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
