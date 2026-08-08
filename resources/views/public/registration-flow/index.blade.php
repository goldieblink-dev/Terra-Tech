@extends('layouts.public')

@section('title', 'Alur Pendaftaran Resmi')
@section('meta_description', 'Petunjuk langkah demi langkah, syarat dokumen, dan alur pendaftaran resmi Terra Tech Indonesia.')

@section('content')
<main class="container">
    <div class="page-hero">
        <h1>Alur & Tata Cara Pendaftaran</h1>
        <p>Ikuti langkah-langkah berikut untuk menyelesaikan proses pendaftaran di Terra Tech.</p>
    </div>

    @if($steps->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 2rem; max-width: 850px; margin: 0 auto;">
            @foreach($steps as $item)
                <article style="background: #fff; border: 1px solid #e2e8f0; border-radius: 1.25rem; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: transform .2s;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
                        <span style="font-size: .8rem; font-weight: 800; background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: .3rem .8rem; border-radius: 9999px; text-transform: uppercase;">
                            Langkah {{ $item->sort_order }}
                        </span>
                        @if($item->icon)
                            <span style="font-size: .8rem; color: #64748b; font-weight: 600;">📌 {{ $item->icon }}</span>
                        @endif
                    </div>

                    @if($item->illustration_image_url)
                        <div style="margin-bottom: 1.25rem; text-align: center; background: #f8fafc; border-radius: 1rem; padding: 1rem;">
                            <img src="{{ $item->illustration_image_url }}" alt="{{ $item->title }}" style="max-height: 220px; max-width: 100%; object-fit: contain; border-radius: .75rem;" />
                        </div>
                    @endif

                    <h2 style="font-size: 1.35rem; font-weight: 800; color: #0f172a; margin-bottom: .75rem; line-height: 1.3;">
                        <a href="{{ route('public.registration_flow.show', $item->slug) }}" style="color: inherit;">
                            {{ $item->title }}
                        </a>
                    </h2>

                    <div style="font-size: .95rem; color: #475569; line-height: 1.7; margin-bottom: 1.25rem;">
                        {!! nl2br(e($item->description)) !!}
                    </div>

                    {{-- Requirements Bullet List --}}
                    @if(is_array($item->requirements) && count($item->requirements) > 0)
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.25rem; margin-bottom: 1rem;">
                            <div style="font-size: .75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .05em; margin-bottom: .75rem;">
                                Persyaratan & Berkas Kelengkapan:
                            </div>
                            <ul style="list-style-type: none; padding-left: 0; margin: 0; display: flex; flex-direction: column; gap: .5rem;">
                                @foreach($item->requirements as $req)
                                    <li style="font-size: .9rem; color: #1e293b; font-weight: 500; display: flex; align-items: flex-start; gap: .5rem;">
                                        <span style="color: #10b981; font-weight: 800;">✓</span>
                                        <span>{{ $req }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div style="display: flex; justify-content: flex-end; margin-top: 1rem; padding-top: .75rem; border-top: 1px solid #f1f5f9;">
                        <a href="{{ route('public.registration_flow.show', $item->slug) }}" style="font-size: .85rem; font-weight: 600; color: #4f46e5; text-decoration: none; display: inline-flex; align-items: center; gap: .25rem;">
                            <span>Detail Instruksi</span> &rarr;
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="pagination">
            {{ $steps->links() }}
        </div>
    @else
        <div class="empty">
            <p>Belum ada informasi alur pendaftaran yang publikasikan.</p>
        </div>
    @endif
</main>
@endsection
