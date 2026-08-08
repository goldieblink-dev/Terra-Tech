<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\FileItem;
use App\Models\InformationPost;
use App\Models\RegistrationStep;
use App\Models\Timeline;
use Illuminate\Support\Facades\Cache;

class DashboardActivityService
{
    /**
     * Get latest activities across all content modules, cached for 60 seconds.
     */
    public function getLatestActivities(int $limit = 5): array
    {
        return Cache::remember('admin_dashboard_activities', 60, function () use ($limit) {
            return [
                'latest_information' => InformationPost::with('creator')
                    ->latest()
                    ->limit($limit)
                    ->get(['id', 'title', 'slug', 'status', 'created_by', 'created_at']),

                'latest_announcements' => Announcement::with('creator')
                    ->latest()
                    ->limit($limit)
                    ->get(['id', 'title', 'slug', 'status', 'priority', 'created_by', 'created_at']),

                'latest_timelines' => Timeline::with('creator')
                    ->latest()
                    ->limit($limit)
                    ->get(['id', 'title', 'status', 'start_date', 'end_date', 'created_by', 'created_at']),

                'latest_files' => FileItem::with(['creator', 'category'])
                    ->latest()
                    ->limit($limit)
                    ->get(['id', 'category_id', 'title', 'slug', 'status', 'downloads_count', 'created_by', 'created_at']),

                'latest_registration_steps' => RegistrationStep::with('creator')
                    ->latest()
                    ->limit($limit)
                    ->get(['id', 'title', 'slug', 'status', 'sort_order', 'created_by', 'created_at']),
            ];
        });
    }
}
