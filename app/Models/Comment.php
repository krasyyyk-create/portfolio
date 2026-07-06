<?php

namespace App\Models;

use App\Services\CommentImageProcessor;
use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'body',
        'image_path',
        'image_width',
        'image_height',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Comment $comment): void {
            $comment->deleteStoredImage();
        });
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return Attribute<?string, never>
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->image_path
                ? Storage::disk('public')->url($this->image_path)
                : null
        );
    }

    public function hasImage(): bool
    {
        return filled($this->image_path);
    }

    public function storeUploadedImage(UploadedFile $file): void
    {
        $this->deleteStoredImage();

        $processed = app(CommentImageProcessor::class)->process($file);

        $this->image_path = $processed['path'];
        $this->image_width = $processed['width'];
        $this->image_height = $processed['height'];
    }

    public function deleteStoredImage(): void
    {
        if ($this->image_path) {
            Storage::disk('public')->delete($this->image_path);
        }
    }

    public function imageNeedsCropDisplay(): bool
    {
        if (! $this->hasImage()) {
            return false;
        }

        return $this->image_width > CommentImageProcessor::MAX_WIDTH
            || $this->image_height > CommentImageProcessor::MAX_HEIGHT;
    }
}
