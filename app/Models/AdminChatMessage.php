<?php

namespace App\Models;

use Database\Factories\AdminChatMessageFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AdminChatMessage extends Model
{
    /** @use HasFactory<AdminChatMessageFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'body',
        'image_path',
    ];

    protected static function booted(): void
    {
        static::deleting(function (AdminChatMessage $message): void {
            $message->deleteStoredImage();
        });
    }

    public function sender(): BelongsTo
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
        $this->image_path = $file->store('admin-chat', 'public');
    }

    public function deleteStoredImage(): void
    {
        if ($this->image_path) {
            Storage::disk('public')->delete($this->image_path);
        }
    }

    public function toChatArray(): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'image_url' => $this->image_url,
            'user_id' => $this->user_id,
            'sender_name' => $this->sender?->name ?? 'Unknown',
            'sender_avatar' => $this->sender?->avatar_url,
            'is_mine' => auth()->id() === $this->user_id,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
