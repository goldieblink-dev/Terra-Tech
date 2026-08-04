@extends('layouts.app')

@section('title', 'Tambah Artikel Informasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tambah Artikel Informasi Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Buat artikel baru dengan kategori, gambar sampul, dan data SEO.</p>
        </div>
        <a href="{{ route('cms.information.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 flex items-center space-x-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali</span>
        </a>
    </div>

    <form method="POST" action="{{ route('cms.information.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Main Content --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-5">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3">Konten Artikel</h3>

            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Judul Artikel <span class="text-rose-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-rose-500 @enderror" required />
                @error('title') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                    <select name="category_id" id="category_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('category_id') border-rose-500 @enderror" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Status Publikasi <span class="text-rose-500">*</span></label>
                    <select name="status" id="status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="excerpt" class="block text-sm font-semibold text-gray-700 mb-1">Ringkasan / Excerpt</label>
                <textarea name="excerpt" id="excerpt" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ringkasan singkat artikel...">{{ old('excerpt') }}</textarea>
                @error('excerpt') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="content" class="block text-sm font-semibold text-gray-700 mb-1">Konten Artikel <span class="text-rose-500">*</span></label>
                <textarea name="content" id="content" rows="12" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono @error('content') border-rose-500 @enderror" placeholder="Isi konten artikel di sini..." required>{{ old('content') }}</textarea>
                @error('content') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Featured Image --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3">Gambar Sampul (Featured Image)</h3>
            <div>
                <input type="file" name="featured_image" id="featured_image" accept="image/png,image/jpg,image/jpeg,image/webp" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                <p class="text-xs text-gray-400 mt-1">Format: PNG, JPG, JPEG, WEBP. Maksimal 2MB.</p>
                @error('featured_image') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="featured_image_alt" class="block text-sm font-semibold text-gray-700 mb-1">Alt Text Gambar</label>
                <input type="text" name="featured_image_alt" id="featured_image_alt" value="{{ old('featured_image_alt') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm" placeholder="Deskripsi gambar untuk aksesibilitas" />
            </div>
        </div>

        {{-- SEO --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3">Metadata SEO</h3>
            <div>
                <label for="meta_title" class="block text-sm font-semibold text-gray-700 mb-1">Meta Title</label>
                <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm" placeholder="Judul SEO (maks. 255 karakter)" />
            </div>
            <div>
                <label for="meta_description" class="block text-sm font-semibold text-gray-700 mb-1">Meta Description</label>
                <textarea name="meta_description" id="meta_description" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm" placeholder="Deskripsi meta untuk mesin pencari (maks. 500 karakter)">{{ old('meta_description') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('cms.information.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition">Batal</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 shadow-sm transition">Simpan Artikel</button>
        </div>
    </form>
</div>
@endsection
