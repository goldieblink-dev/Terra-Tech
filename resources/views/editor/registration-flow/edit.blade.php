@extends('layouts.app')

@section('title', 'Edit Langkah Pendaftaran')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Langkah Pendaftaran</h2>
            <p class="text-sm text-gray-500 mt-1">Perbarui judul, instruksi, persyaratan, atau gambar ilustrasi.</p>
        </div>
        <a href="{{ route('cms.registration-steps.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
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

    <form method="POST" action="{{ route('cms.registration-steps.update', $registrationStep) }}" enctype="multipart/form-data" class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        {{-- Judul --}}
        <div>
            <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Judul Langkah <span class="text-rose-500">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title', $registrationStep->title) }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>

        {{-- Deskripsi --}}
        <div>
            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi & Instruksi Langkah <span class="text-rose-500">*</span></label>
            <textarea name="description" id="description" rows="4" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $registrationStep->description) }}</textarea>
        </div>

        {{-- Persyaratan (Requirements List) --}}
        <div>
            <label for="requirements" class="block text-sm font-semibold text-gray-700 mb-1">Daftar Persyaratan / Berkas Syarat <span class="text-xs font-normal text-gray-500">(Opsional, 1 poin per baris)</span></label>
            @php
                $reqText = is_array($registrationStep->requirements) ? implode("\n", $registrationStep->requirements) : '';
            @endphp
            <textarea name="requirements" id="requirements" rows="4" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm font-mono focus:ring-indigo-500 focus:border-indigo-500">{{ old('requirements', is_array(old('requirements')) ? implode("\n", old('requirements')) : $reqText) }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Setiap baris teks akan otomatis dirender sebagai poin daftar perincian (bullet list).</p>
        </div>

        {{-- Gambar Ilustrasi Saat Ini & Input Ganti Gambar --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="illustration_image" class="block text-sm font-semibold text-gray-700 mb-1">Gambar Ilustrasi <span class="text-xs font-normal text-gray-500">(Opsional)</span></label>
                @if($registrationStep->illustration_image_url)
                    <div class="mb-2">
                        <img src="{{ $registrationStep->illustration_image_url }}" alt="Preview Ilustrasi" class="w-20 h-20 rounded-xl object-cover border border-gray-200" />
                        <span class="text-xs text-gray-500 block mt-1">Gambar saat ini. Kosongkan jika tidak ingin mengganti.</span>
                    </div>
                @endif
                <input type="file" name="illustration_image" id="illustration_image" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer" />
            </div>

            <div>
                <label for="icon" class="block text-sm font-semibold text-gray-700 mb-1">Nama Ikon <span class="text-xs font-normal text-gray-500">(Opsional)</span></label>
                <input type="text" name="icon" id="icon" value="{{ old('icon', $registrationStep->icon) }}" placeholder="Contoh: user-check" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>
        </div>

        {{-- Urutan Sort & Status Publish --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="sort_order" class="block text-sm font-semibold text-gray-700 mb-1">Urutan Tampil (Sort Order) <span class="text-rose-500">*</span></label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $registrationStep->sort_order) }}" min="0" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>

            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Status Publikasi <span class="text-rose-500">*</span></label>
                <select name="status" id="status" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="draft" {{ old('status', $registrationStep->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $registrationStep->status) === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
            <a href="{{ route('cms.registration-steps.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">Batal</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">Perbarui Langkah</button>
        </div>
    </form>
</div>
@endsection
