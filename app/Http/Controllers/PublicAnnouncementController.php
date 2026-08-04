<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicAnnouncementController extends Controller
{
    /**
     * Display public listing of published announcements.
     * Cached using versioned cache key.
     */
    public function index(Request $request): View
    {
        $version = Announcement::cacheVersion();
        $cacheKey = Announcement::CACHE_PREFIX . 'v' . $version . '_index'
            . '_p' . ($request->get('priority', ''))
            . '_pg' . ($request->get('page', 1));

        $announcements = Cache::remember($cacheKey, 3600, function () use ($request) {
            $query = Announcement::published()
                ->orderedForPublic();

            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }

            return $query->paginate(10)->withQueryString();
        });

        return view('public.announcements.index', compact('announcements'));
    }

    /**
     * Display single published announcement detail by slug.
     */
    public function show(string $slug): View
    {
        $announcement = Announcement::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $recentAnnouncements = Announcement::published()
            ->where('id', '!=', $announcement->id)
            ->orderedForPublic()
            ->limit(3)
            ->get();

        return view('public.announcements.show', compact('announcement', 'recentAnnouncements'));
    }

    /**
     * Download attachment for a published announcement.
     * Increments downloads_count using withoutEvents to keep cache intact.
     */
    public function download(string $slug): StreamedResponse
    {
        $announcement = Announcement::published()
            ->where('slug', $slug)
            ->firstOrFail();

        if (!$announcement->attachment_file || !Storage::disk('public')->exists($announcement->attachment_file)) {
            abort(404, 'Berkas lampiran tidak ditemukan.');
        }

        // Increment downloads_count without firing model events
        Announcement::withoutEvents(function () use ($announcement) {
            $announcement->increment('downloads_count');
        });

        $downloadName = $announcement->attachment_name ?: basename($announcement->attachment_file);

        return Storage::disk('public')->download($announcement->attachment_file, $downloadName);
    }
}
