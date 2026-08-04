@extends('layouts.app')

@section('title', 'Edit Pengumuman')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Pengumuman</h2>
            <p class="text-sm text-gray-500 mt-1">Perbarui pengumuman: <strong>{{ $announcement->title }}</strong></p>
        </div>
        <a href="{{ route('cms.announcements.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 flex items-center space-x-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali</span>
        </a>
    </div>

    <form method="POST" action="{{ route('cms.announcements.update', $announcement) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-5">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3">Informasi Utama</h3>

            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Judul Pengumuman <span class="text-rose-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $announcement->title) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-rose-500 @enderror" required />
                @error('title') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="priority" class="block text-sm font-semibold text-gray-700 mb-1">Tingkat Prioritas <span class="text-rose-500">*</span></label>
                    <select name="priority" id="priority" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm" required>
                        <option value="normal" {{ old('priority', $announcement->priority) === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="important" {{ old('priority', $announcement->priority) === 'important' ? 'selected' : '' }}>Important</option>
                        <option value="urgent" {{ old('priority', $announcement->priority) === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Status Publikasi <span class="text-rose-500">*</span></label>
                    <select name="status" id="status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm" required>
                        <option value="draft" {{ old('status', $announcement->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $announcement->status) === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="content" class="block text-sm font-semibold text-gray-700 mb-1">Isi Pengumuman <span class="text-rose-500">*</span></label>
                <textarea name="content" id="content" rows="12" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm font-mono @error('content') border-rose-500 @enderror" required>{{ old('content', $announcement->content) }}</textarea>
                @error('content') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Attachment --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3">Berkas Lampiran</h3>

            @if($announcement->attachment_file)
                <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between">
                    <div class="flex items-center space-x-2 text-xs text-gray-700">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="font-medium">{{ $announcement->attachment_name }}</span>
                    </div>
                    <a href="{{ route('cms.announcements.download', $announcement) }}" class="text-xs font-semibold text-indigo-600 hover:underline">Unduh Berkas</a>
                </div>
            @endif

            <div>
                <input type="file" name="attachment" id="attachment" accept=".pdf,.doc,.docx" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                <p class="text-xs text-gray-400 mt-1">Upload berkas baru untuk mengganti lampiran sebelumnya. Format: PDF, DOC, DOCX. Maks 5MB.</p>
                @error('attachment') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('cms.announcements.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition">Batal</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 shadow-sm transition">Perbarui Pengumuman</button>
        </div>
    </form>
</div>
@endsection
