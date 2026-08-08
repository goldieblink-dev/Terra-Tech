@extends('layouts.app')

@section('title', 'Detail File: ' . $fileItem->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Dokumen File</h2>
            <p class="text-sm text-gray-500 mt-1">Informasi lengkap berkas dokumen dan statistik unduhan.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('cms.files.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
                Kembali
            </a>
            @if(Auth::user()->hasAnyRole(['super_admin','admin','editor']))
                <a href="{{ route('cms.files.edit', $fileItem) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                    Edit
                </a>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-6">
        {{-- Badges --}}
        <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-gray-100">
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                    {{ $fileItem->category->name ?? 'Uncategorized' }}
                </span>
                @if($fileItem->status === 'published')
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-medium">Published</span>
                @else
                    <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-xs font-medium">Draft</span>
                @endif
            </div>

            <div class="text-xs text-gray-500 text-right">
                <div>Dibuat oleh: <span class="font-medium text-gray-700">{{ $fileItem->creator->name ?? '-' }}</span></div>
                @if($fileItem->published_at)
                    <div>Dipublikasikan: <span class="font-medium text-gray-700">{{ $fileItem->published_at->format('d M Y H:i') }}</span></div>
                @endif
            </div>
        </div>

        {{-- Title --}}
        <h1 class="text-2xl font-extrabold text-gray-900 leading-snug">{{ $fileItem->title }}</h1>

        {{-- Berkas Info Card --}}
        <div class="bg-gray-50 rounded-xl p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Nama Berkas Asli</span>
                <div class="font-mono font-semibold text-gray-800 break-all">{{ $fileItem->original_name }}</div>
            </div>
            <div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Ukuran & Tipe MIME</span>
                <div class="font-semibold text-gray-800">{{ $fileItem->formatted_file_size }} &bull; {{ $fileItem->mime_type }}</div>
            </div>
            <div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Total Unduhan</span>
                <div class="font-semibold text-gray-800">{{ number_format($fileItem->downloads_count) }} kali diunduh</div>
            </div>
            <div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Terakhir Diperbarui</span>
                <div class="font-semibold text-gray-800">{{ $fileItem->updated_at->format('d M Y H:i') }}</div>
            </div>
        </div>

        {{-- Download Button --}}
        <div class="flex items-center space-x-3">
            <a href="{{ route('cms.files.download', $fileItem) }}" class="inline-flex items-center space-x-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Unduh Berkas</span>
            </a>
        </div>

        {{-- Deskripsi --}}
        @if($fileItem->description)
            <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Deskripsi Dokumen</h3>
                <div class="whitespace-pre-line bg-white border border-gray-100 p-4 rounded-xl text-sm text-gray-700 leading-relaxed">
                    {!! nl2br(e($fileItem->description)) !!}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
