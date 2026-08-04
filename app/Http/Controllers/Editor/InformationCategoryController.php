<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\StoreCategoryRequest;
use App\Http\Requests\Editor\UpdateCategoryRequest;
use App\Models\InformationCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class InformationCategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(): View
    {
        $this->authorizeAccess();

        $categories = InformationCategory::withCount('posts')
            ->orderBy('name')
            ->paginate(10);

        return view('editor.information-categories.index', compact('categories'));
    }

    /**
     * Show form to create a new category.
     */
    public function create(): View
    {
        $this->authorizeManage();

        return view('editor.information-categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->authorizeManage();

        DB::transaction(function () use ($request) {
            InformationCategory::create([
                'name'        => $request->name,
                'slug'        => InformationCategory::generateUniqueSlug($request->name),
                'description' => $request->description,
                'created_by'  => Auth::id(),
                'updated_by'  => Auth::id(),
            ]);
        });

        Log::info('information_category_created', [
            'name'       => $request->name,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('cms.information-categories.index')
            ->with('success', 'Kategori informasi berhasil ditambahkan.');
    }

    /**
     * Show form to edit a category.
     */
    public function edit(InformationCategory $informationCategory): View
    {
        $this->authorizeManage();

        return view('editor.information-categories.edit', compact('informationCategory'));
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateCategoryRequest $request, InformationCategory $informationCategory): RedirectResponse
    {
        $this->authorizeManage();

        DB::transaction(function () use ($request, $informationCategory) {
            $informationCategory->update([
                'name'        => $request->name,
                'slug'        => InformationCategory::generateUniqueSlug($request->name, $informationCategory->id),
                'description' => $request->description,
                'updated_by'  => Auth::id(),
            ]);
        });

        Log::info('information_category_updated', [
            'id'         => $informationCategory->id,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('cms.information-categories.index')
            ->with('success', 'Kategori informasi berhasil diperbarui.');
    }

    /**
     * Delete a category (soft delete).
     */
    public function destroy(InformationCategory $informationCategory): RedirectResponse
    {
        $this->authorizeManage();

        $informationCategory->delete();

        Log::info('information_category_deleted', [
            'id'         => $informationCategory->id,
            'deleted_by' => Auth::id(),
        ]);

        return redirect()->route('cms.information-categories.index')
            ->with('success', 'Kategori informasi berhasil dihapus.');
    }

    /**
     * Allow super_admin, admin, editor, and operator to access listings.
     */
    private function authorizeAccess(): void
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole(['super_admin', 'admin', 'editor', 'operator'])) {
            abort(403, 'Akses ditolak.');
        }
    }

    /**
     * Only super_admin, admin, and editor can manage categories.
     */
    private function authorizeManage(): void
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole(['super_admin', 'admin', 'editor'])) {
            abort(403, 'Anda tidak memiliki izin untuk mengelola kategori informasi.');
        }
    }
}
