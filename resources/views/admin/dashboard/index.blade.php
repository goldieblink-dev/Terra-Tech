@extends('layouts.app')

@section('title', 'Dashboard Operasional')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Hero Header (~120px height, compact & SaaS style) --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-700 via-indigo-800 to-slate-900 text-white rounded-xl p-5 shadow-sm border border-indigo-800 dark:border-indigo-950 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1 z-10">
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 bg-white/15 text-white backdrop-blur-md border border-white/20 rounded-full text-xs font-semibold uppercase tracking-wider">
                    {{ Auth::user()->roles->pluck('name')->implode(', ') ?: 'User' }}
                </span>
                <span class="text-xs text-indigo-200">
                    &bull; {{ now()->isoFormat('D MMMM YYYY') }}
                </span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">
                Dashboard Operasional
            </h1>
            <p class="text-xs text-indigo-100 max-w-xl">
                Halo, <span class="font-semibold text-white">{{ Auth::user()->name }}</span>. Pusat pengawasan real-time konten, draft, analitik, dan status infrastruktur Terra Tech CMS.
            </p>
        </div>

        <div class="flex flex-row md:flex-col items-center md:items-end justify-between gap-2 z-10 shrink-0 border-t md:border-t-0 border-white/10 pt-3 md:pt-0">
            <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 bg-black/25 backdrop-blur-md rounded-md text-xs text-indigo-200 border border-white/10">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Cache 60 Detik</span>
            </div>
            <span class="text-xs text-indigo-200">
                Pembaruan: <span class="font-mono text-white font-semibold">{{ now()->format('H:i:s') }} WIB</span>
            </span>
        </div>
    </div>

    {{-- 8 Metric Cards (Grid 4-col desktop, height ~120px) --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white tracking-tight flex items-center space-x-2">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Statistik Konten</span>
            </h2>
            <span class="text-xs text-slate-500 dark:text-slate-400">Total akumulasi data</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            {{-- 1. Users --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md transition group h-[120px] flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Users</span>
                    <div class="p-2 rounded-lg bg-violet-50 text-violet-600 dark:bg-violet-950/50 dark:text-violet-400 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($stats['total_users']) }}</div>
                    <div class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Pengguna terdaftar</div>
                </div>
            </div>

            {{-- 2. Informasi --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md transition group h-[120px] flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Informasi</span>
                    <div class="p-2 rounded-lg bg-sky-50 text-sky-600 dark:bg-sky-950/50 dark:text-sky-400 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($stats['total_information_posts']) }}</div>
                    <div class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Artikel & berita</div>
                </div>
            </div>

            {{-- 3. Pengumuman --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md transition group h-[120px] flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Pengumuman</span>
                    <div class="p-2 rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($stats['total_announcements']) }}</div>
                    <div class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Pengumuman resmi</div>
                </div>
            </div>

            {{-- 4. Timeline --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md transition group h-[120px] flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Timeline</span>
                    <div class="p-2 rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($stats['total_timelines']) }}</div>
                    <div class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Agenda kegiatan</div>
                </div>
            </div>

            {{-- 5. Dokumen File --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md transition group h-[120px] flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Dokumen File</span>
                    <div class="p-2 rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($stats['total_files']) }}</div>
                    <div class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Berkas dokumen</div>
                </div>
            </div>

            {{-- 6. Langkah Pendaftaran --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md transition group h-[120px] flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Langkah Pendaftaran</span>
                    <div class="p-2 rounded-lg bg-cyan-50 text-cyan-600 dark:bg-cyan-950/50 dark:text-cyan-400 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($stats['total_registration_steps']) }}</div>
                    <div class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Tahapan alur</div>
                </div>
            </div>

            {{-- 7. Download File --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md transition group h-[120px] flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Download File</span>
                    <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($stats['total_file_downloads']) }}</div>
                    <div class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Total unduhan berkas</div>
                </div>
            </div>

            {{-- 8. Download Pengumuman --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 hover:shadow-md transition group h-[120px] flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Download Pengumuman</span>
                    <div class="p-2 rounded-lg bg-purple-50 text-purple-600 dark:bg-purple-950/50 dark:text-purple-400 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($stats['total_announcement_downloads']) }}</div>
                    <div class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Total unduhan lampiran</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Draft Monitoring + System Health (Side-by-Side 2 Cols) --}}
    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Draft Monitoring --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center space-x-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Draft Monitoring</span>
                </h3>
                <span class="text-xs text-slate-500 dark:text-slate-400">Penyuntingan pending</span>
            </div>

            <div class="space-y-2">
                @php
                    $draftRows = [
                        ['label' => 'Draft Informasi', 'count' => $drafts['draft_information'], 'route' => 'cms.information.index'],
                        ['label' => 'Draft Pengumuman', 'count' => $drafts['draft_announcements'], 'route' => 'cms.announcements.index'],
                        ['label' => 'Draft Timeline', 'count' => $drafts['draft_timelines'], 'route' => 'cms.timelines.index'],
                        ['label' => 'Draft Dokumen File', 'count' => $drafts['draft_files'], 'route' => 'cms.files.index'],
                        ['label' => 'Draft Alur Pendaftaran', 'count' => $drafts['draft_registration_steps'], 'route' => 'cms.registration-steps.index'],
                    ];
                @endphp

                @foreach($draftRows as $row)
                    <a href="{{ route($row['route']) }}" class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg border border-slate-100 dark:border-slate-800 transition">
                        <div class="flex items-center space-x-2.5">
                            <span class="w-2 h-2 rounded-full {{ $row['count'] > 0 ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $row['label'] }}</span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $row['count'] > 0 ? 'bg-rose-100 text-rose-700 border-rose-200 dark:bg-rose-950/50 dark:text-rose-400 dark:border-rose-800' : 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-400 dark:border-emerald-800' }}">
                            {{ $row['count'] }} item
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- System Health --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>System Health</span>
                </h3>
                <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-400 dark:border-emerald-800">
                    Healthy
                </span>
            </div>

            <div class="space-y-2">
                {{-- DB --}}
                <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-100 dark:border-slate-800 text-xs">
                    <span class="font-medium text-slate-600 dark:text-slate-400">Database Connection</span>
                    @if($health['db_status'] === 'Connected')
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 font-bold dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">✓ Connected</span>
                    @else
                        <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 font-bold dark:bg-rose-950/50 dark:text-rose-400 border border-rose-200 dark:border-rose-800">✗ Error</span>
                    @endif
                </div>

                {{-- Cache --}}
                <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-100 dark:border-slate-800 text-xs">
                    <span class="font-medium text-slate-600 dark:text-slate-400">Cache Engine</span>
                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 font-bold uppercase dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">{{ $health['cache_driver'] }}</span>
                </div>

                {{-- Storage --}}
                <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-100 dark:border-slate-800 text-xs">
                    <span class="font-medium text-slate-600 dark:text-slate-400">Storage Usage</span>
                    <span class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ $health['storage_readable'] }}</span>
                </div>

                {{-- PHP --}}
                <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-100 dark:border-slate-800 text-xs">
                    <span class="font-medium text-slate-600 dark:text-slate-400">PHP Version</span>
                    <span class="font-mono font-bold text-slate-800 dark:text-slate-200">v{{ $health['php_version'] }}</span>
                </div>

                {{-- Laravel --}}
                <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-100 dark:border-slate-800 text-xs">
                    <span class="font-medium text-slate-600 dark:text-slate-400">Laravel Version</span>
                    <span class="font-mono font-bold text-slate-800 dark:text-slate-200">v{{ $health['laravel_version'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Analytics (Top Downloaded Files & Top Viewed Information) --}}
    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Top Downloaded Files --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center space-x-2">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Top Downloaded Files</span>
                </h3>
                <span class="text-xs text-slate-500 dark:text-slate-400">Maks. 5 teratas</span>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($topFiles->take(5) as $index => $file)
                    <div class="py-2.5 flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-3 min-w-0">
                            <span class="w-6 h-6 rounded-md bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-400 font-bold flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-900">
                                #{{ $index + 1 }}
                            </span>
                            <div class="min-w-0">
                                <div class="font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $file->title }}</div>
                                <div class="text-slate-400 truncate">{{ $file->original_name }}</div>
                            </div>
                        </div>
                        <span class="ml-2 px-2.5 py-0.5 rounded-full font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900 shrink-0">
                            {{ number_format($file->downloads_count) }}×
                        </span>
                    </div>
                @empty
                    <div class="py-6 text-center text-xs text-slate-400">Belum ada data unduhan.</div>
                @endforelse
            </div>
        </div>

        {{-- Top Viewed Information --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center space-x-2">
                    <svg class="w-5 h-5 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>Top Viewed Information</span>
                </h3>
                <span class="text-xs text-slate-500 dark:text-slate-400">Maks. 5 teratas</span>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($topPosts->take(5) as $index => $post)
                    <div class="py-2.5 flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-3 min-w-0">
                            <span class="w-6 h-6 rounded-md bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-400 font-bold flex items-center justify-center shrink-0 border border-sky-100 dark:border-sky-900">
                                #{{ $index + 1 }}
                            </span>
                            <div class="min-w-0">
                                <div class="font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $post->title }}</div>
                                <div class="text-slate-400 truncate">{{ $post->slug }}</div>
                            </div>
                        </div>
                        <span class="ml-2 px-2.5 py-0.5 rounded-full font-bold bg-sky-50 text-sky-700 dark:bg-sky-950/50 dark:text-sky-400 border border-sky-100 dark:border-sky-900 shrink-0">
                            {{ number_format($post->views_count) }}×
                        </span>
                    </div>
                @empty
                    <div class="py-6 text-center text-xs text-slate-400">Belum ada tayangan informasi.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Latest Activities (Timeline Vertikal Kompak) --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center space-x-2">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Latest Activities</span>
            </h3>
            <span class="text-xs text-slate-500 dark:text-slate-400">Pembaruan terkini</span>
        </div>

        @php
            $unifiedActivities = collect();

            foreach ($activities['latest_information'] as $item) {
                $unifiedActivities->push((object)[
                    'type' => 'Informasi',
                    'title' => $item->title,
                    'creator' => $item->creator->name ?? 'System',
                    'created_at' => $item->created_at,
                    'badge' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-400 border-sky-100 dark:border-sky-900',
                ]);
            }
            foreach ($activities['latest_announcements'] as $item) {
                $unifiedActivities->push((object)[
                    'type' => 'Pengumuman',
                    'title' => $item->title,
                    'creator' => $item->creator->name ?? 'System',
                    'created_at' => $item->created_at,
                    'badge' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border-amber-100 dark:border-amber-900',
                ]);
            }
            foreach ($activities['latest_timelines'] as $item) {
                $unifiedActivities->push((object)[
                    'type' => 'Timeline',
                    'title' => $item->title,
                    'creator' => $item->creator->name ?? 'System',
                    'created_at' => $item->created_at,
                    'badge' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border-emerald-100 dark:border-emerald-900',
                ]);
            }
            foreach ($activities['latest_files'] as $item) {
                $unifiedActivities->push((object)[
                    'type' => 'Dokumen File',
                    'title' => $item->title,
                    'creator' => $item->creator->name ?? 'System',
                    'created_at' => $item->created_at,
                    'badge' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border-rose-100 dark:border-rose-900',
                ]);
            }
            foreach ($activities['latest_registration_steps'] as $item) {
                $unifiedActivities->push((object)[
                    'type' => 'Langkah Pendaftaran',
                    'title' => $item->title,
                    'creator' => $item->creator->name ?? 'System',
                    'created_at' => $item->created_at,
                    'badge' => 'bg-cyan-50 text-cyan-700 dark:bg-cyan-950/60 dark:text-cyan-400 border-cyan-100 dark:border-cyan-900',
                ]);
            }

            $sortedActivities = $unifiedActivities->sortByDesc('created_at')->take(8);
        @endphp

        <div class="relative pl-5 space-y-3 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-800">
            @forelse($sortedActivities as $act)
                <div class="relative flex items-center justify-between text-xs">
                    <span class="absolute -left-[17px] top-1.5 w-2.5 h-2.5 rounded-full bg-indigo-600 dark:bg-indigo-400 ring-4 ring-white dark:ring-slate-900"></span>
                    <div class="flex items-center space-x-2.5 min-w-0 pr-2">
                        <span class="px-2 py-0.5 rounded text-[11px] font-bold border shrink-0 {{ $act->badge }}">
                            {{ $act->type }}
                        </span>
                        <span class="font-medium text-slate-800 dark:text-slate-200 truncate">{{ $act->title }}</span>
                        <span class="text-slate-400 dark:text-slate-500 hidden sm:inline">&bull; {{ $act->creator }}</span>
                    </div>
                    <span class="text-slate-400 dark:text-slate-500 font-mono text-[11px] shrink-0">
                        {{ $act->created_at ? $act->created_at->diffForHumans() : '-' }}
                    </span>
                </div>
            @empty
                <div class="py-4 text-center text-xs text-slate-400">Belum ada riwayat aktivitas terbaru.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
