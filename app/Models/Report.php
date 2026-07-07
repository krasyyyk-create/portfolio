<?php

namespace App\Models;

use App\Enums\ReportResolution;
use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'status',
        'resolution',
        'moderation_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ReportStatus::class,
            'resolution' => ReportResolution::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    public function moderationNotification(): HasOne
    {
        return $this->hasOne(ModerationNotification::class);
    }

    public function isPending(): bool
    {
        return $this->status === ReportStatus::Pending;
    }

    public function contentTypeLabel(): string
    {
        return match ($this->reportable_type) {
            Post::class => 'post',
            Comment::class => 'comment',
            User::class => 'profile',
            default => 'content',
        };
    }

    public function contentSummary(): string
    {
        $reportable = $this->reportable;

        if ($reportable instanceof Post) {
            return $reportable->title;
        }

        if ($reportable instanceof Comment) {
            $excerpt = trim(strip_tags($reportable->body ?? ''));

            if ($excerpt !== '') {
                return \Illuminate\Support\Str::limit($excerpt, 120);
            }

            return $reportable->hasImage() ? 'Image comment' : 'Comment';
        }

        if ($reportable instanceof User) {
            if ($reportable->bio) {
                return \Illuminate\Support\Str::limit($reportable->bio, 120);
            }

            return $reportable->name;
        }

        return 'Removed content';
    }

    public function contentAuthor(): ?User
    {
        $reportable = $this->reportable;

        if ($reportable instanceof Post || $reportable instanceof Comment) {
            return $reportable->author;
        }

        if ($reportable instanceof User) {
            return $reportable;
        }

        return null;
    }
}
