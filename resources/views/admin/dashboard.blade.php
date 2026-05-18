<x-layout :hide-chrome="true" body-class="admin-body" title="Admin | Dashboard" description="Dashboard amministrativa del sito di Virginia.">
    @include('admin.partials.header', ['title' => 'Dashboard'])

    <div class="admin-grid">
        <a href="{{ route('admin.books.index') }}" class="admin-card">
            <span>Libri</span>
            <strong>{{ $booksCount }}</strong>
        </a>
        <a href="{{ route('admin.posts.index') }}" class="admin-card">
            <span>Articoli</span>
            <strong>{{ $postsCount }}</strong>
        </a>
        <a href="{{ route('admin.newsletter.index') }}" class="admin-card">
            <span>Newsletter</span>
            <strong>{{ $subscribersCount }}</strong>
        </a>
        <a href="{{ route('admin.testimonials.index') }}" class="admin-card">
            <span>Testimonianze</span>
            <strong>{{ $testimonialsCount }}</strong>
        </a>
    </div>

    <div class="admin-dashboard-panels">
        <section class="admin-panel">
            <h2>Ultimi articoli</h2>
            @forelse ($latestPosts as $post)
                <a href="{{ route('admin.posts.edit', $post) }}">{{ $post->title }}</a>
            @empty
                <p>Nessun articolo.</p>
            @endforelse
        </section>

        <section class="admin-panel">
            <h2>Ultimi iscritti</h2>
            @forelse ($latestSubscribers as $subscriber)
                <p>{{ $subscriber->email }}</p>
            @empty
                <p>Nessun iscritto.</p>
            @endforelse
        </section>
    </div>

    @include('admin.partials.footer')
</x-layout>
