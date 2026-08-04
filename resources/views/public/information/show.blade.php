@extends('layouts.public')

@section('title', $post->meta_title ?: $post->title)
@section('meta_description', $post->meta_description ?: ($post->excerpt ?: Str::limit(strip_tags($post->content), 160)))

@section('content')
<main class="container" style="max-w: 800px;">
    <article style="background: #fff; border: 1px solid #e2e8f0; border-radius: 1.25rem; padding: 2.5rem; margin-top: 1rem;">
        <div style="font-size: .8rem; font-weight: 700; color: #6366f1; text-transform: uppercase; letter-spacing: .05em; margin-bottom: .5rem;">
            {{ $post->category?->name ?? 'Informasi' }}
        </div>
        <h1 style="font-size: 2.25rem; font-weight: 800; color: #0f172a; line-height: 1.3; margin-bottom: 1rem;">
            {{ $post->title }}
        </h1>
        <div style="font-size: .85rem; color: #94a3b8; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: center;">
            <span>{{ $post->published_at ? $post->published_at->format('d M Y') : '' }}</span>
            <span>&bull;</span>
            <span>{{ number_format($post->views_count) }} kali dilihat</span>
        </div>

        @if($post->featured_image_url)
            <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt ?: $post->title }}" style="width: 100%; max-height: 400px; object-fit: cover; border-radius: 1rem; margin-bottom: 2rem; border: 1px solid #e2e8f0;" />
        @endif

        @if($post->excerpt)
            <div style="font-size: 1.1rem; color: #475569; font-style: italic; border-left: 4px solid #6366f1; padding-left: 1rem; margin-bottom: 2rem; line-height: 1.6;">
                {{ $post->excerpt }}
            </div>
        @endif

        <div style="font-size: 1.05rem; color: #334155; line-height: 1.8; white-space: pre-wrap;">{{ $post->content }}</div>
    </article>

    @if($related->count() > 0)
        <section style="margin-top: 3rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin-bottom: 1.5rem;">Artikel Terkait</h2>
            <div class="posts-grid">
                @foreach($related as $item)
                    <article class="post-card">
                        @if($item->featured_image_url)
                            <a href="{{ route('public.information.show', $item->slug) }}">
                                <img src="{{ $item->featured_image_url }}" alt="{{ $item->featured_image_alt ?: $item->title }}" class="post-card-image" />
                            </a>
                        @endif
                        <div class="post-card-body">
                            <div class="post-card-category">{{ $item->category?->name }}</div>
                            <h3 class="post-card-title">
                                <a href="{{ route('public.information.show', $item->slug) }}" style="color: inherit;">{{ $item->title }}</a>
                            </h3>
                            <div class="post-card-footer">
                                <span class="post-card-date">{{ $item->published_at ? $item->published_at->format('d M Y') : '' }}</span>
                                <a href="{{ route('public.information.show', $item->slug) }}" class="post-card-link">Baca &rarr;</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</main>
@endsection
