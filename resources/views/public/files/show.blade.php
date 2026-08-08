@extends('layouts.public')

@section('title', $fileItem->title . ' — Dokumen File')
@section('meta_description', Str::limit(strip_tags($fileItem->description ?? $fileItem->title), 160))

@section('content')
<main class="container" style="max-width: 800px;">
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('public.files.index') }}" style="font-size: .875rem; color: #64748b; font-weight: 500; display: inline-flex; align-items: center; gap: .4rem;">
            &larr; Kembali ke Dokumen File
        </a>
    </div>

    <article style="background: #fff; border: 1px solid #e2e8f0; border-top: 6px solid #4f46e5; border-radius: 1.25rem; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
            <span style="font-size: .75rem; font-weight: 700; background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: .25rem .65rem; border-radius: 9999px; text-transform: uppercase;">{{ $fileItem->category->name ?? 'File' }}</span>
            <span style="font-size: .85rem; color: #64748b;">{{ $fileItem->published_at ? $fileItem->published_at->format('d M Y') : '' }}</span>
        </div>

        <h1 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-bottom: 1.5rem; line-height: 1.3;">
            {{ $fileItem->title }}
        </h1>

        {{-- File Info Card --}}
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.25rem; margin-bottom: 1.5rem;">
            <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: center; margin-bottom: 1rem;">
                <div>
                    <div style="font-size: .7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; margin-bottom: .25rem;">Nama Berkas</div>
                    <div style="font-size: .9rem; font-weight: 600; color: #1e293b; font-family: monospace; word-break: break-all;">{{ $fileItem->original_name }}</div>
                </div>
                <div>
                    <div style="font-size: .7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; margin-bottom: .25rem;">Ukuran</div>
                    <div style="font-size: .9rem; font-weight: 600; color: #1e293b;">{{ $fileItem->formatted_file_size }}</div>
                </div>
                <div>
                    <div style="font-size: .7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; margin-bottom: .25rem;">Total Unduhan</div>
                    <div style="font-size: .9rem; font-weight: 600; color: #1e293b;">{{ number_format($fileItem->downloads_count) }} kali</div>
                </div>
            </div>

            <a href="{{ route('public.files.download', $fileItem->slug) }}" style="display: inline-flex; align-items: center; gap: .5rem; padding: .7rem 1.5rem; background: #4f46e5; color: #fff; border-radius: .75rem; font-size: .9rem; font-weight: 600; text-decoration: none; transition: background .2s;">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Unduh Berkas
            </a>
        </div>

        @if($fileItem->description)
            <div style="font-size: 1rem; color: #334155; line-height: 1.8; margin-top: 1rem;">
                {!! nl2br(e($fileItem->description)) !!}
            </div>
        @endif
    </article>
</main>
@endsection
