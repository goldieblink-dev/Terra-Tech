@extends('layouts.public')

@section('title', $announcement->title . ' — Pengumuman')
@section('meta_description', Str::limit(strip_tags($announcement->content), 160))

@section('content')
<main class="container" style="max-width: 800px;">
    <article style="background: #fff; border: 1px solid #e2e8f0; border-radius: 1.25rem; padding: 2.5rem; margin-top: 1rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <div>
                @if($announcement->priority === 'urgent')
                    <span style="font-size: .7rem; font-weight: 700; background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; padding: .2rem .6rem; border-radius: 9999px; text-transform: uppercase;">Urgent</span>
                @elseif($announcement->priority === 'important')
                    <span style="font-size: .7rem; font-weight: 700; background: #fffbeb; color: #d97706; border: 1px solid #fef3c7; padding: .2rem .6rem; border-radius: 9999px; text-transform: uppercase;">Important</span>
                @else
                    <span style="font-size: .7rem; font-weight: 700; background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; padding: .2rem .6rem; border-radius: 9999px; text-transform: uppercase;">Normal</span>
                @endif
            </div>
            <span style="font-size: .85rem; color: #94a3b8;">{{ $announcement->published_at ? $announcement->published_at->format('d M Y') : '' }}</span>
        </div>

        <h1 style="font-size: 2.25rem; font-weight: 800; color: #0f172a; line-height: 1.3; margin-bottom: 1.5rem;">
            {{ $announcement->title }}
        </h1>

        {{-- Attachment Card --}}
        @if($announcement->attachment_file)
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: .85rem; padding: 1.25rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: .75rem;">
                    <svg style="width: 28px; height: 28px; color: #4f46e5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <div>
                        <div style="font-size: .9rem; font-weight: 700; color: #1e293b;">{{ $announcement->attachment_name }}</div>
                        <div style="font-size: .75rem; color: #94a3b8;">Berkas Lampiran Resmi ({{ number_format($announcement->downloads_count) }} kali diunduh)</div>
                    </div>
                </div>
                <a href="{{ route('public.announcements.download', $announcement->slug) }}" style="background: #4f46e5; color: #fff; padding: .6rem 1.2rem; border-radius: .6rem; font-size: .8rem; font-weight: 600;">
                    Unduh Berkas
                </a>
            </div>
        @endif

        <div style="font-size: 1.05rem; color: #334155; line-height: 1.8; white-space: pre-wrap;">{{ $announcement->content }}</div>
    </article>

    @if($recentAnnouncements->count() > 0)
        <section style="margin-top: 3rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin-bottom: 1.5rem;">Pengumuman Lainnya</h2>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($recentAnnouncements as $recent)
                    <article style="background: #fff; border: 1px solid #e2e8f0; border-radius: .85rem; padding: 1rem 1.25rem;">
                        <h3 style="font-size: 1rem; font-weight: 700;">
                            <a href="{{ route('public.announcements.show', $recent->slug) }}" style="color: #0f172a;">{{ $recent->title }}</a>
                        </h3>
                        <div style="font-size: .75rem; color: #94a3b8; margin-top: .4rem;">
                            {{ $recent->published_at ? $recent->published_at->format('d M Y') : '' }}
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</main>
@endsection
