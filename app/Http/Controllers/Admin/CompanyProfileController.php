<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCompanyProfileRequest;
use App\Models\CompanyProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CompanyProfileController extends Controller
{
    /**
     * Show the form for editing company profile.
     */
    public function edit(): View
    {
        $this->authorizeCompanyProfileAdmin();

        $profile = CompanyProfile::firstOrCreate(
            ['id' => 1],
            ['company_name' => 'Terra Tech Indonesia']
        );

        return view('admin.company-profile.edit', compact('profile'));
    }

    /**
     * Update company profile configuration in storage.
     */
    public function update(UpdateCompanyProfileRequest $request): RedirectResponse
    {
        $this->authorizeCompanyProfileAdmin();

        $profile = CompanyProfile::firstOrCreate(
            ['id' => 1],
            ['company_name' => 'Terra Tech Indonesia']
        );

        DB::transaction(function () use ($request, $profile) {
            $data = [
                'company_name' => $request->company_name,
                'tagline' => $request->tagline,
                'description' => $request->description,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'facebook_url' => $request->facebook_url,
                'instagram_url' => $request->instagram_url,
                'linkedin_url' => $request->linkedin_url,
                'youtube_url' => $request->youtube_url,
                'updated_by' => Auth::id(),
            ];

            // Handle Logo Upload
            if ($request->hasFile('logo')) {
                $logoFile = $request->file('logo');
                if ($logoFile->isValid()) {
                    // Delete old logo if exists
                    if ($profile->logo_path && Storage::disk('public')->exists($profile->logo_path)) {
                        Storage::disk('public')->delete($profile->logo_path);
                    }

                    $logoExtension = $logoFile->getClientOriginalExtension();
                    $logoName = 'logo_' . Str::uuid() . '.' . strtolower($logoExtension);
                    $logoPath = $logoFile->storeAs('company/logo', $logoName, 'public');

                    $data['logo_path'] = $logoPath;
                }
            }

            // Handle Favicon Upload
            if ($request->hasFile('favicon')) {
                $faviconFile = $request->file('favicon');
                if ($faviconFile->isValid()) {
                    // Delete old favicon if exists
                    if ($profile->favicon_path && Storage::disk('public')->exists($profile->favicon_path)) {
                        Storage::disk('public')->delete($profile->favicon_path);
                    }

                    $faviconExtension = $faviconFile->getClientOriginalExtension();
                    $faviconName = 'favicon_' . Str::uuid() . '.' . strtolower($faviconExtension);
                    $faviconPath = $faviconFile->storeAs('company/favicon', $faviconName, 'public');

                    $data['favicon_path'] = $faviconPath;
                }
            }

            $profile->update($data);

            // Clear cache after successful update
            CompanyProfile::clearCache();
        });

        Log::info('company_profile_updated', [
            'updated_by' => Auth::id(),
            'company_name' => $request->company_name,
        ]);

        return redirect()->route('admin.company_profile.edit')
            ->with('success', 'Profil perusahaan berhasil diperbarui.');
    }

    /**
     * Explicit authorization check for Super Admin and Admin.
     */
    private function authorizeCompanyProfileAdmin(): void
    {
        if (!Auth::check() || (!Auth::user()->hasRole('super_admin') && !Auth::user()->hasRole('admin'))) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengelola profil perusahaan.');
        }
    }
}
