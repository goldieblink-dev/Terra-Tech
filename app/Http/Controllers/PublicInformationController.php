<?php

namespace App\Http\Controllers;

use App\Models\InformationCategory;
use App\Models\InformationPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PublicInformationController extends Controller
{
    /**
     * Display public listing of published information posts.
     */
    public function index(Request $request): View
    {
        $version = InformationPost::cacheVersion();
        $cacheKey = InformationPost::CACHE_PREFIX . 'v' . $version . '_index'
            . '_c' . ($request->get('category', ''))
            . '_p' . ($request->get('page', 1));

        $posts = Cache::remember($cacheKey, 3600, function () use ($request) {
            $query = InformationPost::with('category')
                ->published()
                ->orderBy('published_at', 'desc');

            if ($request->filled('category')) {
                $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
            }

            return $query->paginate(10)->withQueryString();
        });

        $categories = InformationCategory::has('posts')->orderBy('name')->get();

        return view('public.information.index', compact('posts', 'categories'));
    }

    /**
     * Display a single published information post by slug.
     * Increments views_count.
     */
    public function show(string $slug): View
    {
        $post = InformationPost::with('category')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views_count directly without firing model events to avoid cache clear
        InformationPost::withoutEvents(function () use ($post) {
            $post->increment('views_count');
        });

        $related = InformationPost::with('category')
            ->published()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return view('public.information.show', compact('post', 'related'));
    }
}
