<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreUserRequest;
use App\Http\Requests\SuperAdmin\UpdatePasswordRequest;
use App\Http\Requests\SuperAdmin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): View
    {
        $this->authorizeSuperAdmin();

        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('roles', function ($rq) use ($search) {
                      $rq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('super-admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $this->authorizeSuperAdmin();

        $roles = ['admin', 'operator', 'editor'];

        return view('super-admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $user = DB::transaction(function () use ($request) {
            $createdUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => true,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $createdUser->syncRoles([$request->role]);

            return $createdUser;
        });

        Log::info('user_created', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $request->role,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('super_admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $this->authorizeSuperAdmin();
        $this->preventSuperAdminTarget($user, 'mengedit');

        $roles = ['admin', 'operator', 'editor'];

        return view('super-admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorizeSuperAdmin();
        $this->preventSuperAdminTarget($user, 'mengubah data');

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'updated_by' => Auth::id(),
            ]);

            $user->syncRoles([$request->role]);
        });

        Log::info('user_updated', [
            'user_id' => $user->id,
            'updated_by' => Auth::id(),
            'role' => $request->role,
        ]);

        return redirect()->route('super_admin.users.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * Toggle active status of the specified user.
     */
    public function status(User $user): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        if ($user->id === Auth::id()) {
            abort(403, 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $this->preventSuperAdminTarget($user, 'mengubah status');

        $user->is_active = !$user->is_active;
        $user->updated_by = Auth::id();
        $user->save();

        Log::info('user_status_changed', [
            'user_id' => $user->id,
            'is_active' => $user->is_active,
            'changed_by' => Auth::id(),
        ]);

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('super_admin.users.index')
            ->with('success', "Status user {$user->name} berhasil {$statusText}.");
    }

    /**
     * Show the form for resetting user password.
     */
    public function passwordEdit(User $user): View
    {
        $this->authorizeSuperAdmin();
        $this->preventSuperAdminTarget($user, 'mereset password');

        return view('super-admin.users.password', compact('user'));
    }

    /**
     * Update user password.
     */
    public function passwordUpdate(UpdatePasswordRequest $request, User $user): RedirectResponse
    {
        $this->authorizeSuperAdmin();
        $this->preventSuperAdminTarget($user, 'mereset password');

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'password' => Hash::make($request->password),
                'updated_by' => Auth::id(),
            ]);
        });

        Log::info('user_password_reset', [
            'user_id' => $user->id,
            'reset_by' => Auth::id(),
        ]);

        return redirect()->route('super_admin.users.index')
            ->with('success', "Password untuk user {$user->name} berhasil diperbarui.");
    }

    /**
     * Remove the specified user from storage (Soft Delete).
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        if ($user->id === Auth::id()) {
            abort(403, 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $this->preventSuperAdminTarget($user, 'menghapus');

        $user->delete();

        Log::info('user_deleted', [
            'user_id' => $user->id,
            'deleted_by' => Auth::id(),
        ]);

        return redirect()->route('super_admin.users.index')
            ->with('success', "User {$user->name} berhasil dihapus.");
    }

    /**
     * Explicit authorization check for Super Admin.
     */
    private function authorizeSuperAdmin(): void
    {
        if (!Auth::check() || !Auth::user()->hasRole('super_admin')) {
            abort(403, 'Akses ditolak. Hanya Super Admin yang diizinkan mengelola data user.');
        }
    }

    /**
     * Prevent operations targeting super_admin accounts.
     */
    private function preventSuperAdminTarget(User $targetUser, string $action): void
    {
        if ($targetUser->hasRole('super_admin')) {
            abort(403, "Anda tidak diizinkan untuk {$action} akun Super Admin lain melalui UI CMS.");
        }
    }
}
