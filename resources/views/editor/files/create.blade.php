@extends('layouts.app')

@section('title', 'Unggah File Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Unggah File Dokumen Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Unggah dokumen teknis, panduan, atau berkas arsip resmi.</p>
        </div>
        <a href="{{ route('cms.files.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
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

    <form method="POST" action="{{ route('cms.files.store') }}" enctype="multipart/form-data" class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-6">
        @csrf

        {{-- Judul --}}
        <div>
            <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Judul Dokumen <span class="text-rose-500">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Contoh: Panduan Operasional Sistem v2.0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>

        {{-- Kategori & Status --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1">Kategori File <span class="text-rose-500">*</span></label>
                <select name="category_id" id="category_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Status Publikasi <span class="text-rose-500">*</span></label>
                <select name="status" id="status" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Disimpan lebih dulu)</option>
                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published (Tampil di publik)</option>
                </select>
            </div>
        </div>

        {{-- Unggah Berkas --}}
        <div>
            <label for="file" class="block text-sm font-semibold text-gray-700 mb-1">Unggah Berkas File <span class="text-rose-500">*</span></label>
            <input type="file" name="file" id="file" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer" />
            <p class="text-xs text-gray-500 mt-1.5">
                Format yang diizinkan: <strong>PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, RAR</strong>. Maksimal <strong>20 MB</strong>.<br>
                <span class="text-rose-600 font-medium">Berkas executable dan skrip (.exe, .bat, .cmd, .sh, .php, .js, .html) tidak diizinkan.</span>
            </p>
        </div>

        {{-- Deskripsi --}}
        <div>
            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Dokumen <span class="text-xs font-normal text-gray-500">(Opsional)</span></label>
            <textarea name="description" id="description" rows="4" placeholder="Penjelasan mengenai isi dokumen file..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
            <a href="{{ route('cms.files.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">Batal</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">Unggah Berkas</button>
        </div>
    </form>
</div>
@endsection
