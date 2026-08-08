<?php

namespace App\Http\Controllers\Api\V1\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\StoreRegistrationStepRequest;
use App\Http\Requests\Editor\UpdateRegistrationStepRequest;
use App\Http\Resources\Api\V1\RegistrationStepResource;
use App\Models\RegistrationStep;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RegistrationFlowController extends Controller
{
    use ApiResponse;

    /**
     * Display listing of registration steps for CMS.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorizeAccess();

        $query = RegistrationStep::with('creator')
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'asc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $steps = $query->paginate(10);

        return $this->successResponse([
            'items'      => RegistrationStepResource::collection($steps),
            'pagination' => [
                'current_page' => $steps->currentPage(),
                'last_page'    => $steps->lastPage(),
                'per_page'     => $steps->perPage(),
                'total'        => $steps->total(),
            ],
        ], 'Daftar langkah pendaftaran CMS berhasil diambil.');
    }

    /**
     * Store a newly created registration step in CMS.
     */
    public function store(StoreRegistrationStepRequest $request): JsonResponse
    {
        $this->authorizeManage();

        $validated = $request->validated();

        $step = DB::transaction(function () use ($request, $validated) {
            $imagePath = null;
            if ($request->hasFile('illustration_image')) {
                $imagePath = $request->file('illustration_image')->store('registration_steps', 'public');
            }

            return RegistrationStep::create([
                'title'              => $validated['title'],
                'description'        => $validated['description'],
                'requirements'       => $validated['requirements'] ?? null,
                'icon'               => $validated['icon'] ?? null,
                'illustration_image' => $imagePath,
                'sort_order'         => $validated['sort_order'] ?? 0,
                'status'             => $validated['status'],
                'created_by'         => Auth::id(),
                'updated_by'         => Auth::id(),
            ]);
        });

        $step->load('creator');

        return $this->successResponse(
            new RegistrationStepResource($step),
            'Langkah pendaftaran berhasil dibuat.',
            201
        );
    }

    /**
     * Display details of a specific registration step in CMS.
     */
    public function show(int $id): JsonResponse
    {
        $this->authorizeAccess();

        $step = RegistrationStep::with(['creator', 'updater'])->findOrFail($id);

        return $this->successResponse(
            new RegistrationStepResource($step),
            'Detail langkah pendaftaran CMS berhasil diambil.'
        );
    }

    /**
     * Update a registration step in CMS.
     */
    public function update(UpdateRegistrationStepRequest $request, int $id): JsonResponse
    {
        $this->authorizeManage();

        $step = RegistrationStep::findOrFail($id);
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated, $step) {
            $updateData = [
                'title'        => $validated['title'],
                'description'  => $validated['description'],
                'requirements' => $validated['requirements'] ?? null,
                'icon'         => $validated['icon'] ?? null,
                'sort_order'   => $validated['sort_order'] ?? 0,
                'status'       => $validated['status'],
                'updated_by'   => Auth::id(),
            ];

            if ($request->hasFile('illustration_image')) {
                if ($step->illustration_image && Storage::disk('public')->exists($step->illustration_image)) {
                    Storage::disk('public')->delete($step->illustration_image);
                }
                $updateData['illustration_image'] = $request->file('illustration_image')->store('registration_steps', 'public');
            }

            $step->update($updateData);
        });

        $step->load(['creator', 'updater']);

        return $this->successResponse(
            new RegistrationStepResource($step),
            'Langkah pendaftaran berhasil diperbarui.'
        );
    }

    /**
     * Soft-delete a registration step in CMS.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorizeManage();

        $step = RegistrationStep::findOrFail($id);

        DB::transaction(function () use ($step) {
            $step->delete();
        });

        return $this->successResponse(null, 'Langkah pendaftaran berhasil dihapus.');
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
            abort(403, 'Anda tidak memiliki izin untuk mengelola langkah pendaftaran.');
        }
    }
}
