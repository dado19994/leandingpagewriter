<x-layout :hide-chrome="true" body-class="admin-body" title="Admin | Virginia" description="Accesso al pannello amministrativo di Virginia.">
    <section class="admin-login">
        <div class="admin-login-copy">
            <a href="{{ route('home') }}" class="admin-brand">
                Virginia<span>.</span>
                <small>Admin</small>
            </a>
            <p class="writer-eyebrow">Admin</p>
            <h1>Accesso editoriale</h1>
            <p>Inserisci la password impostata in <code>WRITER_ADMIN_PASSWORD</code>.</p>
        </div>

        <form action="{{ route('admin.login.store') }}" method="post" class="admin-form">
            @csrf
            <label>
                Email
                <input type="email" name="email" value="{{ old('email', env('WRITER_ADMIN_EMAIL', 'admin@example.com')) }}" required autofocus>
            </label>
            <label>
                Password
                <input type="password" name="password" required>
            </label>
            <label class="admin-check">
                <input type="checkbox" name="remember" value="1">
                Ricordami
            </label>

            @error('email')
                <strong class="form-status form-status-error">{{ $message }}</strong>
            @enderror

            <button type="submit" class="btn-writer-primary">Entra</button>
        </form>
    </section>
</x-layout>
