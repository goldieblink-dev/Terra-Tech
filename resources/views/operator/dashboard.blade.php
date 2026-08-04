@extends('layouts.app')

@section('title', 'Operator Dashboard')

@section('content')
<div class="space-y-6">
    <div class="bg-emerald-900 text-white rounded-2xl p-6 shadow-lg">
        <h2 class="text-2xl font-bold">Welcome back, {{ Auth::user()->name }}! 👋</h2>
        <p class="mt-2 text-emerald-200">Anda berada di Dashboard Operator. Anda bertanggung jawab dalam mengelola pengumuman, timeline acara, dan manajemen berkas/file.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-500">Pengumuman</div>
            <div class="text-3xl font-extrabold text-emerald-600 mt-2">Active</div>
            <div class="text-xs text-gray-400 mt-1">Publikasi Pengumuman Terbaru</div>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-500">Timeline</div>
            <div class="text-3xl font-extrabold text-blue-600 mt-2">Up to Date</div>
            <div class="text-xs text-gray-400 mt-1">Jadwal Agenda Perusahaan</div>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-sm font-semibold text-gray-500">File Manager</div>
            <div class="text-3xl font-extrabold text-purple-600 mt-2">Ready</div>
            <div class="text-xs text-gray-400 mt-1">Penyimpanan Berkas Publik</div>
        </div>
    </div>
</div>
@endsection
