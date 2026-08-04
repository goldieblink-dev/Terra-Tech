<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Informasi') — Terra Tech</title>
    <meta name="description" content="@yield('meta_description', 'Portal Informasi Terra Tech')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #1e293b; }
        a { text-decoration: none; }

        /* Navbar */
        .navbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 2rem; display: flex; align-items: center; justify-content: space-between; height: 64px; position: sticky; top: 0; z-index: 50; }
        .navbar-brand { font-size: 1.25rem; font-weight: 800; color: #4f46e5; }
        .navbar-links { display: flex; align-items: center; gap: 1.5rem; }
        .navbar-links a { font-size: 0.875rem; font-weight: 500; color: #475569; transition: color .2s; }
        .navbar-links a:hover, .navbar-links a.active { color: #4f46e5; }

        /* Container */
        .container { max-width: 1100px; margin: 0 auto; padding: 2.5rem 1.5rem; }

        /* Hero */
        .page-hero { text-align: center; padding: 3rem 0 2rem; }
        .page-hero h1 { font-size: 2.25rem; font-weight: 800; color: #0f172a; margin-bottom: .75rem; }
        .page-hero p { color: #64748b; font-size: 1.05rem; }

        /* Filter bar */
        .filter-bar { display: flex; flex-wrap: wrap; align-items: center; gap: 1rem; margin-bottom: 2rem; }
        .filter-bar form { display: flex; flex-wrap: wrap; gap: .75rem; width: 100%; }
        .filter-bar input, .filter-bar select { padding: .6rem 1rem; border: 1px solid #e2e8f0; border-radius: .75rem; font-size: .875rem; background: #fff; color: #1e293b; outline: none; }
        .filter-bar input:focus, .filter-bar select:focus { border-color: #6366f1; box-shadow: 0 0 0 2px rgba(99,102,241,.2); }
        .filter-bar button { padding: .6rem 1.25rem; background: #4f46e5; color: #fff; border: none; border-radius: .75rem; font-size: .875rem; font-weight: 600; cursor: pointer; }
        .filter-bar button:hover { background: #4338ca; }
        .filter-bar .btn-reset { background: #f1f5f9; color: #475569; }
        .filter-bar .btn-reset:hover { background: #e2e8f0; }

        /* Grid */
        .posts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }
        .post-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 1.25rem; overflow: hidden; transition: transform .2s, box-shadow .2s; }
        .post-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,.08); }
        .post-card-image { width: 100%; height: 180px; object-fit: cover; background: #e2e8f0; display: block; }
        .post-card-body { padding: 1.25rem; }
        .post-card-category { font-size: .7rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #6366f1; margin-bottom: .4rem; }
        .post-card-title { font-size: 1rem; font-weight: 700; color: #0f172a; line-height: 1.5; margin-bottom: .5rem; }
        .post-card-excerpt { font-size: .85rem; color: #64748b; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .post-card-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 1rem; padding-top: .75rem; border-top: 1px solid #f1f5f9; }
        .post-card-date { font-size: .75rem; color: #94a3b8; }
        .post-card-link { font-size: .8rem; font-weight: 600; color: #4f46e5; }
        .post-card-link:hover { color: #4338ca; }

        /* Empty */
        .empty { text-align: center; padding: 4rem 0; color: #94a3b8; font-size: .95rem; }

        /* Pagination */
        .pagination { margin-top: 2.5rem; display: flex; justify-content: center; }
        .pagination .page-item { display: inline-block; margin: 0 .2rem; }
        .pagination .page-item a, .pagination .page-item span { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border: 1px solid #e2e8f0; border-radius: .6rem; font-size: .85rem; color: #475569; transition: all .2s; }
        .pagination .page-item.active span { background: #4f46e5; color: #fff; border-color: #4f46e5; }
        .pagination .page-item a:hover { background: #f1f5f9; }

        /* Footer */
        .site-footer { text-align: center; padding: 2.5rem 1rem; color: #94a3b8; font-size: .8rem; border-top: 1px solid #e2e8f0; margin-top: 4rem; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="/" class="navbar-brand">Terra Tech</a>
        <div class="navbar-links">
            <a href="{{ route('public.information.index') }}" class="{{ request()->routeIs('public.information*') ? 'active' : '' }}">Informasi</a>
            <a href="{{ route('public.announcements.index') }}" class="{{ request()->routeIs('public.announcements*') ? 'active' : '' }}">Pengumuman</a>
            <a href="{{ route('public.timelines.index') }}" class="{{ request()->routeIs('public.timelines*') ? 'active' : '' }}">Timeline</a>
            @auth
                <a href="{{ url('/dashboard') }}" style="color: #4f46e5; font-weight: 600;">CMS</a>
            @else
                <a href="{{ route('login') }}" style="color: #4f46e5; font-weight: 600;">Login</a>
            @endauth
        </div>
    </nav>

    @yield('content')

    <footer class="site-footer">
        <p>&copy; {{ date('Y') }} Terra Tech. All rights reserved.</p>
    </footer>
</body>
</html>
