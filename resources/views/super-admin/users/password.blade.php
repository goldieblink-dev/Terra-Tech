@extends('layouts.app')

@section('title', 'Reset Password User')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Reset Password User</h2>
            <p class="text-sm text-gray-500 mt-1">Ubah password untuk akun {{ $user->name }} ({{ $user->email }}).</p>
        </div>
        <a href="{{ route('super_admin.users.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition flex items-center space-x-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <form method="POST" action="{{ route('super_admin.users.password.update', $user) }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="password" id="password" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 @error('password') border-rose-500 @enderror" placeholder="Masukkan password baru (minimal 8 karakter)" required />
                @error('password')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900" placeholder="Ulangi password baru" required />
            </div>

            <!-- Form Actions -->
            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-gray-100">
                <a href="{{ route('super_admin.users.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-amber-600 text-white rounded-xl text-sm font-medium hover:bg-amber-700 shadow-sm transition">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
