<?php

use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Editor\AnnouncementController;
use App\Http\Controllers\Editor\DashboardController as EditorDashboardController;
use App\Http\Controllers\Editor\FileCategoryController;
use App\Http\Controllers\Editor\FileController;
use App\Http\Controllers\Editor\InformationCategoryController;
use App\Http\Controllers\Editor\InformationPostController;
use App\Http\Controllers\Editor\RegistrationFlowController;
use App\Http\Controllers\Editor\TimelineController;
use App\Http\Controllers\Operator\DashboardController as OperatorDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicAnnouncementController;
use App\Http\Controllers\PublicFileController;
use App\Http\Controllers\PublicInformationController;
use App\Http\Controllers\PublicRegistrationFlowController;
use App\Http\Controllers\PublicTimelineController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ------------------------------------------------------------------
// Public Routes (no auth required)
// ------------------------------------------------------------------
Route::get('/information', [PublicInformationController::class, 'index'])->name('public.information.index');
Route::get('/information/{slug}', [PublicInformationController::class, 'show'])->name('public.information.show');

Route::get('/announcements', [PublicAnnouncementController::class, 'index'])->name('public.announcements.index');
Route::get('/announcements/{slug}', [PublicAnnouncementController::class, 'show'])->name('public.announcements.show');
Route::get('/announcements/{slug}/download', [PublicAnnouncementController::class, 'download'])->name('public.announcements.download');

Route::get('/timeline', [PublicTimelineController::class, 'index'])->name('public.timelines.index');
Route::get('/timeline/{timeline}', [PublicTimelineController::class, 'show'])->name('public.timelines.show');

Route::get('/files', [PublicFileController::class, 'index'])->name('public.files.index');
Route::get('/files/{slug}', [PublicFileController::class, 'show'])->name('public.files.show');
Route::get('/files/{slug}/download', [PublicFileController::class, 'download'])->name('public.files.download');

Route::get('/registration-flow', [PublicRegistrationFlowController::class, 'index'])->name('public.registration_flow.index');
Route::get('/registration-flow/{slug}', [PublicRegistrationFlowController::class, 'show'])->name('public.registration_flow.show');

// ------------------------------------------------------------------
// Root Redirect
// ------------------------------------------------------------------
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->hasRole('super_admin')) return redirect()->route('super_admin.dashboard');
        if ($user->hasRole('admin')) return redirect()->route('admin.dashboard');
        if ($user->hasRole('operator')) return redirect()->route('operator.dashboard');
        if ($user->hasRole('editor')) return redirect()->route('editor.dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->hasRole('super_admin')) return redirect()->route('super_admin.dashboard');
    if ($user->hasRole('admin')) return redirect()->route('admin.dashboard');
    if ($user->hasRole('operator')) return redirect()->route('operator.dashboard');
    if ($user->hasRole('editor')) return redirect()->route('editor.dashboard');

    return abort(403, 'Unauthorized role');
})->middleware(['auth'])->name('dashboard');

// ------------------------------------------------------------------
// CMS Shared: Company Profile (super_admin | admin)
// ------------------------------------------------------------------
Route::middleware(['auth', 'role:super_admin|admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('company-profile', [CompanyProfileController::class, 'edit'])->name('company_profile.edit');
    Route::put('company-profile', [CompanyProfileController::class, 'update'])->name('company_profile.update');
});

// ------------------------------------------------------------------
// CMS Shared: Modules (Information & Announcements)
// Listing, Show & Download → super_admin | admin | editor | operator
// Create/Update/Delete     → super_admin | admin | editor only (enforced in controller)
// ------------------------------------------------------------------
Route::middleware(['auth', 'role:super_admin|admin|editor|operator'])
    ->prefix('cms')
    ->name('cms.')
    ->group(function () {

    // Information Posts
    Route::get('information', [InformationPostController::class, 'index'])->name('information.index');
    Route::get('information/create', [InformationPostController::class, 'create'])->name('information.create');
    Route::post('information', [InformationPostController::class, 'store'])->name('information.store');
    Route::get('information/{informationPost}', [InformationPostController::class, 'show'])->name('information.show');
    Route::get('information/{informationPost}/edit', [InformationPostController::class, 'edit'])->name('information.edit');
    Route::put('information/{informationPost}', [InformationPostController::class, 'update'])->name('information.update');
    Route::delete('information/{informationPost}', [InformationPostController::class, 'destroy'])->name('information.destroy');

    // Information Categories
    Route::get('information-categories', [InformationCategoryController::class, 'index'])->name('information-categories.index');
    Route::get('information-categories/create', [InformationCategoryController::class, 'create'])->name('information-categories.create');
    Route::post('information-categories', [InformationCategoryController::class, 'store'])->name('information-categories.store');
    Route::get('information-categories/{informationCategory}/edit', [InformationCategoryController::class, 'edit'])->name('information-categories.edit');
    Route::put('information-categories/{informationCategory}', [InformationCategoryController::class, 'update'])->name('information-categories.update');
    Route::delete('information-categories/{informationCategory}', [InformationCategoryController::class, 'destroy'])->name('information-categories.destroy');

    // Announcements
    Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
    Route::get('announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::get('announcements/{announcement}/download', [AnnouncementController::class, 'download'])->name('announcements.download');

    // Timelines
    Route::get('timelines', [TimelineController::class, 'index'])->name('timelines.index');
    Route::get('timelines/create', [TimelineController::class, 'create'])->name('timelines.create');
    Route::post('timelines', [TimelineController::class, 'store'])->name('timelines.store');
    Route::get('timelines/{timeline}', [TimelineController::class, 'show'])->name('timelines.show');
    Route::get('timelines/{timeline}/edit', [TimelineController::class, 'edit'])->name('timelines.edit');
    Route::put('timelines/{timeline}', [TimelineController::class, 'update'])->name('timelines.update');
    Route::delete('timelines/{timeline}', [TimelineController::class, 'destroy'])->name('timelines.destroy');

    // File Categories
    Route::get('file-categories', [FileCategoryController::class, 'index'])->name('file-categories.index');
    Route::get('file-categories/create', [FileCategoryController::class, 'create'])->name('file-categories.create');
    Route::post('file-categories', [FileCategoryController::class, 'store'])->name('file-categories.store');
    Route::get('file-categories/{fileCategory}/edit', [FileCategoryController::class, 'edit'])->name('file-categories.edit');
    Route::put('file-categories/{fileCategory}', [FileCategoryController::class, 'update'])->name('file-categories.update');
    Route::delete('file-categories/{fileCategory}', [FileCategoryController::class, 'destroy'])->name('file-categories.destroy');

    // Files
    Route::get('files', [FileController::class, 'index'])->name('files.index');
    Route::get('files/create', [FileController::class, 'create'])->name('files.create');
    Route::post('files', [FileController::class, 'store'])->name('files.store');
    Route::get('files/{fileItem}', [FileController::class, 'show'])->name('files.show');
    Route::get('files/{fileItem}/edit', [FileController::class, 'edit'])->name('files.edit');
    Route::put('files/{fileItem}', [FileController::class, 'update'])->name('files.update');
    Route::delete('files/{fileItem}', [FileController::class, 'destroy'])->name('files.destroy');
    Route::get('files/{fileItem}/download', [FileController::class, 'download'])->name('files.download');

    // Registration Steps
    Route::get('registration-steps', [RegistrationFlowController::class, 'index'])->name('registration-steps.index');
    Route::get('registration-steps/create', [RegistrationFlowController::class, 'create'])->name('registration-steps.create');
    Route::post('registration-steps', [RegistrationFlowController::class, 'store'])->name('registration-steps.store');
    Route::get('registration-steps/{registrationStep}', [RegistrationFlowController::class, 'show'])->name('registration-steps.show');
    Route::get('registration-steps/{registrationStep}/edit', [RegistrationFlowController::class, 'edit'])->name('registration-steps.edit');
    Route::put('registration-steps/{registrationStep}', [RegistrationFlowController::class, 'update'])->name('registration-steps.update');
    Route::delete('registration-steps/{registrationStep}', [RegistrationFlowController::class, 'destroy'])->name('registration-steps.destroy');
});

// ------------------------------------------------------------------
// Super Admin Routes
// ------------------------------------------------------------------
Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super_admin.')->group(function () {
    Route::get('/', SuperAdminDashboardController::class)->name('dashboard');

    Route::get('users', [SuperAdminUserController::class, 'index'])->name('users.index');
    Route::get('users/create', [SuperAdminUserController::class, 'create'])->name('users.create');
    Route::post('users', [SuperAdminUserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [SuperAdminUserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [SuperAdminUserController::class, 'update'])->name('users.update');
    Route::patch('users/{user}/status', [SuperAdminUserController::class, 'status'])->name('users.status');
    Route::get('users/{user}/password', [SuperAdminUserController::class, 'passwordEdit'])->name('users.password.edit');
    Route::patch('users/{user}/password', [SuperAdminUserController::class, 'passwordUpdate'])->name('users.password.update');
    Route::delete('users/{user}', [SuperAdminUserController::class, 'destroy'])->name('users.destroy');
});

// ------------------------------------------------------------------
// Admin Routes
// ------------------------------------------------------------------
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', AdminDashboardController::class)->name('admin.dashboard');
});

// ------------------------------------------------------------------
// Operator Routes
// ------------------------------------------------------------------
Route::middleware(['auth', 'role:operator'])->group(function () {
    Route::get('/operator', OperatorDashboardController::class)->name('operator.dashboard');
});

// ------------------------------------------------------------------
// Editor Routes
// ------------------------------------------------------------------
Route::middleware(['auth', 'role:editor'])->group(function () {
    Route::get('/editor', EditorDashboardController::class)->name('editor.dashboard');
});

// ------------------------------------------------------------------
// Shared Auth Routes
// ------------------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
