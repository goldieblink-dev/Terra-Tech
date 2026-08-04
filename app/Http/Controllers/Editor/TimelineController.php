<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\StoreTimelineRequest;
use App\Http\Requests\Editor\UpdateTimelineRequest;
use App\Models\Timeline;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TimelineController extends Controller
{
    /**
     * Display a listing of timeline entries in CMS.
     */
    public function index(Request $request): View
    {
        $this->authorizeAccess();

        $query = Timeline::with(['creator'])
            ->orderBy('sort_order', 'asc')
            ->orderBy('start_date', 'desc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $timelines = $query->paginate(10)->withQueryString();

        return view('editor.timelines.index', compact('timelines'));
    }

    /**
     * Show form to create a new timeline entry.
     */
    public function create(): View
    {
        $this->authorizeManage();

        return view('editor.timelines.create');
    }

    /**
     * Store a newly created timeline entry.
     */
    public function store(StoreTimelineRequest $request): RedirectResponse
    {
        $this->authorizeManage();

        DB::transaction(function () use ($request) {
            Timeline::create([
                'title'       => $request->title,
                'description' => $request->description,
                'start_date'  => $request->start_date,
                'end_date'    => $request->end_date,
                'location'    => $request->location,
                'color'       => $request->color ?: '#2563eb',
                'icon'        => $request->icon,
                'status'      => $request->status,
                'sort_order'  => $request->sort_order ?? 0,
                'created_by'  => Auth::id(),
                'updated_by'  => Auth::id(),
            ]);
        });

        Log::info('timeline_created', [
            'title'      => $request->title,
            'status'     => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('cms.timelines.index')
            ->with('success', 'Agenda timeline berhasil ditambahkan.');
    }

    /**
     * Show a single timeline entry detail in CMS.
     */
    public function show(Timeline $timeline): View
    {
        $this->authorizeAccess();

        $timeline->load('creator', 'updater');

        return view('editor.timelines.show', compact('timeline'));
    }

    /**
     * Show form to edit an existing timeline entry.
     */
    public function edit(Timeline $timeline): View
    {
        $this->authorizeManage();

        return view('editor.timelines.edit', compact('timeline'));
    }

    /**
     * Update the specified timeline entry.
     */
    public function update(UpdateTimelineRequest $request, Timeline $timeline): RedirectResponse
    {
        $this->authorizeManage();

        DB::transaction(function () use ($request, $timeline) {
            $timeline->update([
                'title'       => $request->title,
                'description' => $request->description,
                'start_date'  => $request->start_date,
                'end_date'    => $request->end_date,
                'location'    => $request->location,
                'color'       => $request->color ?: '#2563eb',
                'icon'        => $request->icon,
                'status'      => $request->status,
                'sort_order'  => $request->sort_order ?? 0,
                'updated_by'  => Auth::id(),
            ]);
        });

        Log::info('timeline_updated', [
            'id'         => $timeline->id,
            'status'     => $request->status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('cms.timelines.index')
            ->with('success', 'Agenda timeline berhasil diperbarui.');
    }

    /**
     * Soft delete the specified timeline entry.
     */
    public function destroy(Timeline $timeline): RedirectResponse
    {
        $this->authorizeManage();

        $timeline->delete();

        Log::info('timeline_deleted', [
            'id'         => $timeline->id,
            'deleted_by' => Auth::id(),
        ]);

        return redirect()->route('cms.timelines.index')
            ->with('success', 'Agenda timeline berhasil dihapus.');
    }

    /**
     * Allow super_admin, admin, editor, and operator to view listings & details.
     */
    private function authorizeAccess(): void
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole(['super_admin', 'admin', 'editor', 'operator'])) {
            abort(403, 'Akses ditolak.');
        }
    }

    /**
     * Restricted to super_admin, admin, and editor only.
     */
    private function authorizeManage(): void
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole(['super_admin', 'admin', 'editor'])) {
            abort(403, 'Anda tidak memiliki izin untuk mengelola timeline.');
        }
    }
}
