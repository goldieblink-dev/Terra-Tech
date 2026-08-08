@extends('layouts.public')

@section('title', $step->title . ' — Alur Pendaftaran')
@section('meta_description', Str::limit(strip_tags($step->description), 160))

@section('content')
<main class="container" style="max-width: 800px;">
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('public.registration_flow.index') }}" style="font-size: .875rem; color: #64748b; font-weight: 500; display: inline-flex; align-items: center; gap: .4rem; text-decoration: none;">
            &larr; Kembali ke Alur Pendaftaran
        </a>
    </div>

    <article style="background: #fff; border: 1px solid #e2e8f0; border-top: 6px solid #4f46e5; border-radius: 1.25rem; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
            <span style="font-size: .8rem; font-weight: 800; background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: .3rem .8rem; border-radius: 9999px; text-transform: uppercase;">
                Langkah {{ $step->sort_order }}
            </span>
            @if($step->icon)
                <span style="font-size: .85rem; color: #64748b; font-weight: 600;">📌 {{ $step->icon }}</span>
            @endif
        </div>

        @if($step->illustration_image_url)
            <div style="margin-bottom: 1.5rem; text-align: center; background: #f8fafc; border-radius: 1rem; padding: 1.5rem; border: 1px solid #f1f5f9;">
                <img src="{{ $step->illustration_image_url }}" alt="{{ $step->title }}" style="max-height: 320px; max-width: 100%; object-fit: contain; border-radius: .75rem;" />
            </div>
        @endif

        <h1 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-bottom: 1.5rem; line-height: 1.3;">
            {{ $step->title }}
        </h1>

        <div style="font-size: 1rem; color: #334155; line-height: 1.8; margin-bottom: 2rem;">
            {!! nl2br(e($step->description)) !!}
        </div>

        {{-- Requirements Bullet List --}}
        @if(is_array($step->requirements) && count($step->requirements) > 0)
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 1.25rem; padding: 1.5rem; margin-top: 1.5rem;">
                <h3 style="font-size: .9rem; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 1rem;">
                    Persyaratan & Dokumen yang Wajib Disiapkan:
                </h3>
                <ul style="list-style-type: none; padding-left: 0; margin: 0; display: flex; flex-direction: column; gap: .75rem;">
                    @foreach($step->requirements as $req)
                        <li style="font-size: .95rem; color: #1e293b; font-weight: 500; display: flex; align-items: flex-start; gap: .75rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 50%; font-size: .75rem; font-weight: 800; shrink-0;">✓</span>
                            <span>{{ $req }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </article>
</main>
@endsection
