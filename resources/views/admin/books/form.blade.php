@php
    $isEditing = $book->exists;
    $reviewsText = collect(old('reviews_text', $book->reviews ?? []))
        ->map(fn ($review) => is_array($review) ? (($review['quote'] ?? '').' | '.($review['author'] ?? '')) : $review)
        ->implode("\n");
@endphp

<x-layout :hide-chrome="true" body-class="admin-body" title="Admin | {{ $isEditing ? 'Modifica libro' : 'Nuovo libro' }}" description="Form libro.">
    @include('admin.partials.header', ['title' => $isEditing ? 'Modifica libro' : 'Nuovo libro'])

    <form
        class="admin-form admin-form-wide"
        action="{{ $isEditing ? route('admin.books.update', $book) : route('admin.books.store') }}"
        method="post"
        enctype="multipart/form-data"
    >
        @csrf
        @if ($isEditing)
            @method('PUT')
        @endif

        <div class="admin-form-grid">
            <label>
                Titolo
                <input name="title" value="{{ old('title', $book->title) }}" required data-slug-source>
                @error('title')
                    <small class="admin-field-error">{{ $message }}</small>
                @enderror
            </label>
            <label>
                Slug
                <input name="slug" value="{{ old('slug', $book->slug) }}" placeholder="generato dal titolo se vuoto" data-slug-target>
                <small class="admin-field-hint">Anteprima: <span data-slug-preview>{{ old('slug', $book->slug) ?: 'generato-dal-titolo' }}</span></small>
                @error('slug')
                    <small class="admin-field-error">{{ $message }}</small>
                @enderror
            </label>
            <label>
                Stato
                <select name="status">
                    @foreach (['available' => 'Disponibile', 'coming' => 'Prossima uscita', 'writing' => 'In scrittura'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $book->status ?: 'available') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')
                    <small class="admin-field-error">{{ $message }}</small>
                @enderror
            </label>
            <label>
                Genere
                <input name="genre" value="{{ old('genre', $book->genre) }}">
                @error('genre')
                    <small class="admin-field-error">{{ $message }}</small>
                @enderror
            </label>
            <label>
                Cover path
                <input name="cover" value="{{ old('cover', $book->cover) }}" placeholder="images/books/libro.jpg" data-image-path>
                @error('cover')
                    <small class="admin-field-error">{{ $message }}</small>
                @enderror
            </label>
            <label>
                Upload cover
                <input type="file" name="cover_file" accept="image/jpeg,image/png,image/webp" data-image-upload>
                @error('cover_file')
                    <small class="admin-field-error">{{ $message }}</small>
                @enderror
            </label>
            <div class="admin-image-preview" data-image-preview>
                @if (old('cover', $book->cover))
                    <img src="{{ asset(old('cover', $book->cover)) }}" alt="Anteprima cover">
                @else
                    <span>Nessuna immagine selezionata</span>
                @endif
            </div>
            <label>
                Amazon URL
                <input name="amazon_url" value="{{ old('amazon_url', $book->amazon_url) }}">
                @error('amazon_url')
                    <small class="admin-field-error">{{ $message }}</small>
                @enderror
            </label>
            <label>
                Meta title
                <input name="meta_title" value="{{ old('meta_title', $book->meta_title) }}" maxlength="255">
                @error('meta_title')
                    <small class="admin-field-error">{{ $message }}</small>
                @enderror
            </label>
            <label>
                Meta description
                <input name="meta_description" value="{{ old('meta_description', $book->meta_description) }}">
                @error('meta_description')
                    <small class="admin-field-error">{{ $message }}</small>
                @enderror
            </label>
        </div>

        <label data-editor-wrap>
            Descrizione breve
            <textarea name="description" required data-editor>{{ old('description', $book->description) }}</textarea>
            @error('description')
                <small class="admin-field-error">{{ $message }}</small>
            @enderror
            <div class="admin-preview" data-editor-preview></div>
        </label>
        <label data-editor-wrap>
            Sinossi
            <textarea name="synopsis" data-editor>{{ old('synopsis', $book->synopsis) }}</textarea>
            @error('synopsis')
                <small class="admin-field-error">{{ $message }}</small>
            @enderror
            <div class="admin-preview" data-editor-preview></div>
        </label>
        <label data-editor-wrap>
            Estratto
            <textarea name="excerpt" data-editor>{{ old('excerpt', $book->excerpt) }}</textarea>
            @error('excerpt')
                <small class="admin-field-error">{{ $message }}</small>
            @enderror
            <div class="admin-preview" data-editor-preview></div>
        </label>
        <label>
            Recensioni
            <textarea name="reviews_text" placeholder="Citazione | Autore">{{ $reviewsText }}</textarea>
            @error('reviews_text')
                <small class="admin-field-error">{{ $message }}</small>
            @enderror
        </label>
        <label class="admin-check">
            <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $book->is_featured))>
            Libro in evidenza
        </label>

        <div class="admin-actions">
            <button type="submit" class="btn-writer-primary">{{ $isEditing ? 'Salva libro' : 'Crea libro' }}</button>
            <a href="{{ route('admin.books.index') }}" class="btn-writer-secondary">Annulla</a>
        </div>
    </form>

    @include('admin.partials.footer')
</x-layout>
