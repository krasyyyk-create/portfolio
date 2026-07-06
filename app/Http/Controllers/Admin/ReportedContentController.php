<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\ModerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportedContentController extends Controller
{
    public function index(): View
    {
        $reports = Report::query()
            ->with(['reporter', 'reportable'])
            ->where('status', ReportStatus::Pending)
            ->latest()
            ->paginate(20);

        return view('admin.reported.index', [
            'reports' => $reports,
            'pendingCount' => Report::query()->where('status', ReportStatus::Pending)->count(),
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

        return redirect()
            ->route('admin.reported.index')
            ->with('success', 'Content drafted and the author has been notified.');
    }

    public function destroy(Report $report, ModerationService $moderation): RedirectResponse
    {
        $moderation->deleteReportedContent($report, auth()->user());

        return redirect()
            ->route('admin.reported.index')
            ->with('success', 'Reported content deleted.');
    }

    public function dismiss(Report $report, ModerationService $moderation): RedirectResponse
    {
        $moderation->dismissReport($report, auth()->user());

        return redirect()
            ->route('admin.reported.index')
            ->with('success', 'Report dismissed with no action taken.');
    }
}
