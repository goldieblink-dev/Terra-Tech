<?php

namespace App\Http\Controllers\Api\V1\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\StoreAnnouncementRequest;
use App\Http\Requests\Editor\UpdateAnnouncementRequest;
use App\Http\Resources\Api\V1\AnnouncementResource;
use App\Models\Announcement;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    use ApiResponse;

    /**
     * Display listing of announcements for CMS.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorizeAccess();

        $query = Announcement::with('creator')->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $announcements = $query->paginate(10);

        return $this->successResponse([
            'items'      => AnnouncementResource::collection($announcements),
            'pagination' => [
                'current_page' => $announcements->currentPage(),
                'last_page'    => $announcements->lastPage(),
                'per_page'     => $announcements->perPage(),
                'total'        => $announcements->total(),
            ],
        ], 'Daftar pengumuman CMS berhasil diambil.');
    }

    /**
     * Store a newly created announcement in CMS.
     */
    public function store(StoreAnnouncementRequest $request): JsonResponse
    {
        $this->authorizeManage();

        $validated = $request->validated();

        $announcement = DB::transaction(function () use ($request, $validated) {
            $filePath = null;
            $fileName = null;

            if ($request->hasFile('attachment_file')) {
                $file = $request->file('attachment_file');
                $fileName = $file->getClientOriginalName();
                $filePath = $file->store('announcements', 'public');
            }

            return Announcement::create([
                'title'           => $validated['title'],
                'content'         => $validated['content'],
                'attachment_file' => $filePath,
                'attachment_name' => $fileName,
                'priority'        => $validated['priority'] ?? 'normal',
                'status'          => $validated['status'],
                'published_at'    => $validated['status'] === 'published' ? now() : null,
                'created_by'      => Auth::id(),
                'updated_by'      => Auth::id(),
            ]);
        });

        $announcement->load('creator');

        return $this->successResponse(
            new AnnouncementResource($announcement),
            'Pengumuman berhasil dibuat.',
            201
        );
    }

    /**
     * Display details of a specific announcement in CMS.
     */
    public function show(int $id): JsonResponse
    {
        $this->authorizeAccess();

        $announcement = Announcement::with(['creator', 'updater'])->findOrFail($id);

        return $this->successResponse(
            new AnnouncementResource($announcement),
            'Detail pengumuman CMS berhasil diambil.'
        );
    }

    /**
     * Update an announcement in CMS.
     */
    public function update(UpdateAnnouncementRequest $request, int $id): JsonResponse
    {
        $this->authorizeManage();

        $announcement = Announcement::findOrFail($id);
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated, $announcement) {
            $updateData = [
                'title'      => $validated['title'],
                'content'    => $validated['content'],
                'priority'   => $validated['priority'] ?? 'normal',
                'status'     => $validated['status'],
                'updated_by' => Auth::id(),
            ];

            if ($validated['status'] === 'published' && !$announcement->published_at) {
                $updateData['published_at'] = now();
            }

            if ($request->hasFile('attachment_file')) {
                if ($announcement->attachment_file && Storage::disk('public')->exists($announcement->attachment_file)) {
                    Storage::disk('public')->delete($announcement->attachment_file);
                }

                $file = $request->file('attachment_file');
                $updateData['attachment_name'] = $file->getClientOriginalName();
                $updateData['attachment_file'] = $file->store('announcements', 'public');
            }

            $announcement->update($updateData);
        });

        $announcement->load(['creator', 'updater']);

        return $this->successResponse(
            new AnnouncementResource($announcement),
            'Pengumuman berhasil diperbarui.'
        );
    }

    /**
     * Soft-delete an announcement in CMS.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorizeManage();

        $announcement = Announcement::findOrFail($id);

        DB::transaction(function () use ($announcement) {
            $announcement->delete();
        });

        return $this->successResponse(null, 'Pengumuman berhasil dihapus.');
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
            abort(403, 'Anda tidak memiliki izin untuk mengelola pengumuman.');
        }
    }
}
