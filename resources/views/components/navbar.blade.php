<nav class="writer-navbar">
    <a href="{{ route('home') }}" class="writer-logo">
        Virginia<span>.</span>
    </a>

    <button
        type="button"
        class="writer-menu-toggle"
        aria-label="Apri menu"
        aria-expanded="false"
        aria-controls="writer-nav-links"
    >
        <span></span>
        <span></span>
        <span></span>
    </button>

    <div class="writer-nav-links" id="writer-nav-links">
        <a href="{{ route('home') }}#about">Chi sono</a>
        <a href="{{ route('home') }}#book-focus">Libro</a>
        <a href="{{ route('home') }}#books">Libri</a>
        <a href="{{ route('home') }}#events">Eventi</a>
        <a href="{{ route('home') }}#blog">Blog</a>
        <a href="{{ route('home') }}#newsletter">Newsletter</a>
        <a href="{{ route('home') }}#contact">Contatti</a>
    </div>
</nav>
