@extends('layouts.app')

@section('title', 'Tambah Kategori File')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tambah Kategori File Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Buat kategori baru untuk pengelompokan dokumen file.</p>
        </div>
        <a href="{{ route('cms.file-categories.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl">
            <div class="font-semibold text-sm">Terdapat kesalahan pengisian form:</div>
            <ul class="list-disc list-inside text-xs mt-1 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('cms.file-categories.store') }}" class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-6">
        @csrf

        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Modul Teknis, Panduan Pengguna" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>

        <div>
            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Kategori <span class="text-xs font-normal text-gray-500">(Opsional)</span></label>
            <textarea name="description" id="description" rows="4" placeholder="Penjelasan singkat mengenai kategori file ini..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
            <a href="{{ route('cms.file-categories.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">Batal</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">Simpan Kategori</button>
        </div>
    </form>
</div>
@endsection
