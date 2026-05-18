<x-layout :hide-chrome="true" body-class="admin-body" title="Admin | Newsletter" description="Iscritti newsletter del sito di Virginia.">
    @include('admin.partials.header', ['title' => 'Newsletter'])

    <div class="admin-actions">
        <a href="{{ route('admin.newsletter.export') }}" class="btn-writer-primary">Esporta CSV</a>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Fonte</th>
                    <th>Iscrizione</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subscribers as $subscriber)
                    <tr>
                        <td>{{ $subscriber->email }}</td>
                        <td>{{ $subscriber->source }}</td>
                        <td>{{ optional($subscriber->subscribed_at)->format('d/m/Y H:i') }}</td>
                        <td class="admin-row-actions">
                            <form action="{{ route('admin.newsletter.destroy', $subscriber) }}" method="post" data-confirm="Sei sicuro di voler eliminare questo elemento?">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Rimuovi</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Nessun iscritto.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $subscribers->links() }}

    @include('admin.partials.footer')
</x-layout>
