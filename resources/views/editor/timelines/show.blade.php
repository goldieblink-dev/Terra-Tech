@extends('layouts.app')

@section('title', 'Detail Timeline: ' . $timeline->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Timeline</h2>
            <p class="text-sm text-gray-500 mt-1">Informasi lengkap agenda timeline dan milestone.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('cms.timelines.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
                Kembali
            </a>
            @if(Auth::user()->hasAnyRole(['super_admin','admin','editor']))
                <a href="{{ route('cms.timelines.edit', $timeline) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                    Edit Agenda
                </a>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-6" style="border-top: 6px solid {{ $timeline->color ?? '#2563eb' }};">
        {{-- Metadata & Badges --}}
        <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-gray-100">
            <div class="flex items-center space-x-2">
                @if($timeline->timeline_status === 'ongoing')
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-semibold uppercase tracking-wider">
                        Sedang Berjalan
                    </span>
                @elseif($timeline->timeline_status === 'upcoming')
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold uppercase tracking-wider">
                        Akan Datang
                    </span>
                @else
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold uppercase tracking-wider">
                        Selesai
                    </span>
                @endif

                @if($timeline->status === 'published')
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-medium">
                        Published
                    </span>
                @else
                    <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-xs font-medium">
                        Draft
                    </span>
                @endif
            </div>

            <div class="text-xs text-gray-500 space-y-0.5 text-right">
                <div>Urutan Sort: <span class="font-mono font-bold text-gray-700">{{ $timeline->sort_order }}</span></div>
                <div>Dibuat oleh: <span class="font-medium text-gray-700">{{ $timeline->creator->name ?? '-' }}</span></div>
            </div>
        </div>

        {{-- Title --}}
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 leading-snug">{{ $timeline->title }}</h1>
        </div>

        {{-- Tanggal & Lokasi Details --}}
        <div class="bg-gray-50 rounded-xl p-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Rentang Tanggal</span>
                <div class="font-semibold text-gray-800">
                    🗓 {{ $timeline->start_date ? $timeline->start_date->format('d M Y') : '-' }}
                    @if($timeline->end_date)
                        &mdash; {{ $timeline->end_date->format('d M Y') }}
                    @endif
                </div>
            </div>

            <div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Lokasi Agenda</span>
                <div class="font-semibold text-gray-800">
                    📍 {{ $timeline->location ?? 'Tidak ditentukan (Online / Global)' }}
                </div>
            </div>
        </div>

        {{-- Deskripsi --}}
        <div class="prose max-w-none text-gray-700 text-sm leading-relaxed">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Deskripsi Agenda</h3>
            <div class="whitespace-pre-line bg-white border border-gray-100 p-4 rounded-xl">
                {!! nl2br(e($timeline->description)) !!}
            </div>
        </div>
    </div>
</div>
@endsection
