<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CompanyProfile extends Model
{
    use HasFactory;

    /**
     * Cache key for singleton company profile.
     */
    public const CACHE_KEY = 'company_profile';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_name',
        'tagline',
        'description',
        'email',
        'phone',
        'address',
        'logo_path',
        'favicon_path',
        'facebook_url',
        'instagram_url',
        'linkedin_url',
        'youtube_url',
        'updated_by',
    ];

    /**
     * Get the cached singleton instance of CompanyProfile.
     */
    public static function getSingleton(): ?self
    {
        return Cache::remember(self::CACHE_KEY, 86400, function () {
            return self::first();
        });
    }

    /**
     * Clear the company profile cache.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Accessor for full Logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo_path && Storage::disk('public')->exists($this->logo_path)) {
            return asset('storage/' . $this->logo_path);
        }

        return null;
    }

    /**
     * Accessor for full Favicon URL.
     */
    public function getFaviconUrlAttribute(): ?string
    {
        if ($this->favicon_path && Storage::disk('public')->exists($this->favicon_path)) {
            return asset('storage/' . $this->favicon_path);
        }

        return null;
    }

    /**
     * Relationship for updater user.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
