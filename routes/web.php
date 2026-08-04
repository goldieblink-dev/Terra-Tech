<?php

use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Editor\DashboardController as EditorDashboardController;
use App\Http\Controllers\Operator\DashboardController as OperatorDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

// Company Profile Routes (Shared for Super Admin & Admin)
Route::middleware(['auth', 'role:super_admin|admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('company-profile', [CompanyProfileController::class, 'edit'])->name('company_profile.edit');
    Route::put('company-profile', [CompanyProfileController::class, 'update'])->name('company_profile.update');
});

// Super Admin Routes
Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super_admin.')->group(function () {
    Route::get('/', SuperAdminDashboardController::class)->name('dashboard');

    // CMS User Management Routes
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

// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', AdminDashboardController::class)->name('admin.dashboard');
});

// Operator Routes
Route::middleware(['auth', 'role:operator'])->group(function () {
    Route::get('/operator', OperatorDashboardController::class)->name('operator.dashboard');
});

// Editor Routes
Route::middleware(['auth', 'role:editor'])->group(function () {
    Route::get('/editor', EditorDashboardController::class)->name('editor.dashboard');
});

// Shared Auth Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
