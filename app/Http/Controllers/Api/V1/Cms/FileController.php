<?php

namespace App\Http\Controllers\Api\V1\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\StoreFileRequest;
use App\Http\Requests\Editor\UpdateFileRequest;
use App\Http\Resources\Api\V1\FileResource;
use App\Models\FileItem;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    use ApiResponse;

    /**
     * Display listing of files for CMS.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorizeAccess();

        $query = FileItem::with(['category', 'creator'])->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $files = $query->paginate(10);

        return $this->successResponse([
            'items'      => FileResource::collection($files),
            'pagination' => [
                'current_page' => $files->currentPage(),
                'last_page'    => $files->lastPage(),
                'per_page'     => $files->perPage(),
                'total'        => $files->total(),
            ],
        ], 'Daftar berkas dokumen CMS berhasil diambil.');
    }

    /**
     * Store a newly created file item in CMS.
     */
    public function store(StoreFileRequest $request): JsonResponse
    {
        $this->authorizeManage();

        $validated = $request->validated();

        $fileItem = DB::transaction(function () use ($request, $validated) {
            $uploadedFile = $request->file('file');
            $originalName = $uploadedFile->getClientOriginalName();
            $mimeType     = $uploadedFile->getMimeType() ?: $uploadedFile->getClientMimeType();
            $fileSize     = $uploadedFile->getSize();
            $filePath     = $uploadedFile->store('files', 'public');

            return FileItem::create([
                'category_id'   => $validated['category_id'],
                'title'         => $validated['title'],
                'description'   => $validated['description'] ?? null,
                'file_path'     => $filePath,
                'original_name' => $originalName,
                'mime_type'     => $mimeType,
                'file_size'     => $fileSize,
                'status'        => $validated['status'],
                'published_at'  => $validated['status'] === 'published' ? now() : null,
                'created_by'    => Auth::id(),
                'updated_by'    => Auth::id(),
            ]);
        });

        $fileItem->load(['category', 'creator']);

        return $this->successResponse(
            new FileResource($fileItem),
            'Berkas dokumen berhasil diunggah.',
            201
        );
    }

    /**
     * Display details of a specific file item in CMS.
     */
    public function show(int $id): JsonResponse
    {
        $this->authorizeAccess();

        $fileItem = FileItem::with(['category', 'creator', 'updater'])->findOrFail($id);

        return $this->successResponse(
            new FileResource($fileItem),
            'Detail berkas dokumen CMS berhasil diambil.'
        );
    }

    /**
     * Update a file item in CMS.
     */
    public function update(UpdateFileRequest $request, int $id): JsonResponse
    {
        $this->authorizeManage();

        $fileItem = FileItem::findOrFail($id);
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated, $fileItem) {
            $updateData = [
                'category_id' => $validated['category_id'],
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? null,
                'status'      => $validated['status'],
                'updated_by'  => Auth::id(),
            ];

            if ($validated['status'] === 'published' && !$fileItem->published_at) {
                $updateData['published_at'] = now();
            }

            if ($request->hasFile('file')) {
                if ($fileItem->file_path && Storage::disk('public')->exists($fileItem->file_path)) {
                    Storage::disk('public')->delete($fileItem->file_path);
                }

                $uploadedFile = $request->file('file');
                $updateData['original_name'] = $uploadedFile->getClientOriginalName();
                $updateData['mime_type']     = $uploadedFile->getMimeType() ?: $uploadedFile->getClientMimeType();
                $updateData['file_size']     = $uploadedFile->getSize();
                $updateData['file_path']     = $uploadedFile->store('files', 'public');
            }

            $fileItem->update($updateData);
        });

        $fileItem->load(['category', 'creator', 'updater']);

        return $this->successResponse(
            new FileResource($fileItem),
            'Berkas dokumen berhasil diperbarui.'
        );
    }

    /**
     * Soft-delete a file item in CMS.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorizeManage();

        $fileItem = FileItem::findOrFail($id);

        DB::transaction(function () use ($fileItem) {
            $fileItem->delete();
        });

        return $this->successResponse(null, 'Berkas dokumen berhasil dihapus.');
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
            abort(403, 'Anda tidak memiliki izin untuk mengelola berkas dokumen.');
        }
    }
}
