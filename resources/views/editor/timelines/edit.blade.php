@extends('layouts.app')

@section('title', 'Edit Timeline')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Timeline</h2>
            <p class="text-sm text-gray-500 mt-1">Ubah rincian agenda, tanggal, atau status timeline.</p>
        </div>
        <a href="{{ route('cms.timelines.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
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

    <form method="POST" action="{{ route('cms.timelines.update', $timeline) }}" class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        {{-- Judul --}}
        <div>
            <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Judul Agenda / Milestone <span class="text-rose-500">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title', $timeline->title) }}" required placeholder="Contoh: Peresmian Kantor Cabang Surabaya" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>

        {{-- Deskripsi --}}
        <div>
            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Lengkap <span class="text-rose-500">*</span></label>
            <textarea name="description" id="description" rows="5" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $timeline->description) }}</textarea>
        </div>

        {{-- Tanggal Mulai & Tanggal Selesai --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $timeline->start_date ? $timeline->start_date->format('Y-m-d') : '') }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>

            <div>
                <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Selesai <span class="text-xs font-normal text-gray-500">(Opsional)</span></label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $timeline->end_date ? $timeline->end_date->format('Y-m-d') : '') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>
        </div>

        {{-- Lokasi & Icon --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="location" class="block text-sm font-semibold text-gray-700 mb-1">Lokasi <span class="text-xs font-normal text-gray-500">(Opsional)</span></label>
                <input type="text" name="location" id="location" value="{{ old('location', $timeline->location) }}" placeholder="Contoh: Jakarta Convention Center" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>

            <div>
                <label for="icon" class="block text-sm font-semibold text-gray-700 mb-1">Nama Ikon <span class="text-xs font-normal text-gray-500">(Opsional)</span></label>
                <input type="text" name="icon" id="icon" value="{{ old('icon', $timeline->icon) }}" placeholder="Contoh: calendar" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>
        </div>

        {{-- Warna Accent & Urutan Sort --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="color" class="block text-sm font-semibold text-gray-700 mb-1">Warna Aksen Penanda</label>
                <div class="flex items-center space-x-3">
                    <input type="color" name="color" id="color" value="{{ old('color', $timeline->color ?? '#2563eb') }}" class="h-10 w-16 p-1 bg-gray-50 border border-gray-300 rounded-xl cursor-pointer" />
                    <span class="text-xs text-gray-500">Pilih warna penanda timeline di tampilan publik.</span>
                </div>
            </div>

            <div>
                <label for="sort_order" class="block text-sm font-semibold text-gray-700 mb-1">Urutan Tampil (Sort Order)</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $timeline->sort_order) }}" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>
        </div>

        {{-- Status Publish --}}
        <div>
            <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Status Publikasi <span class="text-rose-500">*</span></label>
            <select name="status" id="status" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="draft" {{ old('status', $timeline->status) === 'draft' ? 'selected' : '' }}>Draft (Disimpan lebih dulu)</option>
                <option value="published" {{ old('status', $timeline->status) === 'published' ? 'selected' : '' }}>Published (Tampil di publik)</option>
            </select>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
            <a href="{{ route('cms.timelines.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">Batal</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">Perbarui Timeline</button>
        </div>
    </form>
</div>
@endsection
