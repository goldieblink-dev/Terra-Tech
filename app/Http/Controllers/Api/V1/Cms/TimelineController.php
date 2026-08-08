<?php

namespace App\Http\Controllers\Api\V1\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\StoreTimelineRequest;
use App\Http\Requests\Editor\UpdateTimelineRequest;
use App\Http\Resources\Api\V1\TimelineResource;
use App\Models\Timeline;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimelineController extends Controller
{
    use ApiResponse;

    /**
     * Display listing of timelines for CMS.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorizeAccess();

        $query = Timeline::orderBy('sort_order', 'asc')->orderBy('created_at', 'asc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $timelines = $query->paginate(10);

        return $this->successResponse([
            'items'      => TimelineResource::collection($timelines),
            'pagination' => [
                'current_page' => $timelines->currentPage(),
                'last_page'    => $timelines->lastPage(),
                'per_page'     => $timelines->perPage(),
                'total'        => $timelines->total(),
            ],
        ], 'Daftar timeline CMS berhasil diambil.');
    }

    /**
     * Store a newly created timeline in CMS.
     */
    public function store(StoreTimelineRequest $request): JsonResponse
    {
        $this->authorizeManage();

        $validated = $request->validated();

        $timeline = DB::transaction(function () use ($validated) {
            return Timeline::create([
                'title'       => $validated['title'],
                'description' => $validated['description'],
                'start_date'  => $validated['start_date'],
                'end_date'    => $validated['end_date'] ?? null,
                'location'    => $validated['location'] ?? null,
                'color'       => $validated['color'] ?? '#2563eb',
                'icon'        => $validated['icon'] ?? null,
                'sort_order'  => $validated['sort_order'] ?? 0,
                'status'      => $validated['status'],
                'created_by'  => Auth::id(),
                'updated_by'  => Auth::id(),
            ]);
        });

        return $this->successResponse(
            new TimelineResource($timeline),
            'Timeline berhasil dibuat.',
            201
        );
    }

    /**
     * Display details of a specific timeline in CMS.
     */
    public function show(int $id): JsonResponse
    {
        $this->authorizeAccess();

        $timeline = Timeline::findOrFail($id);

        return $this->successResponse(
            new TimelineResource($timeline),
            'Detail timeline CMS berhasil diambil.'
        );
    }

    /**
     * Update a timeline in CMS.
     */
    public function update(UpdateTimelineRequest $request, int $id): JsonResponse
    {
        $this->authorizeManage();

        $timeline = Timeline::findOrFail($id);
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $timeline) {
            $timeline->update([
                'title'       => $validated['title'],
                'description' => $validated['description'],
                'start_date'  => $validated['start_date'],
                'end_date'    => $validated['end_date'] ?? null,
                'location'    => $validated['location'] ?? null,
                'color'       => $validated['color'] ?? '#2563eb',
                'icon'        => $validated['icon'] ?? null,
                'sort_order'  => $validated['sort_order'] ?? 0,
                'status'      => $validated['status'],
                'updated_by'  => Auth::id(),
            ]);
        });

        return $this->successResponse(
            new TimelineResource($timeline),
            'Timeline berhasil diperbarui.'
        );
    }

    /**
     * Soft-delete a timeline in CMS.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorizeManage();

        $timeline = Timeline::findOrFail($id);

        DB::transaction(function () use ($timeline) {
            $timeline->delete();
        });

        return $this->successResponse(null, 'Timeline berhasil dihapus.');
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
            abort(403, 'Anda tidak memiliki izin untuk mengelola timeline.');
        }
    }
}
