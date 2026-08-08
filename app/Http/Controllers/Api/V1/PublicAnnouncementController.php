<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\AnnouncementResource;
use App\Models\Announcement;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicAnnouncementController extends Controller
{
    use ApiResponse;

    /**
     * Display listing of published announcements.
     */
    public function index(Request $request): JsonResponse
    {
        $version  = Announcement::cacheVersion();
        $cacheKey = Announcement::CACHE_PREFIX . 'v' . $version . '_api_index'
            . '_prio_' . ($request->get('priority', 'all'))
            . '_q_'    . md5($request->get('search', ''))
            . '_pg_'   . ($request->get('page', 1));

        $announcements = Cache::remember($cacheKey, 3600, function () use ($request) {
            $query = Announcement::published()->with('creator');

            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }

            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            }

            return $query->latest('published_at')->paginate(10)->withQueryString();
        });

        return $this->successResponse([
            'items'      => AnnouncementResource::collection($announcements),
            'pagination' => [
                'current_page' => $announcements->currentPage(),
                'last_page'    => $announcements->lastPage(),
                'per_page'     => $announcements->perPage(),
                'total'        => $announcements->total(),
            ],
        ], 'Daftar pengumuman publik berhasil diambil.');
    }

    /**
     * Display details of a single published announcement by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $announcement = Announcement::published()
            ->where('slug', $slug)
            ->with('creator')
            ->firstOrFail();

        return $this->successResponse(
            new AnnouncementResource($announcement),
            'Detail pengumuman berhasil diambil.'
        );
    }
}
