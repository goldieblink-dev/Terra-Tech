@extends('layouts.app')

@section('title', 'Editor Dashboard')

@section('content')
<div class="space-y-6">
    <div class="bg-amber-900 text-white rounded-2xl p-6 shadow-lg">
        <h2 class="text-2xl font-bold">Welcome back, {{ Auth::user()->name }}! 👋</h2>
        <p class="mt-2 text-amber-200">Anda berada di Dashboard Editor. Anda dapat mengelola halaman informasi serta pembaruan konten landing page Beranda.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-500">Halaman Informasi</div>
            <div class="text-3xl font-extrabold text-amber-600 mt-2">Active</div>
            <div class="text-xs text-gray-400 mt-1">Artikel & Informasi Perusahaan</div>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-500">Konten Beranda</div>
            <div class="text-3xl font-extrabold text-indigo-600 mt-2">Published</div>
            <div class="text-xs text-gray-400 mt-1">Hero Section & Highlight Banner</div>
        </div>
    </div>
</div>
@endsection
