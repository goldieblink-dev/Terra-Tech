@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Data User</h2>
            <p class="text-sm text-gray-500 mt-1">Perbarui informasi profil dan peran akses (role) dari user {{ $user->name }}.</p>
        </div>
        <a href="{{ route('super_admin.users.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition flex items-center space-x-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <form method="POST" action="{{ route('super_admin.users.update', $user) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 @error('name') border-rose-500 @enderror" required />
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 @error('email') border-rose-500 @enderror" required />
                @error('email')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role Selection -->
            <div>
                <label for="role" class="block text-sm font-semibold text-gray-700 mb-1">Role / Peran Akses</label>
                @php
                    $currentRole = old('role', $user->roles->first()?->name);
                @endphp
                <select name="role" id="role" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 @error('role') border-rose-500 @enderror" required>
                    <option value="admin" {{ $currentRole === 'admin' ? 'selected' : '' }}>Admin (Pengelola Konten & Staff)</option>
                    <option value="operator" {{ $currentRole === 'operator' ? 'selected' : '' }}>Operator (Pengumuman, Timeline, File)</option>
                    <option value="editor" {{ $currentRole === 'editor' ? 'selected' : '' }}>Editor (Informasi & Beranda)</option>
                </select>
                @error('role')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-gray-100">
                <a href="{{ route('super_admin.users.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 shadow-sm transition">
                    Perbarui User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
