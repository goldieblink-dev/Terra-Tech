<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class Timeline extends Model
{
    use HasFactory, SoftDeletes;

    public const CACHE_PREFIX = 'published_timelines_';
    public const CACHE_VERSION_KEY = 'published_timelines_version';

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'location',
        'color',
        'icon',
        'status',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
            'sort_order' => 'integer',
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
     * Get current cache version for published timelines.
     */
    public static function cacheVersion(): int
    {
        return (int) Cache::get(self::CACHE_VERSION_KEY, 1);
    }

    /**
     * Clear cache for published timelines by incrementing the cache version.
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
     * Computed timeline_status accessor (upcoming, ongoing, completed).
     */
    public function getTimelineStatusAttribute(): string
    {
        $today = Carbon::today()->toDateString();
        $startDate = $this->start_date ? $this->start_date->toDateString() : null;
        $endDate = $this->end_date ? $this->end_date->toDateString() : null;

        if ($endDate && $today > $endDate) {
            return 'completed';
        }

        if ($startDate && $today < $startDate) {
            return 'upcoming';
        }

        return 'ongoing';
    }

    /**
     * Scope for published timelines.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for ordering timelines on public website.
     * Orders by sort_order ascending, then start_date ascending.
     */
    public function scopeOrderedForPublic($query)
    {
        return $query->orderBy('sort_order', 'asc')
                     ->orderBy('start_date', 'asc');
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
