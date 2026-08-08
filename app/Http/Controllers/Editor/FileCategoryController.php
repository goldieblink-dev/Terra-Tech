<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\StoreFileCategoryRequest;
use App\Http\Requests\Editor\UpdateFileCategoryRequest;
use App\Models\FileCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class FileCategoryController extends Controller
{
    /**
     * Display a listing of file categories.
     */
    public function index(Request $request): View
    {
        $this->authorizeAccess();

        $query = FileCategory::withCount('files')
            ->orderBy('name', 'asc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->paginate(10)->withQueryString();

        return view('editor.file-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new file category.
     */
    public function create(): View
    {
        $this->authorizeManage();

        return view('editor.file-categories.create');
    }

    /**
     * Store a newly created file category.
     */
    public function store(StoreFileCategoryRequest $request): RedirectResponse
    {
        $this->authorizeManage();

        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $category = FileCategory::create([
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? null,
                'created_by'  => Auth::id(),
                'updated_by'  => Auth::id(),
            ]);

            Log::info('Kategori file berhasil dibuat', [
                'id'         => $category->id,
                'name'       => $category->name,
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->route('cms.file-categories.index')
            ->with('success', 'Kategori file berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified file category.
     */
    public function edit(FileCategory $fileCategory): View
    {
        $this->authorizeManage();

        return view('editor.file-categories.edit', compact('fileCategory'));
    }

    /**
     * Update the specified file category.
     */
    public function update(UpdateFileCategoryRequest $request, FileCategory $fileCategory): RedirectResponse
    {
        $this->authorizeManage();

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $fileCategory) {
            $fileCategory->update([
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? null,
                'updated_by'  => Auth::id(),
            ]);

            Log::info('Kategori file berhasil diperbarui', [
                'id'         => $fileCategory->id,
                'name'       => $fileCategory->name,
                'updated_by' => Auth::id(),
            ]);
        });

        return redirect()->route('cms.file-categories.index')
            ->with('success', 'Kategori file berhasil diperbarui.');
    }

    /**
     * Remove the specified file category.
     */
    public function destroy(FileCategory $fileCategory): RedirectResponse
    {
        $this->authorizeManage();

        DB::transaction(function () use ($fileCategory) {
            $categoryId = $fileCategory->id;
            $categoryName = $fileCategory->name;

            $fileCategory->delete();

            Log::info('Kategori file berhasil dihapus', [
                'id'         => $categoryId,
                'name'       => $categoryName,
                'deleted_by' => Auth::id(),
            ]);
        });

        return redirect()->route('cms.file-categories.index')
            ->with('success', 'Kategori file berhasil dihapus.');
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
            abort(403, 'Anda tidak memiliki izin untuk mengelola kategori file.');
        }
    }
}
