@extends('layouts.app')

@section('title', 'Tambah Kategori Informasi')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tambah Kategori Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Buat kategori baru untuk mengelompokkan artikel informasi.</p>
        </div>
        <a href="{{ route('cms.information-categories.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 flex items-center space-x-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali</span>
        </a>
    </div>

    <form method="POST" action="{{ route('cms.information-categories.store') }}" class="space-y-4">
        @csrf

        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-5">
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-rose-500 @enderror" required />
                @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="4" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm" placeholder="Deskripsi singkat kategori ini...">{{ old('description') }}</textarea>
                @error('description') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('cms.information-categories.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition">Batal</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 shadow-sm transition">Simpan Kategori</button>
        </div>
    </form>
</div>
@endsection
