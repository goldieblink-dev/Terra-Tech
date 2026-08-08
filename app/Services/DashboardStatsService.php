<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\FileItem;
use App\Models\InformationPost;
use App\Models\RegistrationStep;
use App\Models\Timeline;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    /**
     * Get all dashboard statistics, cached for 60 seconds.
     */
    public function getStats(): array
    {
        return Cache::remember('admin_dashboard_stats', 60, function () {
            return [
                'total_users'                => User::count(),
                'total_information_posts'     => InformationPost::count(),
                'total_announcements'         => Announcement::count(),
                'total_timelines'             => Timeline::count(),
                'total_files'                => FileItem::count(),
                'total_registration_steps'    => RegistrationStep::count(),
                'total_file_downloads'        => (int) FileItem::sum('downloads_count'),
                'total_announcement_downloads'=> (int) Announcement::sum('downloads_count'),
            ];
        });
    }

    /**
     * Get draft counts for all content modules, cached for 60 seconds.
     */
    public function getDraftCounts(): array
    {
        return Cache::remember('admin_dashboard_drafts', 60, function () {
            return [
                'draft_information'        => InformationPost::where('status', 'draft')->count(),
                'draft_announcements'      => Announcement::where('status', 'draft')->count(),
                'draft_timelines'          => Timeline::where('status', 'draft')->count(),
                'draft_files'              => FileItem::where('status', 'draft')->count(),
                'draft_registration_steps' => RegistrationStep::where('status', 'draft')->count(),
            ];
        });
    }

    /**
     * Get top downloaded files, cached for 60 seconds.
     */
    public function getTopDownloadedFiles(int $limit = 5): \Illuminate\Support\Collection
    {
        return Cache::remember('admin_dashboard_top_files', 60, function () use ($limit) {
            return FileItem::where('downloads_count', '>', 0)
                ->orderByDesc('downloads_count')
                ->limit($limit)
                ->get(['id', 'title', 'original_name', 'downloads_count', 'mime_type']);
        });
    }

    /**
     * Get top viewed information posts, cached for 60 seconds.
     */
    public function getTopViewedPosts(int $limit = 5): \Illuminate\Support\Collection
    {
        return Cache::remember('admin_dashboard_top_posts', 60, function () use ($limit) {
            return InformationPost::where('views_count', '>', 0)
                ->orderByDesc('views_count')
                ->limit($limit)
                ->get(['id', 'title', 'slug', 'views_count']);
        });
    }

    /**
     * Get system health information.
     */
    public function getSystemHealth(): array
    {
        $storageUsed = 0;
        $storagePath = storage_path('app/public');
        if (is_dir($storagePath)) {
            $storageUsed = $this->getDirectorySize($storagePath);
        }

        $dbStatus = 'Connected';
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbStatus = 'Error: ' . $e->getMessage();
        }

        return [
            'storage_used'     => $storageUsed,
            'storage_readable' => $this->formatBytes($storageUsed),
            'cache_driver'     => config('cache.default'),
            'db_status'        => $dbStatus,
            'laravel_version'  => app()->version(),
            'php_version'      => PHP_VERSION,
        ];
    }

    /**
     * Calculate the total size of a directory recursively.
     */
    private function getDirectorySize(string $path): int
    {
        $size = 0;
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }

        return $size;
    }

    /**
     * Format bytes into a human-readable size string.
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
