<?php

namespace App\Http\Controllers\Api\V1\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\StoreInformationPostRequest;
use App\Http\Requests\Editor\UpdateInformationPostRequest;
use App\Http\Resources\Api\V1\InformationResource;
use App\Models\InformationPost;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InformationController extends Controller
{
    use ApiResponse;

    /**
     * Display listing of information posts for CMS.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorizeAccess();

        $query = InformationPost::with(['category', 'creator'])
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $posts = $query->paginate(10);

        return $this->successResponse([
            'items'      => InformationResource::collection($posts),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page'    => $posts->lastPage(),
                'per_page'     => $posts->perPage(),
                'total'        => $posts->total(),
            ],
        ], 'Daftar informasi CMS berhasil diambil.');
    }

    /**
     * Store a newly created information post in CMS.
     */
    public function store(StoreInformationPostRequest $request): JsonResponse
    {
        $this->authorizeManage();

        $validated = $request->validated();

        $post = DB::transaction(function () use ($request, $validated) {
            $imagePath = null;
            if ($request->hasFile('featured_image')) {
                $imagePath = $request->file('featured_image')->store('information', 'public');
            }

            return InformationPost::create([
                'category_id'    => $validated['category_id'],
                'title'          => $validated['title'],
                'content'        => $validated['content'],
                'featured_image' => $imagePath,
                'status'         => $validated['status'],
                'published_at'   => $validated['status'] === 'published' ? now() : null,
                'created_by'     => Auth::id(),
                'updated_by'     => Auth::id(),
            ]);
        });

        $post->load(['category', 'creator']);

        return $this->successResponse(
            new InformationResource($post),
            'Informasi berhasil dibuat.',
            201
        );
    }

    /**
     * Display details of a specific information post in CMS.
     */
    public function show(int $id): JsonResponse
    {
        $this->authorizeAccess();

        $post = InformationPost::with(['category', 'creator', 'updater'])
            ->findOrFail($id);

        return $this->successResponse(
            new InformationResource($post),
            'Detail informasi CMS berhasil diambil.'
        );
    }

    /**
     * Update an information post in CMS.
     */
    public function update(UpdateInformationPostRequest $request, int $id): JsonResponse
    {
        $this->authorizeManage();

        $post = InformationPost::findOrFail($id);
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated, $post) {
            $updateData = [
                'category_id' => $validated['category_id'],
                'title'       => $validated['title'],
                'content'     => $validated['content'],
                'status'      => $validated['status'],
                'updated_by'  => Auth::id(),
            ];

            if ($validated['status'] === 'published' && !$post->published_at) {
                $updateData['published_at'] = now();
            }

            if ($request->hasFile('featured_image')) {
                if ($post->featured_image && Storage::disk('public')->exists($post->featured_image)) {
                    Storage::disk('public')->delete($post->featured_image);
                }
                $updateData['featured_image'] = $request->file('featured_image')->store('information', 'public');
            }

            $post->update($updateData);
        });

        $post->load(['category', 'creator', 'updater']);

        return $this->successResponse(
            new InformationResource($post),
            'Informasi berhasil diperbarui.'
        );
    }

    /**
     * Soft-delete an information post in CMS.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorizeManage();

        $post = InformationPost::findOrFail($id);

        DB::transaction(function () use ($post) {
            $post->delete();
        });

        return $this->successResponse(null, 'Informasi berhasil dihapus.');
    }

    private function authorizeAccess(): void
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole(['super_admin', 'admin', 'editor', 'operator'])) {
            abort(403, 'Akses ditolak.');
        }
    }

    private function authorizeManage(): void
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole(['super_admin', 'admin', 'editor'])) {
            abort(403, 'Anda tidak memiliki izin untuk mengelola informasi.');
        }
    }
}
