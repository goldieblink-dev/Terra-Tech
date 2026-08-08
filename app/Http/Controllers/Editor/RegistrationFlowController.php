<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\StoreRegistrationStepRequest;
use App\Http\Requests\Editor\UpdateRegistrationStepRequest;
use App\Models\RegistrationStep;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RegistrationFlowController extends Controller
{
    /**
     * Display a listing of registration steps in CMS.
     */
    public function index(Request $request): View
    {
        $this->authorizeAccess();

        $query = RegistrationStep::with(['creator'])
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'asc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $steps = $query->paginate(10)->withQueryString();

        return view('editor.registration-flow.index', compact('steps'));
    }

    /**
     * Show the form for creating a new registration step.
     */
    public function create(): View
    {
        $this->authorizeManage();

        return view('editor.registration-flow.create');
    }

    /**
     * Store a newly created registration step.
     */
    public function store(StoreRegistrationStepRequest $request): RedirectResponse
    {
        $this->authorizeManage();

        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated) {
            $imagePath = null;
            if ($request->hasFile('illustration_image')) {
                $imagePath = $request->file('illustration_image')->store('registration_steps', 'public');
            }

            $step = RegistrationStep::create([
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

            Log::info('Langkah alur pendaftaran berhasil dibuat', [
                'id'         => $step->id,
                'title'      => $step->title,
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->route('cms.registration-steps.index')
            ->with('success', 'Langkah pendaftaran berhasil ditambahkan.');
    }

    /**
     * Display the specified registration step detail.
     */
    public function show(RegistrationStep $registrationStep): View
    {
        $this->authorizeAccess();

        $registrationStep->load(['creator', 'updater']);

        return view('editor.registration-flow.show', compact('registrationStep'));
    }

    /**
     * Show the form for editing the specified registration step.
     */
    public function edit(RegistrationStep $registrationStep): View
    {
        $this->authorizeManage();

        return view('editor.registration-flow.edit', compact('registrationStep'));
    }

    /**
     * Update the specified registration step.
     */
    public function update(UpdateRegistrationStepRequest $request, RegistrationStep $registrationStep): RedirectResponse
    {
        $this->authorizeManage();

        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated, $registrationStep) {
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
                // Delete old illustration image if exists
                if ($registrationStep->illustration_image && Storage::disk('public')->exists($registrationStep->illustration_image)) {
                    Storage::disk('public')->delete($registrationStep->illustration_image);
                }

                $updateData['illustration_image'] = $request->file('illustration_image')->store('registration_steps', 'public');
            }

            $registrationStep->update($updateData);

            Log::info('Langkah alur pendaftaran berhasil diperbarui', [
                'id'         => $registrationStep->id,
                'title'      => $registrationStep->title,
                'updated_by' => Auth::id(),
            ]);
        });

        return redirect()->route('cms.registration-steps.index')
            ->with('success', 'Langkah pendaftaran berhasil diperbarui.');
    }

    /**
     * Remove the specified registration step.
     */
    public function destroy(RegistrationStep $registrationStep): RedirectResponse
    {
        $this->authorizeManage();

        DB::transaction(function () use ($registrationStep) {
            $id = $registrationStep->id;
            $title = $registrationStep->title;

            $registrationStep->delete();

            Log::info('Langkah alur pendaftaran berhasil dihapus (soft delete)', [
                'id'         => $id,
                'title'      => $title,
                'deleted_by' => Auth::id(),
            ]);
        });

        return redirect()->route('cms.registration-steps.index')
            ->with('success', 'Langkah pendaftaran berhasil dihapus.');
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
            abort(403, 'Anda tidak memiliki izin untuk mengelola alur pendaftaran.');
        }
    }
}
