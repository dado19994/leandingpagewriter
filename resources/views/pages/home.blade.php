@php
    $featuredBook = $books->firstWhere('is_featured', true) ?? $books->first();
    $bookCover = fn($book) => $book?->cover && file_exists(public_path($book->cover))
        ? asset($book->cover)
        : asset('images/virginia.jpg');
    $homeRoute = app()->getLocale() === 'en' ? route('home.en') : route('home');
    $alternateRoute = app()->getLocale() === 'en' ? route('home') : route('home.en');
    $bookRouteName = app()->getLocale() === 'en' ? 'books.show.en' : 'books.show';
    $postRouteName = app()->getLocale() === 'en' ? 'posts.show.en' : 'posts.show';
    $newsletterRouteName = app()->getLocale() === 'en' ? 'newsletter.store.en' : 'newsletter.store';
    $homeSchema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Person',
                'name' => 'Virginia',
                'jobTitle' => app()->getLocale() === 'en' ? 'Author and storyteller' : 'Autrice e storyteller',
                'url' => $homeRoute,
                'image' => asset('images/virginia.jpg'),
            ],
            [
                '@type' => 'WebSite',
                'name' => __('site.meta.title'),
                'url' => $homeRoute,
            ],
        ],
    ];
@endphp

<x-layout title="{{ __('site.meta.title') }}"
    description="{{ __('site.meta.description') }}"
    :canonical="$homeRoute"
    :alternate="$alternateRoute"
    :schema="$homeSchema">

    <section class="writer-hero">
        <img src="{{ asset('images/virginia.jpg') }}" alt="{{ __('site.content.image_author') }}" class="writer-hero-bg-img">

        <div class="writer-hero-overlay"></div>

        <div class="writer-hero-content">
            <div class="writer-kicker-row">
                <p class="writer-eyebrow">{{ __('site.hero.eyebrow') }}</p>
            </div>

            <h1>{!! nl2br(e(__('site.hero.title'))) !!}</h1>

            <p>
                {{ __('site.hero.body') }}
            </p>

            <div class="writer-actions">
                <a href="#blog" class="btn-writer-primary">{{ __('site.hero.blog') }}</a>
                <a href="#books" class="btn-writer-secondary">{{ __('site.hero.books') }}</a>
            </div>
        </div>

        <span class="writer-scroll-cue">{{ __('site.hero.scroll') }}</span>
    </section>

    @if (app()->getLocale() === 'en')
        <aside class="writer-section" role="note">{{ __('site.content.fallback_notice') }}</aside>
    @endif

    <section id="featured" class="writer-section writer-featured">
        <div class="writer-section-header">
            <div>
                <p class="writer-eyebrow">{{ __('site.featured.eyebrow') }}</p>
                <h2>{{ __('site.featured.title') }}</h2>
            </div>
            <p>
                {{ __('site.featured.body') }}
            </p>
        </div>

        <div class="featured-excerpt">
            <blockquote>
                {{ __('site.featured.quote') }}
            </blockquote>

            <div class="featured-excerpt-meta">
                <span>{{ __('site.featured.label') }}</span>
                <a href="#blog" class="writer-read-more">{{ __('site.featured.link') }} <span aria-hidden="true">→</span></a>
            </div>
        </div>
    </section>

    <section id="about" class="writer-section writer-about">
        <div class="writer-about-image">
            <img src="{{ asset('images/virgi.jpg') }}" alt="{{ __('site.content.image_author') }}" loading="lazy" decoding="async">
        </div>

        <div class="writer-about-content">
            <p class="writer-eyebrow">{{ __('site.about.eyebrow') }}</p>

            <h2>{{ __('site.about.title') }}</h2>

            <p>
                {{ __('site.about.p1') }}
            </p>

            <p>
                {{ __('site.about.p2') }}
            </p>

            <div class="about-highlights" aria-label="Temi principali della scrittura">
                @foreach (__('site.about.tags') as $tag)
                    <span>{{ $tag }}</span>
                @endforeach
            </div>

            <div class="about-signature">
                <span>Virginia</span>
                <small>{{ __('site.about.role') }}</small>
            </div>
        </div>
    </section>

    @if ($featuredBook)
        <section id="book-focus" class="writer-section book-focus">
            <div class="book-focus-cover">
                <img src="{{ $bookCover($featuredBook) }}" alt="{{ __('site.content.image_book', ['title' => $featuredBook->title]) }}" loading="lazy"
                    decoding="async">
            </div>

            <div class="book-focus-content">
                <p class="writer-eyebrow">{{ __('site.books.featured') }}</p>
                <h2>{{ $featuredBook->title }}</h2>
                <p>{{ $featuredBook->description }}</p>

                <div class="book-focus-details">
                    <span>{{ $featuredBook->status === 'available' ? __('site.books.available_now') : __('site.books.updating') }}</span>
                    <span>{{ __('site.books.genre') }}</span>
                    <span>{{ __('site.books.audience') }}</span>
                </div>

                <div class="writer-actions">
                    @if ($featuredBook->amazon_url)
                        <a href="{{ $featuredBook->amazon_url }}" target="_blank" class="btn-writer-primary">
                            {{ __('site.books.open_book') }} <span aria-hidden="true">↗</span>
                        </a>
                    @else
                        <a href="#newsletter" class="btn-writer-primary">{{ __('site.books.updates') }}</a>
                    @endif
                    <button type="button" class="btn-writer-secondary" data-modal-open="featured-excerpt-modal">
                        {{ __('site.books.excerpt') }}
                    </button>
                    <a href="{{ route($bookRouteName, $featuredBook) }}" class="btn-writer-secondary">{{ __('site.books.sheet') }}</a>
                </div>
            </div>
        </section>

        <div class="writer-modal" id="featured-excerpt-modal" aria-hidden="true">
            <div class="writer-modal-panel" role="dialog" aria-modal="true" aria-labelledby="featured-excerpt-title">
                <button type="button" class="writer-modal-close" data-modal-close
                    aria-label="{{ __('site.books.close_excerpt') }}">×</button>
                <p class="writer-eyebrow">{{ __('site.books.modal_eyebrow') }}</p>
                <h2 id="featured-excerpt-title">{{ $featuredBook->title }}</h2>
                <p>{{ $featuredBook->excerpt ?: __('site.books.empty_excerpt') }}</p>
                <div class="writer-actions">
                    <a href="{{ route($bookRouteName, $featuredBook) }}" class="btn-writer-primary">{{ __('site.books.sheet') }}</a>
                </div>
            </div>
        </div>
    @endif

    <section id="books" class="writer-section">
        <div class="writer-section-header">
            <div>
                <p class="writer-eyebrow">{{ __('site.books.section_eyebrow') }}</p>
                <h2>{{ __('site.books.section_title') }}</h2>
            </div>
            <p>
                {{ __('site.books.section_body') }}
            </p>
        </div>

        <div class="book-grid">
            @forelse ($books as $book)
                <article class="book-card" style="background-image: url('{{ $bookCover($book) }}')">
                    <div class="book-overlay"></div>

                    <div class="book-content">
                        @if ($book->status === 'available')
                            <span class="book-badge available">{{ __('site.books.available') }}</span>
                        @elseif ($book->status === 'coming')
                            <span class="book-badge coming">{{ __('site.books.coming') }}</span>
                        @else
                            <span class="book-badge writing">{{ __('site.books.writing') }}</span>
                        @endif

                        <h3>{{ $book->title }}</h3>

                        <p>{{ $book->description }}</p>

                        @if ($book->amazon_url)
                            <a href="{{ $book->amazon_url }}" target="_blank" class="btn-writer-primary">
                                {{ __('site.books.buy') }} <span aria-hidden="true">↗</span>
                            </a>
                        @else
                            <a href="#contact" class="btn-writer-secondary">
                                {{ __('site.books.updates') }}
                            </a>
                        @endif
                        <a href="{{ route($bookRouteName, $book) }}" class="writer-read-more">
                            {{ __('site.books.sheet') }} <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </article>
            @empty
                <p>{{ __('site.books.empty') }}</p>
            @endforelse
        </div>
    </section>
    <section id="events" class="writer-section">
        <div class="writer-section-header">
            <div>
                <p class="writer-eyebrow">{{ __('site.events.eyebrow') }}</p>
                <h2>{{ __('site.events.title') }}</h2>
            </div>
            <p>
                {{ __('site.events.body') }}
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
                        <span>{{ $event->category ?? __('site.events.default_category') }}</span>
                        <h3>{{ $event->title }}</h3>

                        <p>
                            {{ $event->description }}
                            @if ($event->location)
                                <br><strong>{{ __('site.events.location') }}</strong> {{ $event->location }}
                            @endif
                        </p>
                    </div>

                    @if ($event->link)
                        <a href="{{ $event->link }}" target="_blank" class="event-link">{{ __('site.events.info') }}</a>
                    @else
                        <a href="#contact" class="event-link">{{ __('site.events.ask') }}</a>
                    @endif
                </article>
            @empty
                <article class="event-card event-disabled">
                    <div class="event-date">
                        <strong>SOON</strong>
                        <span>2026</span>
                    </div>

                    <div class="event-content">
                        <span>{{ __('site.events.soon_label') }}</span>
                        <h3>{{ __('site.events.soon_title') }}</h3>
                        <p>{{ __('site.events.soon_body') }}</p>
                    </div>

                    <a href="#contact" class="event-link">{{ __('site.events.soon_label') }}</a>
                </article>
            @endforelse
        </div>
    </section>

    <section id="blog" class="writer-section">
        <div class="writer-section-header">
            <div>
                <p class="writer-eyebrow">{{ __('site.blog.eyebrow') }}</p>
                <h2>{{ __('site.blog.title') }}</h2>
            </div>
            <p>
                {{ __('site.blog.body') }}
            </p>
        </div>

        @if ($categories->isNotEmpty())
            <div class="blog-filters" aria-label="{{ __('site.blog.filters') }}">
                <a href="{{ $homeRoute }}#blog" class="{{ $activeCategory ? '' : 'is-active' }}">{{ __('site.blog.all') }}</a>
                @foreach ($categories as $category)
                    <a href="{{ $homeRoute }}?category={{ urlencode($category) }}#blog"
                        class="{{ $activeCategory === $category ? 'is-active' : '' }}">
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

                    <a href="{{ route($postRouteName, $post->slug) }}" class="writer-read-more">
                        {{ __('site.blog.read') }} <span aria-hidden="true">→</span>
                    </a>
                </article>
            @empty
                <article class="writer-card">
                    <span>Blog</span>
                    <h3>{{ __('site.blog.empty_title') }}</h3>
                    <p>{{ __('site.blog.empty_body') }}</p>
                </article>
            @endforelse
        </div>
    </section>

    <section id="newsletter" class="writer-section newsletter-section">
        <div class="newsletter-content">
            <p class="writer-eyebrow">{{ __('site.newsletter.eyebrow') }}</p>
            <h2>{{ __('site.newsletter.title') }}</h2>
            <p>
                {{ __('site.newsletter.body') }}
            </p>
        </div>

        <form class="newsletter-form" action="{{ route($newsletterRouteName) }}" method="post">
            @csrf
            <label for="newsletter-email">{{ __('site.newsletter.email') }}</label>
            <div>
                <input id="newsletter-email" type="email" name="email" placeholder="{{ __('site.newsletter.placeholder') }}"
                    value="{{ old('email') }}" required>
                <button type="submit" class="btn-writer-primary">{{ __('site.newsletter.submit') }}</button>
            </div>
            @if (session('newsletter_status'))
                <strong class="form-status">{{ session('newsletter_status') }}</strong>
            @endif
            @error('email')
                <strong class="form-status form-status-error">{{ $message }}</strong>
            @enderror
            <small>{{ __('site.newsletter.small') }}</small>
        </form>
    </section>

    <section class="writer-section testimonials-section">
        <div class="writer-section-header">
            <div>
                <p class="writer-eyebrow">{{ __('site.testimonials.eyebrow') }}</p>
                <h2>{{ __('site.testimonials.title') }}</h2>
            </div>
            <p>
                {{ __('site.testimonials.body') }}
            </p>
        </div>

        <div class="testimonials-grid">
            @forelse ($testimonials as $testimonial)
                <article>
                    <p>“{{ $testimonial->quote }}”</p>
                    <span>{{ $testimonial->author ?? ($testimonial->context ?? __('site.content.reader')) }}</span>
                </article>
            @empty
                <article>
                    <p>{{ __('site.testimonials.fallback_quote') }}</p>
                    <span>{{ __('site.testimonials.fallback_author') }}</span>
                </article>
            @endforelse
        </div>
    </section>

    <section id="press" class="writer-section press-section">
        <div class="writer-section-header">
            <div>
                <p class="writer-eyebrow">{{ __('site.press.eyebrow') }}</p>
                <h2>{{ __('site.press.title') }}</h2>
            </div>
            <p>
                {{ __('site.press.body') }}
            </p>
        </div>

        <div class="press-grid">
            <article class="press-card press-card-main">
                <span>{{ __('site.press.bio_label') }}</span>
                <p>
                    {{ __('site.press.bio') }}
                </p>
                <button type="button" class="post-share"
                    data-copy-text="{{ __('site.press.bio') }}"
                    data-copied-label="{{ __('site.copy.bio_copied') }}"
                    data-copy-failed-label="{{ __('site.copy.failed') }}">
                    {{ __('site.press.copy') }}
                </button>
            </article>

            <article class="press-card">
                <span>{{ __('site.press.events_label') }}</span>
                <h3>{{ __('site.press.events_title') }}</h3>
                <p>{{ __('site.press.events_body') }}</p>
            </article>

            <article class="press-card">
                <span>{{ __('site.press.press_label') }}</span>
                <h3>{{ __('site.press.press_title') }}</h3>
                <p>{{ __('site.press.press_body') }}</p>
            </article>
        </div>
    </section>

    <section id="contact" class="writer-section writer-contact">
        <p class="writer-eyebrow">{{ __('site.contact.eyebrow') }}</p>
        <h2>{{ __('site.contact.title') }}</h2>

        <p>
            {{ __('site.contact.body') }}
        </p>

        <div class="writer-actions">
            <a href="mailto:email@example.com?subject=Contatto%20dal%20sito%20di%20Virginia"
                class="btn-writer-primary">
                {{ __('site.contact.mail') }}
            </a>
            <a href="#blog" class="btn-writer-secondary">{{ __('site.contact.blog') }}</a>
        </div>
    </section>

</x-layout>
