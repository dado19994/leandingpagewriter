<x-layout :hide-chrome="true" body-class="admin-body" title="Admin | Testimonianze" description="Gestione testimonianze del sito di Virginia.">
    @include('admin.partials.header', ['title' => 'Testimonianze'])

    <div class="admin-actions">
        <a href="{{ route('admin.testimonials.create') }}" class="btn-writer-primary">Nuova testimonianza</a>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Citazione</th>
                    <th>Autore</th>
                    <th>Attiva</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($testimonials as $testimonial)
                    <tr>
                        <td>{{ $testimonial->quote }}</td>
                        <td>{{ $testimonial->author }}</td>
                        <td>{{ $testimonial->is_active ? 'Sì' : 'No' }}</td>
                        <td class="admin-row-actions">
                            <a href="{{ route('admin.testimonials.edit', $testimonial) }}">Modifica</a>
                            <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="post" data-confirm="Sei sicuro di voler eliminare questo elemento?">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Elimina</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Nessuna testimonianza.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('admin.partials.footer')
</x-layout>
