@extends('layouts.public')

@section('title', 'Dokumen File')
@section('meta_description', 'Pusat dokumen resmi, arsip teknis, modul panduan, dan berkas unduhan dari Terra Tech Indonesia.')

@section('content')
<main class="container">
    <div class="page-hero">
        <h1>Pusat Dokumen File</h1>
        <p>Unduh dokumen resmi, arsip teknis, panduan pengguna, dan berkas dari Terra Tech.</p>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('public.files.index') }}">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari dokumen..." />
            <select name="category" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit">Cari</button>
            @if(request()->hasAny(['search','category']))
                <a href="{{ route('public.files.index') }}" class="btn-reset" style="padding: .6rem 1.25rem; background: #f1f5f9; color: #475569; border: none; border-radius: .75rem; font-size: .875rem; font-weight: 600; cursor: pointer; text-decoration: none;">Reset</a>
            @endif
        </form>
    </div>

    {{-- File List --}}
    @if($files->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            @foreach($files as $item)
                <article style="background: #fff; border: 1px solid #e2e8f0; border-radius: 1.25rem; padding: 1.5rem; transition: transform .2s, box-shadow .2s;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: .75rem;">
                        <div style="display: flex; align-items: center; gap: .5rem;">
                            <span style="font-size: .7rem; font-weight: 700; background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: .2rem .6rem; border-radius: 9999px; text-transform: uppercase;">{{ $item->category->name ?? 'File' }}</span>
                        </div>
                        <span style="font-size: .75rem; color: #94a3b8;">{{ $item->published_at ? $item->published_at->format('d M Y') : '' }}</span>
                    </div>

                    <h2 style="font-size: 1.2rem; font-weight: 700; color: #0f172a; margin-bottom: .5rem;">
                        <a href="{{ route('public.files.show', $item->slug) }}" style="color: inherit;">
                            {{ $item->title }}
                        </a>
                    </h2>

                    @if($item->description)
                        <p style="font-size: .9rem; color: #64748b; line-height: 1.6; margin-bottom: 1rem;">
                            {{ Str::limit(strip_tags($item->description), 180) }}
                        </p>
                    @endif

                    <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #f1f5f9; margin-top: 1rem; padding-top: .75rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <span style="font-size: .75rem; color: #94a3b8; font-weight: 500;">{{ $item->formatted_file_size }}</span>
                            <span style="font-size: .75rem; color: #94a3b8;">{{ number_format($item->downloads_count) }}x unduhan</span>
                        </div>
                        <a href="{{ route('public.files.download', $item->slug) }}" style="font-size: .8rem; font-weight: 600; color: #4f46e5; display: inline-flex; align-items: center; gap: .3rem;">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Unduh</span>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="pagination">
            {{ $files->links() }}
        </div>
    @else
        <div class="empty">
            <p>Belum ada dokumen file yang tersedia untuk diunduh.</p>
        </div>
    @endif
</main>
@endsection
