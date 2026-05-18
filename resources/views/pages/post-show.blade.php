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
        'url' => route('posts.show', $post->slug),
        'image' => $post->image ? asset($post->image) : asset('images/virginia.jpg'),
    ];
@endphp

<x-layout
    title="{{ $post->meta_title ?: $post->title.' | Virginia' }}"
    description="{{ $post->meta_description ?: $post->excerpt }}"
    :image="$post->image ? asset($post->image) : asset('images/virginia.jpg')"
    type="article"
    :schema="$postSchema"
>

    <section class="post-hero">
        <div class="post-tools">
            <a href="{{ route('home') }}#blog" class="post-back">
                <span aria-hidden="true">←</span> Torna al blog
            </a>

            <button type="button" class="post-share" data-copy-url="{{ url()->current() }}">
                Copia link
            </button>
        </div>

        <p class="writer-eyebrow">{{ $post->category ?? 'Blog' }}</p>

        <h1>{{ $post->title }}</h1>

        <p>{{ $post->excerpt }}</p>
    </section>

    <article class="post-content">
        {!! nl2br(e($post->body)) !!}
    </article>

</x-layout>
