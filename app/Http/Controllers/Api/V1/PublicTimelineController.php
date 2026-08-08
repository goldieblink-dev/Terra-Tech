<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TimelineResource;
use App\Models\Timeline;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicTimelineController extends Controller
{
    use ApiResponse;

    /**
     * Display listing of published timelines ordered for public.
     */
    public function index(Request $request): JsonResponse
    {
        $version  = Timeline::cacheVersion();
        $cacheKey = Timeline::CACHE_PREFIX . 'v' . $version . '_api_index'
            . '_pg_' . ($request->get('page', 1));

        $timelines = Cache::remember($cacheKey, 3600, function () {
            return Timeline::published()
                ->orderedForPublic()
                ->paginate(10)
                ->withQueryString();
        });

        return $this->successResponse([
            'items'      => TimelineResource::collection($timelines),
            'pagination' => [
                'current_page' => $timelines->currentPage(),
                'last_page'    => $timelines->lastPage(),
                'per_page'     => $timelines->perPage(),
                'total'        => $timelines->total(),
            ],
        ], 'Daftar timeline publik berhasil diambil.');
    }

    /**
     * Display details of a single published timeline item by ID.
     */
    public function show(int $id): JsonResponse
    {
        $timeline = Timeline::published()
            ->where('id', $id)
            ->firstOrFail();

        return $this->successResponse(
            new TimelineResource($timeline),
            'Detail timeline berhasil diambil.'
        );
    }
}
