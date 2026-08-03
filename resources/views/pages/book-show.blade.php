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
        'url' => app()->getLocale() === 'en' ? route('books.show.en', $book) : route('books.show', $book),
    ];
    $homeRoute = app()->getLocale() === 'en' ? route('home.en') : route('home');
    $canonicalRoute = app()->getLocale() === 'en' ? route('books.show.en', $book) : route('books.show', $book);
    $alternateRoute = app()->getLocale() === 'en' ? route('books.show', $book) : route('books.show.en', $book);
@endphp

<x-layout
    title="{{ $book->meta_title ?: $book->title.' | Virginia' }}"
    description="{{ $book->meta_description ?: $book->description }}"
    :image="$cover"
    type="book"
    :schema="$bookSchema"
    :canonical="$canonicalRoute"
    :alternate="$alternateRoute"
>
    <section class="book-page-hero">
        <div class="book-page-cover">
            <img src="{{ $cover }}" alt="{{ __('site.content.image_book', ['title' => $book->title]) }}" loading="lazy" decoding="async">
        </div>

        <div class="book-page-intro">
            <a href="{{ $homeRoute }}#books" class="post-back">
                <span aria-hidden="true">←</span> {{ __('site.book_page.back') }}
            </a>
            @if (app()->getLocale() === 'en')
                <p role="note">{{ __('site.content.fallback_notice') }}</p>
            @endif
            <p class="writer-eyebrow">{{ $book->genre ?? __('site.book_page.type') }}</p>
            <h1>{{ $book->title }}</h1>
            <p>{{ $book->description }}</p>

            <div class="book-focus-details">
                <span>{{ $book->status === 'available' ? __('site.book_page.available') : __('site.book_page.updating') }}</span>
                <span>{{ $book->genre ?? __('site.book_page.genre_fallback') }}</span>
            </div>

            <div class="writer-actions">
                @if ($book->amazon_url)
                    <a href="{{ $book->amazon_url }}" target="_blank" class="btn-writer-primary">
                        {{ __('site.book_page.buy') }} <span aria-hidden="true">↗</span>
                    </a>
                @else
                    <a href="{{ $homeRoute }}#newsletter" class="btn-writer-primary">{{ __('site.book_page.updates') }}</a>
                @endif
                <button type="button" class="btn-writer-secondary" data-modal-open="book-excerpt-modal">
                    {{ __('site.book_page.excerpt') }}
                </button>
            </div>
        </div>
    </section>

    <section class="writer-section book-page-section">
        <div>
            <p class="writer-eyebrow">{{ __('site.book_page.synopsis') }}</p>
            <h2>{{ __('site.book_page.inside') }}</h2>
        </div>
        <p>{{ $book->synopsis ?: $book->description }}</p>
    </section>

    @if ($book->reviews)
        <section class="writer-section testimonials-section">
            <div class="writer-section-header">
                <div>
                    <p class="writer-eyebrow">{{ __('site.book_page.reviews') }}</p>
                    <h2>{{ __('site.book_page.reviews_title') }}</h2>
                </div>
            </div>

            <div class="testimonials-grid">
                @foreach ($book->reviews as $review)
                    <article>
                        <p>“{{ $review['quote'] ?? '' }}”</p>
                        <span>{{ $review['author'] ?? __('site.content.reader') }}</span>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <div class="writer-modal" id="book-excerpt-modal" aria-hidden="true">
        <div class="writer-modal-panel" role="dialog" aria-modal="true" aria-labelledby="book-excerpt-title">
            <button type="button" class="writer-modal-close" data-modal-close aria-label="{{ __('site.book_page.close_excerpt') }}">×</button>
            <p class="writer-eyebrow">{{ __('site.book_page.excerpt_label') }}</p>
            <h2 id="book-excerpt-title">{{ $book->title }}</h2>
            <p>{{ $book->excerpt ?: __('site.book_page.empty_excerpt') }}</p>
        </div>
    </div>
</x-layout>
