@extends('layouts.app')

@section('title', $announcement->title . ' — Detail Pengumuman')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Pengumuman</h2>
            <p class="text-sm text-gray-500 mt-1">Preview pengumuman internal/publik.</p>
        </div>
        <div class="flex items-center space-x-2">
            @if(Auth::user()->hasAnyRole(['super_admin','admin','editor']))
                <a href="{{ route('cms.announcements.edit', $announcement) }}" class="inline-flex items-center space-x-1.5 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit</span>
                </a>
            @endif
            <a href="{{ route('cms.announcements.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 flex items-center space-x-1 px-3 py-2.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm">
        {{-- Header Badge & Metadata --}}
        <div class="flex flex-wrap items-center gap-2 mb-5">
            @if($announcement->priority === 'urgent')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                    Urgent
                </span>
            @elseif($announcement->priority === 'important')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                    Important
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    Normal
                </span>
            @endif

            @if($announcement->status === 'published')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Published
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                    Draft
                </span>
            @endif

            <span class="text-xs text-gray-400 font-mono">{{ number_format($announcement->downloads_count) }} kali diunduh</span>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $announcement->title }}</h1>

        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-400 mb-6">
            <span>Dibuat Oleh: <strong class="text-gray-600">{{ $announcement->creator?->name ?? '-' }}</strong></span>
            <span>Dibuat: {{ $announcement->created_at->format('d M Y H:i') }}</span>
            @if($announcement->published_at)
                <span>Dipublikasikan: {{ $announcement->published_at->format('d M Y H:i') }}</span>
            @endif
        </div>

        {{-- Attachment Card --}}
        @if($announcement->attachment_file)
            <div class="p-4 bg-indigo-50/50 border border-indigo-100 rounded-xl mb-6 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-indigo-100 text-indigo-700 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-800">{{ $announcement->attachment_name }}</div>
                        <div class="text-xs text-gray-400">Berkas Lampiran Resmi</div>
                    </div>
                </div>
                <a href="{{ route('cms.announcements.download', $announcement) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">Unduh Berkas</a>
            </div>
        @endif

        <div class="prose max-w-none text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $announcement->content }}</div>

        <div class="mt-8 pt-5 border-t border-gray-100 text-xs text-gray-400">
            <p>Terakhir diperbarui oleh: <strong class="text-gray-600">{{ $announcement->updater?->name ?? '-' }}</strong> pada {{ $announcement->updated_at->format('d M Y H:i') }}</p>
            <p class="font-mono mt-1">Slug: /announcements/{{ $announcement->slug }}</p>
        </div>
    </div>
</div>
@endsection
