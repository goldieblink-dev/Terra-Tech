@extends('layouts.public')

@section('title', 'Pengumuman Resmi')
@section('meta_description', 'Portal pengumuman dan rilis resmi dari Terra Tech Indonesia.')

@section('content')
<main class="container">
    <div class="page-hero">
        <h1>Pengumuman Resmi</h1>
        <p>Informasi edaran, instruksi teknis, dan pembaruan resmi Terra Tech.</p>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('public.announcements.index') }}">
            <select name="priority" onchange="this.form.submit()">
                <option value="">Semua Prioritas</option>
                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                <option value="important" {{ request('priority') === 'important' ? 'selected' : '' }}>Important</option>
                <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
            </select>
            @if(request()->filled('priority'))
                <a href="{{ route('public.announcements.index') }}" class="button btn-reset font-medium text-xs rounded-xl px-4 py-2 flex items-center">Reset Filter</a>
            @endif
        </form>
    </div>

    {{-- Announcement List --}}
    @if($announcements->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            @foreach($announcements as $item)
                <article style="background: #fff; border: 1px solid #e2e8f0; border-radius: 1.25rem; padding: 1.5rem; transition: transform .2s, box-shadow .2s;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: .75rem;">
                        <div>
                            @if($item->priority === 'urgent')
                                <span style="font-size: .7rem; font-weight: 700; background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; padding: .2rem .6rem; border-radius: 9999px; text-transform: uppercase;">Urgent</span>
                            @elseif($item->priority === 'important')
                                <span style="font-size: .7rem; font-weight: 700; background: #fffbeb; color: #d97706; border: 1px solid #fef3c7; padding: .2rem .6rem; border-radius: 9999px; text-transform: uppercase;">Important</span>
                            @else
                                <span style="font-size: .7rem; font-weight: 700; background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; padding: .2rem .6rem; border-radius: 9999px; text-transform: uppercase;">Normal</span>
                            @endif
                        </div>
                        <span style="font-size: .75rem; color: #94a3b8;">{{ $item->published_at ? $item->published_at->format('d M Y') : '' }}</span>
                    </div>

                    <h2 style="font-size: 1.2rem; font-weight: 700; color: #0f172a; margin-bottom: .5rem;">
                        <a href="{{ route('public.announcements.show', $item->slug) }}" style="color: inherit;">
                            {{ $item->title }}
                        </a>
                    </h2>

                    <p style="font-size: .9rem; color: #64748b; line-height: 1.6; margin-bottom: 1rem;">
                        {{ Str::limit(strip_tags($item->content), 180) }}
                    </p>

                    <div style="display: flex; align-items: center; justify-content: space-between; pt-3; border-top: 1px solid #f1f5f9; margin-top: 1rem; padding-top: .75rem;">
                        @if($item->attachment_file)
                            <a href="{{ route('public.announcements.download', $item->slug) }}" style="font-size: .8rem; font-weight: 600; color: #4f46e5; display: inline-flex; align-items: center; gap: .3rem;">
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Unduh Lampiran ({{ number_format($item->downloads_count) }})</span>
                            </a>
                        @else
                            <span></span>
                        @endif

                        <a href="{{ route('public.announcements.show', $item->slug) }}" style="font-size: .8rem; font-weight: 600; color: #4f46e5;">Baca Selengkapnya &rarr;</a>
                    </div>
                </article>
            @endforeach
        </div>

        @if($announcements->hasPages())
            <div class="pagination">
                {{ $announcements->links() }}
            </div>
        @endif
    @else
        <div class="empty">
            <p>Belum ada pengumuman resmi yang dipublikasikan pada kategori ini.</p>
        </div>
    @endif
</main>
@endsection
