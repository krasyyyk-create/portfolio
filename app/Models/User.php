<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'role',
        'avatar_path',
        'bio',
        'banner_path',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function hasRole(UserRole $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::Admin);
    }

    public function usesGoogleAuth(): bool
    {
        return $this->google_id !== null && $this->password === null;
    }

    public function hasPassword(): bool
    {
        return $this->password !== null;
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function receivedReports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_likes')->withTimestamps();
    }

    public function moderationNotifications(): HasMany
    {
        return $this->hasMany(ModerationNotification::class);
    }

    /**
     * @return Attribute<?string, never>
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->avatar_path
                ? Storage::disk('public')->url($this->avatar_path)
                : null
        );
    }

    public function storeUploadedAvatar(UploadedFile $file): void
    {
        $this->deleteStoredAvatar();
        $this->avatar_path = $file->store('avatars', 'public');
    }

    public function deleteStoredAvatar(): void
    {
        if ($this->avatar_path) {
            Storage::disk('public')->delete($this->avatar_path);
        }
    }

    /**
     * @return Attribute<?string, never>
     */
    protected function bannerUrl(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->banner_path
                ? Storage::disk('public')->url($this->banner_path)
                : null
        );
    }

    public function storeUploadedBanner(UploadedFile $file): void
    {
        $this->deleteStoredBanner();
        $this->banner_path = $file->store('banners', 'public');
    }

    public function deleteStoredBanner(): void
    {
        if ($this->banner_path) {
            Storage::disk('public')->delete($this->banner_path);
        }
    }
}
