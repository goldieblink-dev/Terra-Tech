<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\FileResource;
use App\Models\FileItem;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicFileController extends Controller
{
    use ApiResponse;

    /**
     * Display listing of published files.
     */
    public function index(Request $request): JsonResponse
    {
        $version  = FileItem::cacheVersion();
        $cacheKey = FileItem::CACHE_PREFIX . 'v' . $version . '_api_index'
            . '_cat_' . ($request->get('category', 'all'))
            . '_q_'   . md5($request->get('search', ''))
            . '_pg_'  . ($request->get('page', 1));

        $files = Cache::remember($cacheKey, 3600, function () use ($request) {
            $query = FileItem::published()->with('category');

            if ($request->filled('category')) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('slug', $request->category);
                });
            }

            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where('title', 'like', "%{$search}%");
            }

            return $query->latest('published_at')->paginate(10)->withQueryString();
        });

        return $this->successResponse([
            'items'      => FileResource::collection($files),
            'pagination' => [
                'current_page' => $files->currentPage(),
                'last_page'    => $files->lastPage(),
                'per_page'     => $files->perPage(),
                'total'        => $files->total(),
            ],
        ], 'Daftar berkas dokumen publik berhasil diambil.');
    }

    /**
     * Display details of a single published file item by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $file = FileItem::published()
            ->where('slug', $slug)
            ->with('category')
            ->firstOrFail();

        return $this->successResponse(
            new FileResource($file),
            'Detail berkas dokumen berhasil diambil.'
        );
    }
}
