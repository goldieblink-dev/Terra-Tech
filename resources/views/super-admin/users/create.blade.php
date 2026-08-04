@extends('layouts.app')

@section('title', 'Tambah User Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tambah User CMS Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Buat akun pengguna baru dan tentukan peran aksesnya (admin, operator, atau editor).</p>
        </div>
        <a href="{{ route('super_admin.users.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition flex items-center space-x-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <form method="POST" action="{{ route('super_admin.users.store') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 @error('name') border-rose-500 @enderror" placeholder="Masukkan nama lengkap user" required />
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 @error('email') border-rose-500 @enderror" placeholder="user@terratech.test" required />
                @error('email')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role Selection -->
            <div>
                <label for="role" class="block text-sm font-semibold text-gray-700 mb-1">Role / Peran Akses</label>
                <select name="role" id="role" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 @error('role') border-rose-500 @enderror" required>
                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Pilih Role --</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Pengelola Konten & Staff)</option>
                    <option value="operator" {{ old('role') === 'operator' ? 'selected' : '' }}>Operator (Pengumuman, Timeline, File)</option>
                    <option value="editor" {{ old('role') === 'editor' ? 'selected' : '' }}>Editor (Informasi & Beranda)</option>
                </select>
                @error('role')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 @error('password') border-rose-500 @enderror" placeholder="Minimal 8 karakter" required />
                @error('password')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900" placeholder="Ulangi password di atas" required />
            </div>

            <!-- Form Actions -->
            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-gray-100">
                <a href="{{ route('super_admin.users.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 shadow-sm transition">
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
