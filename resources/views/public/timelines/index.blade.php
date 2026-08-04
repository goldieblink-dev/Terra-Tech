@extends('layouts.public')

@section('title', 'Timeline & Milestone')
@section('meta_description', 'Jadwal, perintis milestone, dan agenda perkembangan Terra Tech Indonesia.')

@section('content')
<main class="container">
    <div class="page-hero">
        <h1>Timeline & Milestone</h1>
        <p>Jadwal kegiatan, agenda penting, dan rekam jejak perkembangan Terra Tech.</p>
    </div>

    {{-- Timeline Card List --}}
    @if($timelines->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 850px; margin: 0 auto;">
            @foreach($timelines as $item)
                <article style="background: #fff; border: 1px solid #e2e8f0; border-left: 6px solid {{ $item->color ?? '#2563eb' }}; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.03); transition: transform .2s, box-shadow .2s;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: .75rem; margin-bottom: .75rem;">
                        <div style="display: flex; align-items: center; gap: .5rem;">
                            {{-- Badge Timeline Status --}}
                            @if($item->timeline_status === 'ongoing')
                                <span style="font-size: .75rem; font-weight: 700; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: .25rem .65rem; border-radius: 9999px; text-transform: uppercase;">Sedang Berjalan</span>
                            @elseif($item->timeline_status === 'upcoming')
                                <span style="font-size: .75rem; font-weight: 700; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: .25rem .65rem; border-radius: 9999px; text-transform: uppercase;">Akan Datang</span>
                            @else
                                <span style="font-size: .75rem; font-weight: 700; background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: .25rem .65rem; border-radius: 9999px; text-transform: uppercase;">Selesai</span>
                            @endif
                        </div>

                        {{-- Date range & location --}}
                        <div style="font-size: .8rem; color: #64748b; font-weight: 500;">
                            <span>{{ $item->start_date ? $item->start_date->format('d M Y') : '' }}</span>
                            @if($item->end_date)
                                &mdash; <span>{{ $item->end_date->format('d M Y') }}</span>
                            @endif
                            @if($item->location)
                                &bull; <span style="color: #475569;">📍 {{ $item->location }}</span>
                            @endif
                        </div>
                    </div>

                    <h2 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: .5rem;">
                        <a href="{{ route('public.timelines.show', $item) }}" style="color: inherit;">
                            {{ $item->title }}
                        </a>
                    </h2>

                    <p style="font-size: .9rem; color: #64748b; line-height: 1.6; margin-bottom: 1rem;">
                        {{ Str::limit(strip_tags($item->description), 200) }}
                    </p>

                    <div style="display: flex; justify-content: flex-end; pt-2;">
                        <a href="{{ route('public.timelines.show', $item) }}" style="font-size: .85rem; font-weight: 600; color: #4f46e5; display: inline-flex; align-items: center; gap: .25rem;">
                            <span>Detail Agenda</span> &rarr;
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="pagination">
            {{ $timelines->links() }}
        </div>
    @else
        <div class="empty">
            <p>Belum ada jadwal timeline publik yang tersedia.</p>
        </div>
    @endif
</main>
@endsection
