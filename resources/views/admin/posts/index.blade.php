<x-layout :hide-chrome="true" body-class="admin-body" title="Admin | Articoli" description="Gestione articoli del sito di Virginia.">
    @include('admin.partials.header', ['title' => 'Articoli'])

    <div class="admin-actions">
        <a href="{{ route('admin.posts.create') }}" class="btn-writer-primary">Nuovo articolo</a>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Titolo</th>
                    <th>Categoria</th>
                    <th>Pubblicato</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr>
                        <td>{{ $post->title }}</td>
                        <td>{{ $post->category }}</td>
                        <td>{{ $post->is_published ? 'Sì' : 'No' }}</td>
                        <td class="admin-row-actions">
                            <a href="{{ route('posts.show', $post->slug) }}">Vedi</a>
                            <a href="{{ route('admin.posts.edit', $post) }}">Modifica</a>
                            <form action="{{ route('admin.posts.destroy', $post) }}" method="post" data-confirm="Sei sicuro di voler eliminare questo elemento?">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Elimina</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Nessun articolo.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('admin.partials.footer')
</x-layout>
