@php
    $featuredBook = $books->firstWhere('is_featured', true) ?? $books->first();
    $bookCover = fn ($book) => $book?->cover && file_exists(public_path($book->cover))
        ? asset($book->cover)
        : asset('images/virginia.jpg');
    $homeSchema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Person',
                'name' => 'Virginia',
                'jobTitle' => 'Autrice e storyteller',
                'url' => route('home'),
                'image' => asset('images/virginia.jpg'),
            ],
            [
                '@type' => 'WebSite',
                'name' => 'Virginia | Blog & Scrittura',
                'url' => route('home'),
            ],
        ],
    ];
@endphp

<x-layout
    title="Virginia | Blog & Scrittura"
    description="Romanzi, racconti, eventi e appunti di scrittura di Virginia. Uno spazio editoriale intimo per lettrici, lettori e collaborazioni."
    :schema="$homeSchema"
>

    <section class="writer-hero">
        <img src="{{ asset('images/virginia.jpg') }}" alt="Virginia scrittrice" class="writer-hero-bg-img">

        <div class="writer-hero-overlay"></div>

        <div class="writer-hero-content">
            <div class="writer-kicker-row">
                <p class="writer-eyebrow">Romanzi • Pensieri • Scrittura</p>
            </div>

            <h1>Storie che restano, parole che respirano.</h1>

            <p>
                Uno spazio personale dove racconti, romanzi e appunti di vita
                trovano una forma intima, precisa e vicina a chi legge.
            </p>

            <div class="writer-actions">
                <a href="#blog" class="btn-writer-primary">Leggi gli ultimi articoli</a>
                <a href="#books" class="btn-writer-secondary">Scopri i libri</a>
            </div>
        </div>

        <span class="writer-scroll-cue">Scorri</span>
    </section>

    <section id="featured" class="writer-section writer-featured">
        <div class="writer-section-header">
            <div>
                <p class="writer-eyebrow">In evidenza</p>
                <h2>Un frammento da leggere prima di entrare.</h2>
            </div>
            <p>
                Un assaggio del tono narrativo: poche righe per avvicinarsi
                al ritmo, all’atmosfera e allo sguardo dell’autrice.
            </p>
        </div>

        <div class="featured-excerpt">
            <blockquote>
                “Ci sono parole che arrivano piano, come una luce rimasta accesa
                in fondo a una stanza. Non chiedono attenzione: la meritano.”
            </blockquote>

            <div class="featured-excerpt-meta">
                <span>Estratto breve</span>
                <a href="#blog" class="writer-read-more">Leggi altri testi <span aria-hidden="true">→</span></a>
            </div>
        </div>
    </section>

    <section id="about" class="writer-section writer-about">
        <div class="writer-about-image">
            <img src="{{ asset('images/virginia.jpg') }}" alt="Virginia, scrittrice" loading="lazy" decoding="async">
        </div>

        <div class="writer-about-content">
            <p class="writer-eyebrow">Chi sono</p>

            <h2>Una voce narrativa intima, elegante e personale.</h2>

            <p>
                Virginia scrive storie che nascono dall’osservazione dei dettagli,
                dalle emozioni quotidiane e dal desiderio di trasformare pensieri
                e ricordi in parole capaci di restare.
            </p>

            <p>
                Questo spazio nasce come diario creativo: un luogo dove raccogliere
                racconti, riflessioni, progetti editoriali e frammenti di scrittura.
            </p>

            <div class="about-highlights" aria-label="Temi principali della scrittura">
                <span>Racconti intimi</span>
                <span>Romanzi contemporanei</span>
                <span>Appunti dal quotidiano</span>
            </div>

            <div class="about-signature">
                <span>Virginia</span>
                <small>Autrice & storyteller</small>
            </div>
        </div>
    </section>

    @if ($featuredBook)
        <section id="book-focus" class="writer-section book-focus">
            <div class="book-focus-cover">
                <img src="{{ $bookCover($featuredBook) }}" alt="Copertina di {{ $featuredBook->title }}" loading="lazy" decoding="async">
            </div>

            <div class="book-focus-content">
                <p class="writer-eyebrow">Libro in primo piano</p>
                <h2>{{ $featuredBook->title }}</h2>
                <p>{{ $featuredBook->description }}</p>

                <div class="book-focus-details">
                    <span>{{ $featuredBook->status === 'available' ? 'Disponibile ora' : 'In aggiornamento' }}</span>
                    <span>Romanzo contemporaneo</span>
                    <span>Per lettrici e lettori curiosi</span>
                </div>

                <div class="writer-actions">
                    @if ($featuredBook->amazon_url)
                        <a href="{{ $featuredBook->amazon_url }}" target="_blank" class="btn-writer-primary">
                            Vai al libro <span aria-hidden="true">↗</span>
                        </a>
                    @else
                        <a href="#newsletter" class="btn-writer-primary">Ricevi aggiornamenti</a>
                    @endif
                    <button type="button" class="btn-writer-secondary" data-modal-open="featured-excerpt-modal">
                        Leggi estratto
                    </button>
                    <a href="{{ route('books.show', $featuredBook) }}" class="btn-writer-secondary">Scheda libro</a>
                </div>
            </div>
        </section>

        <div class="writer-modal" id="featured-excerpt-modal" aria-hidden="true">
            <div class="writer-modal-panel" role="dialog" aria-modal="true" aria-labelledby="featured-excerpt-title">
                <button type="button" class="writer-modal-close" data-modal-close aria-label="Chiudi estratto">×</button>
                <p class="writer-eyebrow">Estratto</p>
                <h2 id="featured-excerpt-title">{{ $featuredBook->title }}</h2>
                <p>{{ $featuredBook->excerpt ?: 'Un estratto sarà disponibile prossimamente.' }}</p>
                <div class="writer-actions">
                    <a href="{{ route('books.show', $featuredBook) }}" class="btn-writer-primary">Apri scheda libro</a>
                </div>
            </div>
        </div>
    @endif

    <section id="books" class="writer-section">
        <div class="writer-section-header">
            <div>
                <p class="writer-eyebrow">Libri</p>
                <h2>Romanzi e progetti editoriali</h2>
            </div>
            <p>
                Dalle uscite disponibili ai testi ancora in lavorazione:
                una piccola mappa per seguire l’evoluzione delle storie.
            </p>
        </div>

        <div class="book-grid">
            @forelse ($books as $book)
                <article class="book-card"
                    style="background-image: url('{{ $bookCover($book) }}')">
                    <div class="book-overlay"></div>

                    <div class="book-content">
                        @if ($book->status === 'available')
                            <span class="book-badge available">Disponibile su Amazon</span>
                        @elseif ($book->status === 'coming')
                            <span class="book-badge coming">Prossima uscita</span>
                        @else
                            <span class="book-badge writing">In scrittura</span>
                        @endif

                        <h3>{{ $book->title }}</h3>

                        <p>{{ $book->description }}</p>

                        @if ($book->amazon_url)
                            <a href="{{ $book->amazon_url }}" target="_blank" class="btn-writer-primary">
                                Acquista su Amazon <span aria-hidden="true">↗</span>
                            </a>
                        @else
                            <a href="#contact" class="btn-writer-secondary">
                                Ricevi aggiornamenti
                            </a>
                        @endif
                        <a href="{{ route('books.show', $book) }}" class="writer-read-more">
                            Scheda libro <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </article>
            @empty
                <p>Nessun libro disponibile al momento.</p>
            @endforelse
        </div>
    </section>
    <section id="events" class="writer-section">
        <div class="writer-section-header">
            <div>
                <p class="writer-eyebrow">Calendario</p>
                <h2>Eventi e appuntamenti</h2>
            </div>
            <p>
                Presentazioni, firmacopie e momenti dal vivo per incontrare
                lettrici, lettori e nuove conversazioni.
            </p>
        </div>

        <div class="events-list">
            @forelse ($events as $event)
                <article class="event-card">
                    <div class="event-date">
                        @if ($event->event_date)
                            <strong>{{ $event->event_date->format('d') }}</strong>
                            <span>{{ strtoupper($event->event_date->translatedFormat('M')) }}</span>
                        @else
                            <strong>SOON</strong>
                            <span>2026</span>
                        @endif
                    </div>

                    <div class="event-content">
                        <span>{{ $event->category ?? 'Evento' }}</span>
                        <h3>{{ $event->title }}</h3>

                        <p>
                            {{ $event->description }}
                            @if ($event->location)
                                <br><strong>Luogo:</strong> {{ $event->location }}
                            @endif
                        </p>
                    </div>

                    @if ($event->link)
                        <a href="{{ $event->link }}" target="_blank" class="event-link">Info</a>
                    @else
                        <a href="#contact" class="event-link">Chiedi info</a>
                    @endif
                </article>
            @empty
                <article class="event-card event-disabled">
                    <div class="event-date">
                        <strong>SOON</strong>
                        <span>2026</span>
                    </div>

                    <div class="event-content">
                        <span>Prossimamente</span>
                        <h3>Nuove date in arrivo</h3>
                        <p>Il calendario verrà aggiornato con nuovi eventi, presentazioni e incontri.</p>
                    </div>

                    <a href="#contact" class="event-link">Stay tuned</a>
                </article>
            @endforelse
        </div>
    </section>

    <section id="blog" class="writer-section">
        <div class="writer-section-header">
            <div>
                <p class="writer-eyebrow">Blog</p>
                <h2>Ultimi pensieri</h2>
            </div>
            <p>
                Note di scrittura, dietro le quinte dei personaggi e frammenti
                da leggere con il passo lento di una pagina scelta bene.
            </p>
        </div>

        @if ($categories->isNotEmpty())
            <div class="blog-filters" aria-label="Filtra articoli per categoria">
                <a href="{{ route('home') }}#blog" class="{{ $activeCategory ? '' : 'is-active' }}">Tutti</a>
                @foreach ($categories as $category)
                    <a
                        href="{{ route('home', ['category' => $category]) }}#blog"
                        class="{{ $activeCategory === $category ? 'is-active' : '' }}"
                    >
                        {{ $category }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="writer-grid">
            @forelse ($posts as $post)
                <article class="writer-card">
                    <span>{{ $post->category ?? 'Blog' }}</span>

                    <h3>{{ $post->title }}</h3>

                    <p>{{ $post->excerpt }}</p>

                    <a href="{{ route('posts.show', $post->slug) }}" class="writer-read-more">
                        Leggi articolo <span aria-hidden="true">→</span>
                    </a>
                </article>
            @empty
                <article class="writer-card">
                    <span>Blog</span>
                    <h3>Articoli in arrivo</h3>
                    <p>Presto saranno pubblicati nuovi pensieri, racconti e riflessioni.</p>
                </article>
            @endforelse
        </div>
    </section>

    <section id="newsletter" class="writer-section newsletter-section">
        <div class="newsletter-content">
            <p class="writer-eyebrow">Newsletter</p>
            <h2>Ricevi racconti, nuove uscite e date dal vivo.</h2>
            <p>
                Una lettera leggera, senza rumore: aggiornamenti sui libri,
                note di scrittura e qualche estratto in anteprima.
            </p>
        </div>

        <form class="newsletter-form" action="{{ route('newsletter.store') }}" method="post">
            @csrf
            <label for="newsletter-email">Email</label>
            <div>
                <input id="newsletter-email" type="email" name="email" placeholder="la-tua-email@example.com" value="{{ old('email') }}" required>
                <button type="submit" class="btn-writer-primary">Iscriviti</button>
            </div>
            @if (session('newsletter_status'))
                <strong class="form-status">{{ session('newsletter_status') }}</strong>
            @endif
            @error('email')
                <strong class="form-status form-status-error">{{ $message }}</strong>
            @enderror
            <small>Niente spam. Solo parole quando hanno qualcosa da dire.</small>
        </form>
    </section>

    <section class="writer-section testimonials-section">
        <div class="writer-section-header">
            <div>
                <p class="writer-eyebrow">Recensioni</p>
                <h2>Parole da chi ha già letto.</h2>
            </div>
            <p>
                Citazioni brevi, utili a restituire il tono dei testi e la loro
                accoglienza da parte di lettrici, lettori e prime letture.
            </p>
        </div>

        <div class="testimonials-grid">
            @forelse ($testimonials as $testimonial)
                <article>
                    <p>“{{ $testimonial->quote }}”</p>
                    <span>{{ $testimonial->author ?? $testimonial->context ?? 'Lettura' }}</span>
                </article>
            @empty
                <article>
                    <p>“Una scrittura intima, capace di trasformare i dettagli in memoria.”</p>
                    <span>Lettura in anteprima</span>
                </article>
            @endforelse
        </div>
    </section>

    <section id="press" class="writer-section press-section">
        <div class="writer-section-header">
            <div>
                <p class="writer-eyebrow">Press kit</p>
                <h2>Materiali essenziali per stampa e collaborazioni.</h2>
            </div>
            <p>
                Bio breve, contatti e informazioni utili per librerie, festival,
                redazioni e progetti editoriali.
            </p>
        </div>

        <div class="press-grid">
            <article class="press-card press-card-main">
                <span>Bio breve</span>
                <p>
                    Virginia è un’autrice e storyteller. Scrive romanzi, racconti
                    e appunti narrativi attraversati da dettagli quotidiani,
                    memoria emotiva e una voce intima.
                </p>
                <button
                    type="button"
                    class="post-share"
                    data-copy-text="Virginia è un’autrice e storyteller. Scrive romanzi, racconti e appunti narrativi attraversati da dettagli quotidiani, memoria emotiva e una voce intima."
                >
                    Copia bio
                </button>
            </article>

            <article class="press-card">
                <span>Per eventi</span>
                <h3>Presentazioni e incontri</h3>
                <p>Disponibile per dialoghi con lettori, firmacopie e appuntamenti in libreria.</p>
            </article>

            <article class="press-card">
                <span>Per redazioni</span>
                <h3>Interviste e materiali</h3>
                <p>Contatti, immagini e dettagli editoriali possono essere richiesti via mail.</p>
            </article>
        </div>
    </section>

    <section id="contact" class="writer-section writer-contact">
        <p class="writer-eyebrow">Contatti</p>
        <h2>Hai una collaborazione o vuoi leggere qualcosa?</h2>

        <p>
            Questo spazio è aperto a lettori, collaborazioni editoriali
            e progetti creativi legati alla scrittura.
        </p>

        <div class="writer-actions">
            <a href="mailto:email@example.com?subject=Contatto%20dal%20sito%20di%20Virginia" class="btn-writer-primary">
                Scrivimi una mail
            </a>
            <a href="#blog" class="btn-writer-secondary">Leggi prima il blog</a>
        </div>
    </section>

</x-layout>
