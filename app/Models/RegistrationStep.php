<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RegistrationStep extends Model
{
    use HasFactory, SoftDeletes;

    public const CACHE_PREFIX = 'published_registration_steps_';
    public const CACHE_VERSION_KEY = 'published_registration_steps_version';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'requirements',
        'icon',
        'illustration_image',
        'sort_order',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'requirements' => 'array',
            'sort_order'   => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (RegistrationStep $step) {
            if (empty($step->slug)) {
                $step->slug = static::generateUniqueSlug($step->title);
            }
        });

        static::updating(function (RegistrationStep $step) {
            if ($step->isDirty('title') && !$step->isDirty('slug')) {
                $step->slug = static::generateUniqueSlug($step->title, $step->id);
            }
        });

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

    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $count = 1;

        while (static::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    public static function cacheVersion(): int
    {
        return (int) Cache::get(self::CACHE_VERSION_KEY, 1);
    }

    public static function clearCache(): void
    {
        if (Cache::has(self::CACHE_VERSION_KEY)) {
            Cache::increment(self::CACHE_VERSION_KEY);
        } else {
            Cache::forever(self::CACHE_VERSION_KEY, 2);
        }
    }

    /**
     * Illustration image public storage URL accessor.
     */
    public function getIllustrationImageUrlAttribute(): ?string
    {
        if (!$this->illustration_image) {
            return null;
        }

        return Storage::disk('public')->url($this->illustration_image);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeOrderedForPublic($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'asc');
    }
}
