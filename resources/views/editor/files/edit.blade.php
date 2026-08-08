@extends('layouts.app')

@section('title', 'Edit File Dokumen')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit File Dokumen</h2>
            <p class="text-sm text-gray-500 mt-1">Perbarui informasi dokumen atau ganti berkas yang sudah diunggah.</p>
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

    <form method="POST" action="{{ route('cms.files.update', $fileItem) }}" enctype="multipart/form-data" class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        {{-- Judul --}}
        <div>
            <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Judul Dokumen <span class="text-rose-500">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title', $fileItem->title) }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>

        {{-- Kategori & Status --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1">Kategori File <span class="text-rose-500">*</span></label>
                <select name="category_id" id="category_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $fileItem->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Status Publikasi <span class="text-rose-500">*</span></label>
                <select name="status" id="status" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="draft" {{ old('status', $fileItem->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $fileItem->status) === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>
        </div>

        {{-- Berkas Saat Ini --}}
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Berkas Saat Ini</div>
            <div class="flex items-center space-x-3">
                <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <div>
                    <div class="text-sm font-semibold text-gray-800">{{ $fileItem->original_name }}</div>
                    <div class="text-xs text-gray-500">{{ $fileItem->formatted_file_size }} &bull; {{ $fileItem->mime_type }}</div>
                </div>
            </div>
        </div>

        {{-- Ganti Berkas --}}
        <div>
            <label for="file" class="block text-sm font-semibold text-gray-700 mb-1">Ganti Berkas <span class="text-xs font-normal text-gray-500">(Opsional — kosongkan jika tidak ingin mengganti)</span></label>
            <input type="file" name="file" id="file" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer" />
            <p class="text-xs text-gray-500 mt-1.5">
                Format: <strong>PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, RAR</strong>. Maks <strong>20 MB</strong>.<br>
                <span class="text-rose-600 font-medium">Berkas executable dan skrip (.exe, .bat, .cmd, .sh, .php, .js, .html) tidak diizinkan.</span>
            </p>
        </div>

        {{-- Deskripsi --}}
        <div>
            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Dokumen <span class="text-xs font-normal text-gray-500">(Opsional)</span></label>
            <textarea name="description" id="description" rows="4" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $fileItem->description) }}</textarea>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
            <a href="{{ route('cms.files.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">Batal</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">Perbarui File</button>
        </div>
    </form>
</div>
@endsection
