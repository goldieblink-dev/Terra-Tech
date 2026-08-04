<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\StoreInformationPostRequest;
use App\Http\Requests\Editor\UpdateInformationPostRequest;
use App\Models\InformationCategory;
use App\Models\InformationPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InformationPostController extends Controller
{
    /**
     * Display a listing of information posts.
     */
    public function index(Request $request): View
    {
        $this->authorizeAccess();

        $query = InformationPost::with(['category', 'creator'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $posts = $query->paginate(10)->withQueryString();
        $categories = InformationCategory::orderBy('name')->get();

        return view('editor.information.index', compact('posts', 'categories'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create(): View
    {
        $this->authorizeManage();

        $categories = InformationCategory::orderBy('name')->get();

        return view('editor.information.create', compact('categories'));
    }

    /**
     * Store a newly created information post.
     */
    public function store(StoreInformationPostRequest $request): RedirectResponse
    {
        $this->authorizeManage();

        DB::transaction(function () use ($request) {
            $imagePath = null;
            if ($request->hasFile('featured_image') && $request->file('featured_image')->isValid()) {
                $file = $request->file('featured_image');
                $filename = 'info_' . Str::uuid() . '.' . strtolower($file->getClientOriginalExtension());
                $imagePath = $file->storeAs('information', $filename, 'public');
            }

            $publishedAt = null;
            if ($request->status === 'published') {
                $publishedAt = now();
            }

            InformationPost::create([
                'category_id'        => $request->category_id,
                'title'              => $request->title,
                'slug'               => InformationPost::generateUniqueSlug($request->title),
                'excerpt'            => $request->excerpt,
                'content'            => $request->content,
                'featured_image'     => $imagePath,
                'featured_image_alt' => $request->featured_image_alt,
                'meta_title'         => $request->meta_title,
                'meta_description'   => $request->meta_description,
                'status'             => $request->status,
                'published_at'       => $publishedAt,
                'created_by'         => Auth::id(),
                'updated_by'         => Auth::id(),
            ]);

            InformationPost::clearCache();
        });

        Log::info('information_post_created', [
            'title'      => $request->title,
            'status'     => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('cms.information.index')
            ->with('success', 'Artikel informasi berhasil ditambahkan.');
    }

    /**
     * Show a single post for CMS preview.
     */
    public function show(InformationPost $informationPost): View
    {
        $this->authorizeAccess();

        $informationPost->load('category', 'creator', 'updater');

        return view('editor.information.show', compact('informationPost'));
    }

    /**
     * Show the form for editing an existing post.
     */
    public function edit(InformationPost $informationPost): View
    {
        $this->authorizeManage();

        $categories = InformationCategory::orderBy('name')->get();

        return view('editor.information.edit', compact('informationPost', 'categories'));
    }

    /**
     * Update the specified information post.
     */
    public function update(UpdateInformationPostRequest $request, InformationPost $informationPost): RedirectResponse
    {
        $this->authorizeManage();

        DB::transaction(function () use ($request, $informationPost) {
            $imagePath = $informationPost->featured_image;

            if ($request->hasFile('featured_image') && $request->file('featured_image')->isValid()) {
                // Delete old image from disk
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }

                $file = $request->file('featured_image');
                $filename = 'info_' . Str::uuid() . '.' . strtolower($file->getClientOriginalExtension());
                $imagePath = $file->storeAs('information', $filename, 'public');
            }

            // Set published_at only when transitioning from draft to published
            $publishedAt = $informationPost->published_at;
            if ($request->status === 'published' && $informationPost->status === 'draft') {
                $publishedAt = now();
            } elseif ($request->status === 'draft') {
                $publishedAt = null;
            }

            $informationPost->update([
                'category_id'        => $request->category_id,
                'title'              => $request->title,
                'slug'               => InformationPost::generateUniqueSlug($request->title, $informationPost->id),
                'excerpt'            => $request->excerpt,
                'content'            => $request->content,
                'featured_image'     => $imagePath,
                'featured_image_alt' => $request->featured_image_alt,
                'meta_title'         => $request->meta_title,
                'meta_description'   => $request->meta_description,
                'status'             => $request->status,
                'published_at'       => $publishedAt,
                'updated_by'         => Auth::id(),
            ]);

            InformationPost::clearCache();
        });

        Log::info('information_post_updated', [
            'id'         => $informationPost->id,
            'status'     => $request->status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('cms.information.index')
            ->with('success', 'Artikel informasi berhasil diperbarui.');
    }

    /**
     * Soft delete the specified information post.
     */
    public function destroy(InformationPost $informationPost): RedirectResponse
    {
        $this->authorizeManage();

        $informationPost->delete();
        InformationPost::clearCache();

        Log::info('information_post_deleted', [
            'id'         => $informationPost->id,
            'deleted_by' => Auth::id(),
        ]);

        return redirect()->route('cms.information.index')
            ->with('success', 'Artikel informasi berhasil dihapus.');
    }

    /**
     * Allow super_admin, admin, editor, and operator to view listings.
     */
    private function authorizeAccess(): void
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole(['super_admin', 'admin', 'editor', 'operator'])) {
            abort(403, 'Akses ditolak.');
        }
    }

    /**
     * Only super_admin, admin, and editor may create/update/delete.
     */
    private function authorizeManage(): void
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole(['super_admin', 'admin', 'editor'])) {
            abort(403, 'Anda tidak memiliki izin untuk mengelola konten informasi.');
        }
    }
}
