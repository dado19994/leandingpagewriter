<div class="admin-shell">
    <aside class="admin-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="admin-brand">
            Virginia<span>.</span>
            <small>Admin</small>
        </a>

        <nav class="admin-sidebar-nav">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.books.index') }}">Libri</a>
            <a href="{{ route('admin.posts.index') }}">Articoli</a>
            <a href="{{ route('admin.testimonials.index') }}">Testimonianze</a>
            <a href="{{ route('admin.newsletter.index') }}">Newsletter</a>
            <a href="{{ route('home') }}">Vedi sito</a>
        </nav>

        <form action="{{ route('admin.logout') }}" method="post" class="admin-sidebar-logout">
            @csrf
            <button type="submit">Esci dal pannello</button>
        </form>
    </aside>

    <div class="admin-main">
    <header class="admin-header">
        <div>
            <p class="writer-eyebrow">Admin</p>
            <h1>{{ $title ?? 'Pannello' }}</h1>
        </div>

        <nav class="admin-nav">
            <a href="{{ route('home') }}">Vedi sito</a>
            <form action="{{ route('admin.logout') }}" method="post">
                @csrf
                <button type="submit">Esci</button>
            </form>
        </nav>
    </header>

    @if (session('admin_status'))
        <p class="admin-status" role="status" data-flash>{{ session('admin_status') }}</p>
    @endif

    @if ($errors->any())
        <div class="admin-errors">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
