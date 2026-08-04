@extends('layouts.app')

@section('title', 'Manajemen User CMS')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Pengguna CMS</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola akun pengguna, peran akses (roles), serta status keaktifan user.</p>
        </div>
        <div>
            <a href="{{ route('super_admin.users.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah User Baru</span>
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Search & Filter Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
        <form method="GET" action="{{ route('super_admin.users.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama, email, atau role..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 placeholder-gray-400" />
            </div>
            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-gray-900 text-white rounded-xl text-sm font-medium hover:bg-gray-800 transition">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('super_admin.users.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-4">User Info</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Dibuat Pada</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition">
                            <!-- Name & Email -->
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                            </td>

                            <!-- Role Badge -->
                            <td class="px-6 py-4">
                                @foreach($user->roles as $role)
                                    @if($role->name === 'super_admin')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">Super Admin</span>
                                    @elseif($role->name === 'admin')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">Admin</span>
                                    @elseif($role->name === 'operator')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">Operator</span>
                                    @elseif($role->name === 'editor')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">Editor</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">{{ $role->name }}</span>
                                    @endif
                                @endforeach
                            </td>

                            <!-- Status Badge -->
                            <td class="px-6 py-4">
                                @if($user->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <!-- Created At -->
                            <td class="px-6 py-4 text-xs text-gray-500">
                                {{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                                @if(!$user->hasRole('super_admin'))
                                    <!-- Edit -->
                                    <a href="{{ route('super_admin.users.edit', $user) }}" class="inline-flex items-center px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition" title="Edit User">
                                        Edit
                                    </a>

                                    <!-- Reset Password -->
                                    <a href="{{ route('super_admin.users.password.edit', $user) }}" class="inline-flex items-center px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-medium rounded-lg transition" title="Reset Password">
                                        Password
                                    </a>

                                    <!-- Status Toggle -->
                                    @if($user->id !== Auth::id())
                                        <form method="POST" action="{{ route('super_admin.users.status', $user) }}" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center px-2.5 py-1.5 {{ $user->is_active ? 'bg-orange-50 hover:bg-orange-100 text-orange-700' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700' }} text-xs font-medium rounded-lg transition" onclick="return confirm('Apakah Anda yakin ingin {{ $user->is_active ? 'menonaktifkan' : 'mengaktifkan' }} user ini?')">
                                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Delete -->
                                    @if($user->id !== Auth::id())
                                        <form method="POST" action="{{ route('super_admin.users.destroy', $user) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-medium rounded-lg transition" title="Hapus User">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400 italic">Protected Super Admin</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">
                                Tidak ada data user yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
