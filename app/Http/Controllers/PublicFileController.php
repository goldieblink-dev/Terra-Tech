<?php

namespace App\Http\Controllers;

use App\Models\FileCategory;
use App\Models\FileItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicFileController extends Controller
{
    /**
     * Display public listing of published files.
     * Cached using versioned cache key.
     */
    public function index(Request $request): View
    {
        $version = FileItem::cacheVersion();
        $cacheKey = FileItem::CACHE_PREFIX . 'v' . $version . '_index'
            . '_cat' . ($request->get('category', 'all'))
            . '_q' . md5($request->get('search', ''))
            . '_pg' . ($request->get('page', 1));

        $files = Cache::remember($cacheKey, 3600, function () use ($request) {
            $query = FileItem::published()
                ->with('category')
                ->orderedForPublic();

            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where('title', 'like', "%{$search}%");
            }

            if ($request->filled('category')) {
                $categorySlug = $request->category;
                $query->whereHas('category', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            }

            return $query->paginate(10)->withQueryString();
        });

        $categories = FileCategory::orderBy('name', 'asc')->get();

        return view('public.files.index', compact('files', 'categories'));
    }

    /**
     * Display single published file detail page by slug.
     */
    public function show(string $slug): View
    {
        $fileItem = FileItem::published()
            ->with(['category', 'creator'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.files.show', compact('fileItem'));
    }

    /**
     * Secure download of published file by slug.
     */
    public function download(string $slug): StreamedResponse
    {
        $fileItem = FileItem::published()
            ->where('slug', $slug)
            ->firstOrFail();

        if (!Storage::disk('public')->exists($fileItem->file_path)) {
            abort(404, 'Berkas file tidak ditemukan.');
        }

        DB::transaction(function () use ($fileItem) {
            $fileItem->increment('downloads_count');
        });

        return Storage::disk('public')->download($fileItem->file_path, $fileItem->original_name);
    }
}
