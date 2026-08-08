<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\StoreFileRequest;
use App\Http\Requests\Editor\UpdateFileRequest;
use App\Models\FileCategory;
use App\Models\FileItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    /**
     * Display a listing of files in CMS.
     */
    public function index(Request $request): View
    {
        $this->authorizeAccess();

        $query = FileItem::with(['category', 'creator'])
            ->orderBy('created_at', 'desc');

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

        $files = $query->paginate(10)->withQueryString();
        $categories = FileCategory::orderBy('name', 'asc')->get();

        return view('editor.files.index', compact('files', 'categories'));
    }

    /**
     * Show the form for creating a new file.
     */
    public function create(): View
    {
        $this->authorizeManage();

        $categories = FileCategory::orderBy('name', 'asc')->get();

        return view('editor.files.create', compact('categories'));
    }

    /**
     * Store a newly created file.
     */
    public function store(StoreFileRequest $request): RedirectResponse
    {
        $this->authorizeManage();

        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated) {
            $uploadedFile = $request->file('file');
            $filePath = $uploadedFile->store('files', 'public');

            $fileItem = FileItem::create([
                'category_id'   => $validated['category_id'],
                'title'         => $validated['title'],
                'description'   => $validated['description'] ?? null,
                'file_path'     => $filePath,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'mime_type'     => $uploadedFile->getMimeType(),
                'file_size'     => $uploadedFile->getSize(),
                'status'        => $validated['status'],
                'published_at'  => $validated['status'] === 'published' ? now() : null,
                'created_by'    => Auth::id(),
                'updated_by'    => Auth::id(),
            ]);

            Log::info('Berkas file berhasil diunggah', [
                'id'         => $fileItem->id,
                'title'      => $fileItem->title,
                'path'       => $filePath,
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->route('cms.files.index')
            ->with('success', 'File berhasil diunggah.');
    }

    /**
     * Display the specified file detail.
     */
    public function show(FileItem $fileItem): View
    {
        $this->authorizeAccess();

        $fileItem->load(['category', 'creator', 'updater']);

        return view('editor.files.show', compact('fileItem'));
    }

    /**
     * Show the form for editing the specified file.
     */
    public function edit(FileItem $fileItem): View
    {
        $this->authorizeManage();

        $categories = FileCategory::orderBy('name', 'asc')->get();

        return view('editor.files.edit', compact('fileItem', 'categories'));
    }

    /**
     * Update the specified file.
     */
    public function update(UpdateFileRequest $request, FileItem $fileItem): RedirectResponse
    {
        $this->authorizeManage();

        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated, $fileItem) {
            $updateData = [
                'category_id' => $validated['category_id'],
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? null,
                'status'      => $validated['status'],
                'updated_by'  => Auth::id(),
            ];

            if ($request->hasFile('file')) {
                // Delete old file from storage
                if ($fileItem->file_path && Storage::disk('public')->exists($fileItem->file_path)) {
                    Storage::disk('public')->delete($fileItem->file_path);
                }

                $uploadedFile = $request->file('file');
                $updateData['file_path']     = $uploadedFile->store('files', 'public');
                $updateData['original_name'] = $uploadedFile->getClientOriginalName();
                $updateData['mime_type']     = $uploadedFile->getMimeType();
                $updateData['file_size']     = $uploadedFile->getSize();
            }

            $fileItem->update($updateData);

            Log::info('Berkas file berhasil diperbarui', [
                'id'         => $fileItem->id,
                'title'      => $fileItem->title,
                'updated_by' => Auth::id(),
            ]);
        });

        return redirect()->route('cms.files.index')
            ->with('success', 'File berhasil diperbarui.');
    }

    /**
     * Remove the specified file.
     */
    public function destroy(FileItem $fileItem): RedirectResponse
    {
        $this->authorizeManage();

        DB::transaction(function () use ($fileItem) {
            $id = $fileItem->id;
            $title = $fileItem->title;

            $fileItem->delete();

            Log::info('Berkas file berhasil dihapus (soft delete)', [
                'id'         => $id,
                'title'      => $title,
                'deleted_by' => Auth::id(),
            ]);
        });

        return redirect()->route('cms.files.index')
            ->with('success', 'File berhasil dihapus.');
    }

    /**
     * Secure download file for CMS users.
     */
    public function download(FileItem $fileItem): StreamedResponse
    {
        $this->authorizeAccess();

        if (!Storage::disk('public')->exists($fileItem->file_path)) {
            abort(404, 'Berkas file tidak ditemukan di penyimpanan.');
        }

        DB::transaction(function () use ($fileItem) {
            $fileItem->increment('downloads_count');
        });

        return Storage::disk('public')->download($fileItem->file_path, $fileItem->original_name);
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
            abort(403, 'Anda tidak memiliki izin untuk mengelola file.');
        }
    }
}
