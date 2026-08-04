@extends('layouts.public')

@section('title', 'Informasi & Artikel')
@section('meta_description', 'Kumpulan berita, pengumuman, dan artikel informasi terbaru dari Terra Tech.')

@section('content')
<main class="container">
    <div class="page-hero">
        <h1>Informasi & Artikel</h1>
        <p>Temukan kabar terbaru, wawasan teknologi, dan pembaruan dari Terra Tech.</p>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('public.information.index') }}">
            <select name="category" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            @if(request()->filled('category'))
                <a href="{{ route('public.information.index') }}" class="button btn-reset font-medium text-xs rounded-xl px-4 py-2 flex items-center">Reset Filter</a>
            @endif
        </form>
    </div>

    {{-- Posts Grid --}}
    @if($posts->count() > 0)
        <div class="posts-grid">
            @foreach($posts as $post)
                <article class="post-card">
                    @if($post->featured_image_url)
                        <a href="{{ route('public.information.show', $post->slug) }}">
                            <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt ?: $post->title }}" class="post-card-image" />
                        </a>
                    @else
                        <div class="post-card-image flex items-center justify-center text-gray-400 bg-slate-100">
                            <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                    <div class="post-card-body">
                        <div class="post-card-category">{{ $post->category?->name ?? 'Informasi' }}</div>
                        <h2 class="post-card-title">
                            <a href="{{ route('public.information.show', $post->slug) }}" style="color: inherit;">
                                {{ $post->title }}
                            </a>
                        </h2>
                        @if($post->excerpt)
                            <p class="post-card-excerpt">{{ $post->excerpt }}</p>
                        @endif
                        <div class="post-card-footer">
                            <span class="post-card-date">{{ $post->published_at ? $post->published_at->format('d M Y') : '' }}</span>
                            <a href="{{ route('public.information.show', $post->slug) }}" class="post-card-link">Baca Selengkapnya &rarr;</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if($posts->hasPages())
            <div class="pagination">
                {{ $posts->links() }}
            </div>
        @endif
    @else
        <div class="empty">
            <p>Belum ada artikel informasi yang dipublikasikan pada kategori ini.</p>
        </div>
    @endif
</main>
@endsection
