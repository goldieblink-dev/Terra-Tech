<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\InformationResource;
use App\Models\InformationPost;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicInformationController extends Controller
{
    use ApiResponse;

    /**
     * Display listing of published information posts.
     */
    public function index(Request $request): JsonResponse
    {
        $version  = InformationPost::cacheVersion();
        $cacheKey = InformationPost::CACHE_PREFIX . 'v' . $version . '_api_index'
            . '_cat_' . ($request->get('category', 'all'))
            . '_q_'   . md5($request->get('search', ''))
            . '_pg_'  . ($request->get('page', 1));

        $posts = Cache::remember($cacheKey, 3600, function () use ($request) {
            $query = InformationPost::published()->with(['category', 'creator']);

            if ($request->filled('category')) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('slug', $request->category);
                });
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
            'items'      => InformationResource::collection($posts),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page'    => $posts->lastPage(),
                'per_page'     => $posts->perPage(),
                'total'        => $posts->total(),
            ],
        ], 'Daftar informasi publik berhasil diambil.');
    }

    /**
     * Display details of a single published information post by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $post = InformationPost::published()
            ->where('slug', $slug)
            ->with(['category', 'creator'])
            ->firstOrFail();

        $post->increment('views_count');

        return $this->successResponse(
            new InformationResource($post),
            'Detail informasi berhasil diambil.'
        );
    }
}
