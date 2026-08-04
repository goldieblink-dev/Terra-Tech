<?php

namespace App\Http\Controllers;

use App\Models\Timeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PublicTimelineController extends Controller
{
    /**
     * Display public listing of published timeline entries.
     * Cached using versioned cache key.
     */
    public function index(Request $request): View
    {
        $version = Timeline::cacheVersion();
        $cacheKey = Timeline::CACHE_PREFIX . 'v' . $version . '_index'
            . '_pg' . ($request->get('page', 1));

        $timelines = Cache::remember($cacheKey, 3600, function () {
            return Timeline::published()
                ->orderedForPublic()
                ->paginate(10)
                ->withQueryString();
        });

        return view('public.timelines.index', compact('timelines'));
    }

    /**
     * Display single published timeline entry detail.
     * Uses route model binding: /timeline/{timeline}
     */
    public function show(Timeline $timeline): View
    {
        if ($timeline->status !== 'published') {
            abort(404);
        }

        return view('public.timelines.show', compact('timeline'));
    }
}
