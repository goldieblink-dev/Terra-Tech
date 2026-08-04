@extends('layouts.app')

@section('title', $informationPost->title . ' — Detail Artikel')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Artikel Informasi</h2>
            <p class="text-sm text-gray-500 mt-1">Preview artikel sebelum dipublikasikan.</p>
        </div>
        <div class="flex items-center space-x-2">
            @if(Auth::user()->hasAnyRole(['super_admin','admin','editor']))
                <a href="{{ route('cms.information.edit', $informationPost) }}" class="inline-flex items-center space-x-1.5 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit</span>
                </a>
            @endif
            <a href="{{ route('cms.information.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 flex items-center space-x-1 px-3 py-2.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm">
        {{-- Status & Category --}}
        <div class="flex flex-wrap items-center gap-2 mb-5">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-indigo-50 text-indigo-800 border border-indigo-200">
                {{ $informationPost->category?->name ?? 'Tanpa Kategori' }}
            </span>
            @if($informationPost->status === 'published')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>Published
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>Draft
                </span>
            @endif
            <span class="text-xs text-gray-400">{{ number_format($informationPost->views_count) }} views</span>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $informationPost->title }}</h1>

        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-400 mb-6">
            <span>Oleh: <strong class="text-gray-600">{{ $informationPost->creator?->name ?? '-' }}</strong></span>
            <span>Dibuat: {{ $informationPost->created_at->format('d M Y H:i') }}</span>
            @if($informationPost->published_at)
                <span>Dipublikasikan: {{ $informationPost->published_at->format('d M Y H:i') }}</span>
            @endif
        </div>

        @if($informationPost->featured_image_url)
            <img src="{{ $informationPost->featured_image_url }}" alt="{{ $informationPost->featured_image_alt }}" class="w-full max-h-72 object-cover rounded-xl mb-6 border border-gray-200" />
        @endif

        @if($informationPost->excerpt)
            <p class="text-gray-600 text-base italic border-l-4 border-indigo-300 pl-4 mb-6 leading-relaxed">{{ $informationPost->excerpt }}</p>
        @endif

        <div class="prose max-w-none text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $informationPost->content }}</div>

        {{-- SEO Meta --}}
        @if($informationPost->meta_title || $informationPost->meta_description)
            <div class="mt-8 p-4 bg-gray-50 border border-gray-200 rounded-xl">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">SEO Meta</p>
                @if($informationPost->meta_title)
                    <p class="text-sm text-gray-700"><span class="font-semibold">Meta Title:</span> {{ $informationPost->meta_title }}</p>
                @endif
                @if($informationPost->meta_description)
                    <p class="text-sm text-gray-700 mt-1"><span class="font-semibold">Meta Description:</span> {{ $informationPost->meta_description }}</p>
                @endif
            </div>
        @endif

        <div class="mt-6 pt-5 border-t border-gray-100 text-xs text-gray-400">
            <p>Terakhir diperbarui oleh: <strong class="text-gray-600">{{ $informationPost->updater?->name ?? '-' }}</strong> pada {{ $informationPost->updated_at->format('d M Y H:i') }}</p>
            <p class="font-mono mt-1">Slug: /information/{{ $informationPost->slug }}</p>
        </div>
    </div>
</div>
@endsection
