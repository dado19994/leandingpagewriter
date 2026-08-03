@php($isEditing = $post->exists)

<x-layout :hide-chrome="true" body-class="admin-body" title="Admin | {{ $isEditing ? 'Modifica articolo' : 'Nuovo articolo' }}" description="Form articolo.">
    @include('admin.partials.header', ['title' => $isEditing ? 'Modifica articolo' : 'Nuovo articolo'])

    <form
        class="admin-form admin-form-wide"
        action="{{ $isEditing ? route('admin.posts.update', $post) : route('admin.posts.store') }}"
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
                <input name="title" value="{{ old('title', $post->title) }}" required data-slug-source>
                @error('title')
                    <small class="admin-field-error">{{ $message }}</small>
                @enderror
            </label>
            <label>
                Slug
                <input name="slug" value="{{ old('slug', $post->slug) }}" placeholder="generato dal titolo se vuoto" data-slug-target>
                <small class="admin-field-hint">Anteprima: <span data-slug-preview>{{ old('slug', $post->slug) ?: 'generato-dal-titolo' }}</span></small>
                @error('slug')
                    <small class="admin-field-error">{{ $message }}</small>
                @enderror
            </label>
            <label>
                Categoria
                <input name="category" value="{{ old('category', $post->category) }}">
                @error('category')
                    <small class="admin-field-error">{{ $message }}</small>
                @enderror
            </label>
            <label>
                Immagine
                <input name="image" value="{{ old('image', $post->image) }}" placeholder="images/post.jpg" data-image-path>
                @error('image')
                    <small class="admin-field-error">{{ $message }}</small>
                @enderror
            </label>
            <label>
                Upload immagine
                <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp" data-image-upload>
                @error('image_file')
                    <small class="admin-field-error">{{ $message }}</small>
                @enderror
            </label>
            <div class="admin-image-preview" data-image-preview>
                @if (old('image', $post->image))
                    <img src="{{ asset(old('image', $post->image)) }}" alt="Anteprima immagine">
                @else
                    <span>Nessuna immagine selezionata</span>
                @endif
            </div>
            <label>
                Meta title
                <input name="meta_title" value="{{ old('meta_title', $post->meta_title) }}" maxlength="255">
                @error('meta_title')
                    <small class="admin-field-error">{{ $message }}</small>
                @enderror
            </label>
            <label>
                Meta description
                <input name="meta_description" value="{{ old('meta_description', $post->meta_description) }}">
                @error('meta_description')
                    <small class="admin-field-error">{{ $message }}</small>
                @enderror
            </label>
        </div>

        <label data-editor-wrap>
            Estratto
            <textarea name="excerpt" data-editor>{{ old('excerpt', $post->excerpt) }}</textarea>
            @error('excerpt')
                <small class="admin-field-error">{{ $message }}</small>
            @enderror
            <div class="admin-preview" data-editor-preview></div>
        </label>
        <label data-editor-wrap>
            Corpo articolo
            <textarea name="body" rows="12" data-editor>{{ old('body', $post->body) }}</textarea>
            @error('body')
                <small class="admin-field-error">{{ $message }}</small>
            @enderror
            <div class="admin-preview" data-editor-preview></div>
        </label>
        <label class="admin-check">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $post->is_published ?? true))>
            Pubblicato
        </label>

        <div class="admin-actions">
            <button type="submit" class="btn-writer-primary">{{ $isEditing ? 'Salva articolo' : 'Crea articolo' }}</button>
            <a href="{{ route('admin.posts.index') }}" class="btn-writer-secondary">Annulla</a>
        </div>
    </form>

    @include('admin.partials.footer')
</x-layout>
