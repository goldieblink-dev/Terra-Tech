<?php

use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Editor\AnnouncementController;
use App\Http\Controllers\Editor\DashboardController as EditorDashboardController;
use App\Http\Controllers\Editor\InformationCategoryController;
use App\Http\Controllers\Editor\InformationPostController;
use App\Http\Controllers\Operator\DashboardController as OperatorDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicAnnouncementController;
use App\Http\Controllers\PublicInformationController;
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
