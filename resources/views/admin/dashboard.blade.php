@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <div class="bg-blue-900 text-white rounded-2xl p-6 shadow-lg">
        <h2 class="text-2xl font-bold">Welcome back, {{ Auth::user()->name }}! 👋</h2>
        <p class="mt-2 text-blue-200">Anda berada di Dashboard Admin. Anda dapat mengelola konten website, operator, editor, dan konfigurasi profil perusahaan.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-500">Konten Website</div>
            <div class="text-3xl font-extrabold text-blue-600 mt-2">Active</div>
            <div class="text-xs text-gray-400 mt-1">Manajemen Konten Utama</div>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-500">Operator & Editor</div>
            <div class="text-3xl font-extrabold text-emerald-600 mt-2">Managed</div>
            <div class="text-xs text-gray-400 mt-1">Pengawasan Tim Konten</div>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-500">Profil Perusahaan</div>
            <div class="text-3xl font-extrabold text-amber-600 mt-2">Configured</div>
            <div class="text-xs text-gray-400 mt-1">Pengaturan Identitas Terra Tech</div>
        </div>
    </div>
</div>
@endsection
