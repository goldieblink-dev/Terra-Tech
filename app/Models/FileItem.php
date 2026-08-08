<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'files';

    public const CACHE_PREFIX = 'published_files_';
    public const CACHE_VERSION_KEY = 'published_files_version';

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'downloads_count',
        'status',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'published_at'    => 'datetime',
            'file_size'       => 'integer',
            'downloads_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (FileItem $item) {
            if (empty($item->slug)) {
                $item->slug = static::generateUniqueSlug($item->title);
            }
            if ($item->status === 'published' && empty($item->published_at)) {
                $item->published_at = now();
            }
        });

        static::updating(function (FileItem $item) {
            if ($item->isDirty('title') && !$item->isDirty('slug')) {
                $item->slug = static::generateUniqueSlug($item->title, $item->id);
            }
            if ($item->isDirty('status') && $item->status === 'published' && empty($item->published_at)) {
                $item->published_at = now();
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
     * File public storage URL accessor.
     */
    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return Storage::disk('public')->url($this->file_path);
    }

    /**
     * Secure download route URL accessor.
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('public.files.download', $this->slug);
    }

    /**
     * Formatted file size accessor (B, KB, MB, GB).
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FileCategory::class, 'category_id');
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
        return $query->orderBy('published_at', 'desc')->orderBy('id', 'desc');
    }
}
