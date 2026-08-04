@extends('layouts.public')

@section('title', $timeline->title . ' — Timeline')
@section('meta_description', Str::limit(strip_tags($timeline->description), 160))

@section('content')
<main class="container" style="max-width: 800px;">
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('public.timelines.index') }}" style="font-size: .875rem; color: #64748b; font-weight: 500; display: inline-flex; align-items: center; gap: .4rem;">
            &larr; Kembali ke Timeline
        </a>
    </div>

    <article style="background: #fff; border: 1px solid #e2e8f0; border-top: 6px solid {{ $timeline->color ?? '#2563eb' }}; border-radius: 1.25rem; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
            <div>
                @if($timeline->timeline_status === 'ongoing')
                    <span style="font-size: .75rem; font-weight: 700; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: .25rem .65rem; border-radius: 9999px; text-transform: uppercase;">Sedang Berjalan</span>
                @elseif($timeline->timeline_status === 'upcoming')
                    <span style="font-size: .75rem; font-weight: 700; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: .25rem .65rem; border-radius: 9999px; text-transform: uppercase;">Akan Datang</span>
                @else
                    <span style="font-size: .75rem; font-weight: 700; background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: .25rem .65rem; border-radius: 9999px; text-transform: uppercase;">Selesai</span>
                @endif
            </div>

            <div style="font-size: .85rem; color: #64748b; font-weight: 500; text-align: right;">
                <div>🗓 {{ $timeline->start_date ? $timeline->start_date->format('d M Y') : '' }} @if($timeline->end_date) &mdash; {{ $timeline->end_date->format('d M Y') }} @endif</div>
                @if($timeline->location)
                    <div style="margin-top: .25rem; color: #475569;">📍 {{ $timeline->location }}</div>
                @endif
            </div>
        </div>

        <h1 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-bottom: 1.5rem; line-height: 1.3;">
            {{ $timeline->title }}
        </h1>

        <div style="font-size: 1rem; color: #334155; line-height: 1.8; margin-top: 1rem;">
            {!! nl2br(e($timeline->description)) !!}
        </div>
    </article>
</main>
@endsection
