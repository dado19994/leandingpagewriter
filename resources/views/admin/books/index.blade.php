<x-layout :hide-chrome="true" body-class="admin-body" title="Admin | Libri" description="Gestione libri del sito di Virginia.">
    @include('admin.partials.header', ['title' => 'Libri'])

    <div class="admin-actions">
        <a href="{{ route('admin.books.create') }}" class="btn-writer-primary">Nuovo libro</a>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Titolo</th>
                    <th>Stato</th>
                    <th>Featured</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($books as $book)
                    <tr>
                        <td>{{ $book->title }}</td>
                        <td>{{ $book->status }}</td>
                        <td>{{ $book->is_featured ? 'Sì' : 'No' }}</td>
                        <td class="admin-row-actions">
                            <a href="{{ route('books.show', $book) }}">Vedi</a>
                            <a href="{{ route('admin.books.edit', $book) }}">Modifica</a>
                            <form action="{{ route('admin.books.destroy', $book) }}" method="post" data-confirm="Sei sicuro di voler eliminare questo elemento?">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Elimina</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Nessun libro.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('admin.partials.footer')
</x-layout>
