<?php

namespace App\Services;

use App\Enums\ReportResolution;
use App\Enums\ReportStatus;
use App\Models\Comment;
use App\Models\ModerationNotification;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ModerationService
{
    public function draftReport(Report $report, User $admin, ?string $moderationReason = null): void
    {
        if (! $report->isPending()) {
            throw new InvalidArgumentException('This report has already been reviewed.');
        }

        $reportable = $report->reportable;

        if (! $reportable instanceof Post && ! $reportable instanceof Comment) {
            throw new InvalidArgumentException('Reported content is no longer available.');
        }

        $reason = trim((string) ($moderationReason ?: $report->reason));

        DB::transaction(function () use ($report, $admin, $reportable, $reason): void {
            if ($reportable instanceof Post) {
                $reportable->update([
                    'is_published' => false,
                    'published_at' => null,
                ]);
                $contentType = 'post';
                $contentLabel = $reportable->title;
            } else {
                $reportable->update(['is_hidden' => true]);
                $contentType = 'comment';
                $contentLabel = $report->contentSummary();
            }

            $author = $reportable->author;

            $this->resolvePendingReports(
                $reportable,
                ReportResolution::Drafted,
                $admin,
                $reason,
                $report
            );

            if ($author) {
                ModerationNotification::create([
                    'user_id' => $author->id,
                    'report_id' => $report->id,
                    'content_type' => $contentType,
                    'content_label' => $contentLabel,
                    'reason' => $reason,
                ]);
            }
        });
    }

    public function deleteReportedContent(Report $report, User $admin): void
    {
        if (! $report->isPending()) {
            throw new InvalidArgumentException('This report has already been reviewed.');
        }

        $reportable = $report->reportable;

        if (! $reportable instanceof Post && ! $reportable instanceof Comment) {
            throw new InvalidArgumentException('Reported content is no longer available.');
        }

        DB::transaction(function () use ($report, $admin, $reportable): void {
            $this->resolvePendingReports(
                $reportable,
                ReportResolution::Deleted,
                $admin,
                null,
                $report
            );

            $reportable->delete();
        });
    }

    public function dismissReport(Report $report, User $admin): void
    {
        if (! $report->isPending()) {
            throw new InvalidArgumentException('This report has already been reviewed.');
        }

        $reportable = $report->reportable;

        if ($reportable instanceof Post || $reportable instanceof Comment) {
            $this->resolvePendingReports(
                $reportable,
                ReportResolution::NoAction,
                $admin,
                null,
                $report
            );

            return;
        }

        $report->update([
            'status' => ReportStatus::Resolved,
            'resolution' => ReportResolution::NoAction,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * @param  Post|Comment  $reportable
     */
    private function resolvePendingReports(
        Post|Comment $reportable,
        ReportResolution $resolution,
        User $admin,
        ?string $moderationReason,
        Report $primaryReport
    ): void {
        $pendingReports = Report::query()
            ->where('reportable_type', $reportable::class)
            ->where('reportable_id', $reportable->id)
            ->where('status', ReportStatus::Pending)
            ->get();

        foreach ($pendingReports as $pendingReport) {
            $pendingReport->update([
                'status' => ReportStatus::Resolved,
                'resolution' => $resolution,
                'moderation_reason' => $moderationReason,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);
        }

        if ($pendingReports->isEmpty()) {
            $primaryReport->update([
                'status' => ReportStatus::Resolved,
                'resolution' => $resolution,
                'moderation_reason' => $moderationReason,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);
        }
    }
}
