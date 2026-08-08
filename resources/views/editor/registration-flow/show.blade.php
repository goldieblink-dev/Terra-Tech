@extends('layouts.app')

@section('title', 'Detail Langkah: ' . $registrationStep->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Langkah Alur Pendaftaran</h2>
            <p class="text-sm text-gray-500 mt-1">Pratinjau petunjuk, daftar persyaratan, dan status publikasi.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('cms.registration-steps.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
                Kembali
            </a>
            @if(Auth::user()->hasAnyRole(['super_admin','admin','editor']))
                <a href="{{ route('cms.registration-steps.edit', $registrationStep) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                    Edit Langkah
                </a>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-6">
        {{-- Header & Badges --}}
        <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-xs font-bold font-mono">
                    Urutan {{ $registrationStep->sort_order }}
                </span>
                @if($registrationStep->status === 'published')
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-medium">Published</span>
                @else
                    <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-xs font-medium">Draft</span>
                @endif
            </div>

            <div class="text-xs text-gray-500 text-right space-y-0.5">
                <div>Dibuat oleh: <span class="font-medium text-gray-700">{{ $registrationStep->creator->name ?? '-' }}</span></div>
                <div>Slug: <span class="font-mono text-gray-700">{{ $registrationStep->slug }}</span></div>
            </div>
        </div>

        {{-- Illustration Image Preview --}}
        @if($registrationStep->illustration_image_url)
            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 flex justify-center">
                <img src="{{ $registrationStep->illustration_image_url }}" alt="{{ $registrationStep->title }}" class="max-h-64 rounded-xl object-contain" />
            </div>
        @endif

        {{-- Title --}}
        <h1 class="text-2xl font-extrabold text-gray-900 leading-snug">{{ $registrationStep->title }}</h1>

        {{-- Deskripsi --}}
        <div>
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Deskripsi & Petunjuk</h3>
            <div class="whitespace-pre-line bg-gray-50 border border-gray-100 p-4 rounded-xl text-sm text-gray-700 leading-relaxed">
                {!! nl2br(e($registrationStep->description)) !!}
            </div>
        </div>

        {{-- Requirements Bullet List Preview --}}
        <div>
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Pratinjau Daftar Persyaratan (Requirements)</h3>
            @if(is_array($registrationStep->requirements) && count($registrationStep->requirements) > 0)
                <div class="bg-indigo-50/50 border border-indigo-100 p-4 rounded-xl">
                    <ul class="space-y-2 text-sm text-gray-800">
                        @foreach($registrationStep->requirements as $req)
                            <li class="flex items-start space-x-2.5">
                                <svg class="w-5 h-5 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span class="font-medium">{{ $req }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="bg-gray-50 border border-gray-100 p-4 rounded-xl text-xs text-gray-500 italic">
                    Tidak ada poin persyaratan khusus yang ditentukan untuk langkah ini.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
