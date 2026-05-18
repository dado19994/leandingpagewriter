@php($isEditing = $testimonial->exists)

<x-layout :hide-chrome="true" body-class="admin-body" title="Admin | {{ $isEditing ? 'Modifica testimonianza' : 'Nuova testimonianza' }}" description="Form testimonianza.">
    @include('admin.partials.header', ['title' => $isEditing ? 'Modifica testimonianza' : 'Nuova testimonianza'])

    <form
        class="admin-form admin-form-wide"
        action="{{ $isEditing ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}"
        method="post"
    >
        @csrf
        @if ($isEditing)
            @method('PUT')
        @endif

        <div class="admin-form-grid">
            <label>
                Autore
                <input name="author" value="{{ old('author', $testimonial->author) }}">
            </label>
            <label>
                Contesto
                <input name="context" value="{{ old('context', $testimonial->context) }}">
            </label>
        </div>

        <label data-editor-wrap>
            Citazione
            <textarea name="quote" required data-editor>{{ old('quote', $testimonial->quote) }}</textarea>
            <div class="admin-preview" data-editor-preview></div>
        </label>

        <label class="admin-check">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $testimonial->is_active ?? true))>
            Attiva
        </label>

        <div class="admin-actions">
            <button type="submit" class="btn-writer-primary">{{ $isEditing ? 'Salva testimonianza' : 'Crea testimonianza' }}</button>
            <a href="{{ route('admin.testimonials.index') }}" class="btn-writer-secondary">Annulla</a>
        </div>
    </form>

    @include('admin.partials.footer')
</x-layout>
