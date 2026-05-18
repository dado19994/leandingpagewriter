@php
    $cover = $book->cover && file_exists(public_path($book->cover))
        ? asset($book->cover)
        : asset('images/virginia.jpg');
    $bookSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Book',
        'name' => $book->title,
        'author' => [
            '@type' => 'Person',
            'name' => 'Virginia',
        ],
        'genre' => $book->genre,
        'description' => $book->description,
        'image' => $cover,
        'url' => route('books.show', $book),
    ];
@endphp

<x-layout
    title="{{ $book->meta_title ?: $book->title.' | Virginia' }}"
    description="{{ $book->meta_description ?: $book->description }}"
    :image="$cover"
    type="book"
    :schema="$bookSchema"
>
    <section class="book-page-hero">
        <div class="book-page-cover">
            <img src="{{ $cover }}" alt="Copertina di {{ $book->title }}" loading="lazy" decoding="async">
        </div>

        <div class="book-page-intro">
            <a href="{{ route('home') }}#books" class="post-back">
                <span aria-hidden="true">←</span> Torna ai libri
            </a>
            <p class="writer-eyebrow">{{ $book->genre ?? 'Libro' }}</p>
            <h1>{{ $book->title }}</h1>
            <p>{{ $book->description }}</p>

            <div class="book-focus-details">
                <span>{{ $book->status === 'available' ? 'Disponibile ora' : 'In aggiornamento' }}</span>
                <span>{{ $book->genre ?? 'Narrativa' }}</span>
            </div>

            <div class="writer-actions">
                @if ($book->amazon_url)
                    <a href="{{ $book->amazon_url }}" target="_blank" class="btn-writer-primary">
                        Acquista su Amazon <span aria-hidden="true">↗</span>
                    </a>
                @else
                    <a href="{{ route('home') }}#newsletter" class="btn-writer-primary">Ricevi aggiornamenti</a>
                @endif
                <button type="button" class="btn-writer-secondary" data-modal-open="book-excerpt-modal">
                    Leggi estratto
                </button>
            </div>
        </div>
    </section>

    <section class="writer-section book-page-section">
        <div>
            <p class="writer-eyebrow">Sinossi</p>
            <h2>Dentro la storia</h2>
        </div>
        <p>{{ $book->synopsis ?: $book->description }}</p>
    </section>

    @if ($book->reviews)
        <section class="writer-section testimonials-section">
            <div class="writer-section-header">
                <div>
                    <p class="writer-eyebrow">Recensioni</p>
                    <h2>Cosa si dice del libro</h2>
                </div>
            </div>

            <div class="testimonials-grid">
                @foreach ($book->reviews as $review)
                    <article>
                        <p>“{{ $review['quote'] ?? '' }}”</p>
                        <span>{{ $review['author'] ?? 'Lettura' }}</span>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <div class="writer-modal" id="book-excerpt-modal" aria-hidden="true">
        <div class="writer-modal-panel" role="dialog" aria-modal="true" aria-labelledby="book-excerpt-title">
            <button type="button" class="writer-modal-close" data-modal-close aria-label="Chiudi estratto">×</button>
            <p class="writer-eyebrow">Estratto</p>
            <h2 id="book-excerpt-title">{{ $book->title }}</h2>
            <p>{{ $book->excerpt ?: 'Un estratto sarà disponibile prossimamente.' }}</p>
        </div>
    </div>
</x-layout>
