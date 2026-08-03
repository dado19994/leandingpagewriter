@php
    $postSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $post->title,
        'description' => $post->excerpt,
        'author' => [
            '@type' => 'Person',
            'name' => 'Virginia',
        ],
        'datePublished' => optional($post->created_at)->toAtomString(),
        'dateModified' => optional($post->updated_at)->toAtomString(),
        'url' => app()->getLocale() === 'en' ? route('posts.show.en', $post->slug) : route('posts.show', $post->slug),
        'image' => $post->image ? asset($post->image) : asset('images/virginia.jpg'),
    ];
    $homeRoute = app()->getLocale() === 'en' ? route('home.en') : route('home');
    $canonicalRoute = app()->getLocale() === 'en' ? route('posts.show.en', $post->slug) : route('posts.show', $post->slug);
    $alternateRoute = app()->getLocale() === 'en' ? route('posts.show', $post->slug) : route('posts.show.en', $post->slug);
@endphp

<x-layout
    title="{{ $post->meta_title ?: $post->title.' | Virginia' }}"
    description="{{ $post->meta_description ?: $post->excerpt }}"
    :image="$post->image ? asset($post->image) : asset('images/virginia.jpg')"
    type="article"
    :schema="$postSchema"
    :canonical="$canonicalRoute"
    :alternate="$alternateRoute"
>

    <section class="post-hero">
        <div class="post-tools">
            <a href="{{ $homeRoute }}#blog" class="post-back">
                <span aria-hidden="true">←</span> {{ __('site.post_page.back') }}
            </a>

            <button type="button" class="post-share" data-copy-url="{{ url()->current() }}"
                data-copied-label="{{ __('site.post_page.copied') }}"
                data-copy-failed-label="{{ __('site.post_page.copy_failed') }}">
                {{ __('site.post_page.copy') }}
            </button>
        </div>

        @if (app()->getLocale() === 'en')
            <p role="note">{{ __('site.content.fallback_notice') }}</p>
        @endif

        <p class="writer-eyebrow">{{ $post->category ?? 'Blog' }}</p>

        <h1>{{ $post->title }}</h1>

        <p>{{ $post->excerpt }}</p>
    </section>

    <article class="post-content">
        {!! nl2br(e($post->body)) !!}
    </article>

</x-layout>
