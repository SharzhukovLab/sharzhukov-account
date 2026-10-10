<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class UserProject extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'title', 'slug', 'tagline', 'description',
        'github_url', 'demo_url', 'download_url', 'external_url',
        'cover_image_path', 'gallery',
        'tech_stack', 'license', 'status_label',
        'status', 'moderation_note', 'moderated_by', 'moderated_at',
        'is_featured', 'order', 'views_count',
    ];

    protected $casts = [
        'gallery'      => 'array',
        'tech_stack'   => 'array',
        'moderated_at' => 'datetime',
        'is_featured'  => 'boolean',
    ];

    // === Relations ===
    public function user()      { return $this->belongsTo(User::class); }
    public function moderator() { return $this->belongsTo(User::class, 'moderated_by'); }

    // === Scopes ===
    public function scopeApproved($q)  { return $q->where('status', 'approved'); }
    public function scopePending($q)   { return $q->where('status', 'pending'); }
    public function scopeFeatured($q)  { return $q->where('is_featured', true); }

    public function scopePublicList($q)
    {
        return $q->approved()
                 ->orderByDesc('is_featured')
                 ->orderBy('order')
                 ->orderByDesc('created_at');
    }

    // === Auto slug ===
    protected static function booted(): void
    {
        static::creating(function (self $project) {
            if (empty($project->slug)) {
                $base = Str::slug($project->title);
                $slug = $base;
                $i = 1;
                while (self::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . (++$i);
                }
                $project->slug = $slug;
            }
        });
    }

    // === Accessors ===
    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image_path ? asset('storage/' . $this->cover_image_path) : null;
    }

    public function getGalleryUrlsAttribute(): array
    {
        return collect($this->gallery ?? [])
            ->map(fn ($path) => asset('storage/' . $path))
            ->toArray();
    }

    public function getUrlAttribute(): string
    {
        return route('projects.show', $this->slug);
    }

    /**
     * Fallback-градиент для карточки без cover image.
     * Выбирается детерминированно по slug.
     */
    public function getFallbackGradientAttribute(): string
    {
        $gradients = [
            'linear-gradient(135deg, #ffb088 0%, #ff6b35 50%, #d94a1a 100%)',
            'linear-gradient(135deg, #93c5fd 0%, #3b82f6 50%, #1e40af 100%)',
            'linear-gradient(135deg, #c4b5fd 0%, #8b5cf6 50%, #6d28d9 100%)',
            'linear-gradient(135deg, #86efac 0%, #10b981 50%, #047857 100%)',
            'linear-gradient(135deg, #fda4af 0%, #ec4899 50%, #9d174d 100%)',
            'linear-gradient(135deg, #fcd34d 0%, #f59e0b 50%, #b45309 100%)',
            'linear-gradient(135deg, #67e8f9 0%, #06b6d4 50%, #0e7490 100%)',
            'linear-gradient(135deg, #cbd5e1 0%, #64748b 50%, #334155 100%)',
        ];
        $hash = crc32($this->slug ?? (string) $this->id);
        return $gradients[$hash % count($gradients)];
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'success',
            'pending'  => 'warning',
            'rejected' => 'danger',
            'archived' => 'gray',
            default    => 'gray',
        };
    }

    public function isOwnedBy(?User $user): bool
    {
        return $user && $user->id === $this->user_id;
    }

    // === Moderation helpers ===
    public function approve(?User $moderator = null): void
    {
        $wasApproved = $this->status === 'approved';

        $this->update([
            'status'       => 'approved',
            'moderated_by' => $moderator?->id,
            'moderated_at' => now(),
        ]);

        // Уведомить автора, если проект не был approved
        if (!$wasApproved && $this->user) {
            $this->user->notify(new \App\Notifications\ProjectApprovedNotification($this));
        }
    }

    public function reject(?User $moderator = null, ?string $note = null): void
    {
        $wasRejected = $this->status === 'rejected';

        $this->update([
            'status'          => 'rejected',
            'moderation_note' => $note,
            'moderated_by'    => $moderator?->id,
            'moderated_at'    => now(),
        ]);

        // Уведомить автора
        if (!$wasRejected && $this->user) {
            $this->user->notify(new \App\Notifications\ProjectRejectedNotification($this));
        }
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }
}
