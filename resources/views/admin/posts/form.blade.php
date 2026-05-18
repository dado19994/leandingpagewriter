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
                <input name="title" value="{{ old('title', $post->title) }}" required>
            </label>
            <label>
                Slug
                <input name="slug" value="{{ old('slug', $post->slug) }}" placeholder="generato dal titolo se vuoto">
            </label>
            <label>
                Categoria
                <input name="category" value="{{ old('category', $post->category) }}">
            </label>
            <label>
                Immagine
                <input name="image" value="{{ old('image', $post->image) }}" placeholder="images/post.jpg">
            </label>
            <label>
                Upload immagine
                <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp">
            </label>
            <label>
                Meta title
                <input name="meta_title" value="{{ old('meta_title', $post->meta_title) }}" maxlength="255">
            </label>
            <label>
                Meta description
                <input name="meta_description" value="{{ old('meta_description', $post->meta_description) }}">
            </label>
        </div>

        <label data-editor-wrap>
            Estratto
            <textarea name="excerpt" data-editor>{{ old('excerpt', $post->excerpt) }}</textarea>
            <div class="admin-preview" data-editor-preview></div>
        </label>
        <label data-editor-wrap>
            Corpo articolo
            <textarea name="body" rows="12" data-editor>{{ old('body', $post->body) }}</textarea>
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
