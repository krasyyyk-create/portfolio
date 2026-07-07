<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use App\Services\ModerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportedContentController extends Controller
{
    public function index(Request $request): View
    {
        $type = $this->normalizeType($request->query('type', 'all'));

        $reports = Report::query()
            ->with(['reporter', 'reportable'])
            ->where('status', ReportStatus::Pending)
            ->when($type === 'posts', fn ($query) => $query->where('reportable_type', Post::class))
            ->when($type === 'comments', fn ($query) => $query->where('reportable_type', Comment::class))
            ->when($type === 'profiles', fn ($query) => $query->where('reportable_type', User::class))
            ->latest()
            ->paginate(20)
            ->appends(['type' => $type]);

        return view('admin.reported.index', [
            'reports' => $reports,
            'type' => $type,
            'pendingCount' => Report::query()->where('status', ReportStatus::Pending)->count(),
            'pendingPostCount' => Report::query()
                ->where('status', ReportStatus::Pending)
                ->where('reportable_type', Post::class)
                ->count(),
            'pendingCommentCount' => Report::query()
                ->where('status', ReportStatus::Pending)
                ->where('reportable_type', Comment::class)
                ->count(),
            'pendingProfileCount' => Report::query()
                ->where('status', ReportStatus::Pending)
                ->where('reportable_type', User::class)
                ->count(),
        ]);
    }

    public function draft(Request $request, Report $report, ModerationService $moderation): RedirectResponse
    {
        $validated = $request->validate([
            'moderation_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $moderation->draftReport(
            $report,
            $request->user(),
            $validated['moderation_reason'] ?? null
        );

        $message = $report->reportable_type === User::class
            ? 'Profile cleared and the user has been notified.'
            : 'Content drafted and the author has been notified.';

        return redirect()
            ->route('admin.reported.index', ['type' => $this->normalizeType($request->input('type', 'all'))])
            ->with('success', $message);
    }

    public function destroy(Request $request, Report $report, ModerationService $moderation): RedirectResponse
    {
        $isProfile = $report->reportable_type === User::class;

        $moderation->deleteReportedContent($report, auth()->user());

        $message = $isProfile
            ? 'Reported user deleted.'
            : 'Reported content deleted.';

        return redirect()
            ->route('admin.reported.index', ['type' => $this->normalizeType($request->input('type', 'all'))])
            ->with('success', $message);
    }

    public function dismiss(Request $request, Report $report, ModerationService $moderation): RedirectResponse
    {
        $moderation->dismissReport($report, auth()->user());

        return redirect()
            ->route('admin.reported.index', ['type' => $this->normalizeType($request->input('type', 'all'))])
            ->with('success', 'Report dismissed with no action taken.');
    }

    private function normalizeType(?string $type): string
    {
        return in_array($type, ['posts', 'comments', 'profiles'], true) ? $type : 'all';
    }
}
