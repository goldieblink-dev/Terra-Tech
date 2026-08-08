<?php

namespace App\Http\Controllers;

use App\Models\RegistrationStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PublicRegistrationFlowController extends Controller
{
    /**
     * Display public listing of published registration steps.
     * Cached using versioned cache key.
     */
    public function index(Request $request): View
    {
        $version = RegistrationStep::cacheVersion();
        $cacheKey = RegistrationStep::CACHE_PREFIX . 'v' . $version . '_index'
            . '_pg' . ($request->get('page', 1));

        $steps = Cache::remember($cacheKey, 3600, function () {
            return RegistrationStep::published()
                ->orderedForPublic()
                ->paginate(10)
                ->withQueryString();
        });

        return view('public.registration-flow.index', compact('steps'));
    }

    /**
     * Display single published registration step detail page by slug.
     */
    public function show(string $slug): View
    {
        $step = RegistrationStep::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.registration-flow.show', compact('step'));
    }
}
