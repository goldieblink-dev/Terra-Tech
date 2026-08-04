<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    public const CACHE_PREFIX = 'published_announcements_';
    public const CACHE_VERSION_KEY = 'published_announcements_version';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'attachment_file',
        'attachment_name',
        'priority',
        'status',
        'published_at',
        'downloads_count',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'published_at'    => 'datetime',
            'downloads_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });

        static::restored(function () {
            static::clearCache();
        });
    }

    /**
     * Generate unique slug considering soft-deleted records.
     */
    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    /**
     * Get current cache version for published announcements.
     */
    public static function cacheVersion(): int
    {
        return (int) Cache::get(self::CACHE_VERSION_KEY, 1);
    }

    /**
     * Clear cache for published announcements by incrementing the cache version.
     */
    public static function clearCache(): void
    {
        if (Cache::has(self::CACHE_VERSION_KEY)) {
            Cache::increment(self::CACHE_VERSION_KEY);
        } else {
            Cache::forever(self::CACHE_VERSION_KEY, 2);
        }
    }

    /**
     * Accessor for full attachment URL.
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if ($this->attachment_file && Storage::disk('public')->exists($this->attachment_file)) {
            return asset('storage/' . $this->attachment_file);
        }

        return null;
    }

    /**
     * Scope for published announcements.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * Scope for ordering announcements on public frontend.
     * Orders by priority (urgent > important > normal) then published_at descending.
     */
    public function scopeOrderedForPublic($query)
    {
        return $query->orderByRaw("FIELD(priority, 'urgent', 'important', 'normal')")
                     ->orderBy('published_at', 'desc');
    }

    /**
     * Creator user relationship.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Updater user relationship.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
