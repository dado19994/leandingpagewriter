<?php

use App\Actions\SaveWithUniqueSlug;
use App\Actions\StorePublicImage;
use App\Models\Book;
use App\Models\Event;
use App\Models\NewsletterSubscriber;
use App\Models\Post;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

$requireAdmin = function () {
    return Auth::check() && Auth::user()->is_admin ? null : redirect()->route('admin.login');
};

$parseReviews = function (?string $value): array {
    return collect(preg_split("/\r\n|\n|\r/", (string) $value))
        ->map(fn ($line) => trim($line))
        ->filter()
        ->map(function ($line) {
            [$quote, $author] = array_pad(explode('|', $line, 2), 2, 'Lettura');

            return [
                'quote' => trim($quote),
                'author' => trim($author) ?: 'Lettura',
            ];
        })
        ->values()
        ->all();
};

Route::get('/', function (Request $request) {
    $category = $request->query('category');
    $postsQuery = Post::where('is_published', true)->latest();

    if ($category) {
        $postsQuery->where('category', $category);
    }

    $books = Book::latest()->get();
    $events = Event::where('is_active', true)
        ->orderBy('event_date')
        ->get();
    $posts = $postsQuery->get();
    $testimonials = Testimonial::where('is_active', true)->latest()->get();
    $categories = Post::where('is_published', true)
        ->whereNotNull('category')
        ->distinct()
        ->orderBy('category')
        ->pluck('category');

    return view('pages.home', [
        'books' => $books,
        'events' => $events,
        'posts' => $posts,
        'testimonials' => $testimonials,
        'categories' => $categories,
        'activeCategory' => $category,
    ]);
})->name('home');

Route::get('/robots.txt', function () {
    return response("User-agent: *\nAllow: /\nSitemap: ".route('sitemap')."\n", 200, ['Content-Type' => 'text/plain']);
})->name('robots');

Route::get('/sitemap.xml', function () {
    $urls = collect([route('home')])
        ->merge(Book::all()->map(fn ($book) => route('books.show', $book)))
        ->merge(Post::where('is_published', true)->get()->map(fn ($post) => route('posts.show', $post->slug)));

    return response()
        ->view('sitemap', ['urls' => $urls])
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::post('/newsletter', function (Request $request) {
    $validated = $request->validate([
        'email' => ['required', 'email', 'max:255'],
    ]);

    NewsletterSubscriber::updateOrCreate(
        ['email' => $validated['email']],
        ['source' => 'website', 'subscribed_at' => now()]
    );

    return back()->with('newsletter_status', 'Iscrizione ricevuta. Grazie per essere qui.');
})->name('newsletter.store');

Route::get('/libri/{book}', function (Book $book) {
    return view('pages.book-show', compact('book'));
})->name('books.show');

Route::get('/blog/{post:slug}', function (Post $post) {
    return view('pages.post-show', compact('post'));
})->name('posts.show');

Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

Route::post('/admin/login', function (Request $request) {
    $validated = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $throttleKey = Str::transliterate(Str::lower((string) $request->string('email'))).'|'.($request->ip() ?? 'unknown');

    if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
        $seconds = RateLimiter::availableIn($throttleKey);

        return back()->withErrors([
            'email' => "Troppi tentativi. Riprova tra {$seconds} secondi.",
        ])->onlyInput('email');
    }

    $remember = $request->boolean('remember');

    if (! Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']], $remember)) {
        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors(['email' => 'Credenziali non corrette.'])->onlyInput('email');
    }

    if (! Auth::user()->is_admin) {
        Auth::logout();
        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors(['email' => 'Questo utente non ha accesso al pannello.'])->onlyInput('email');
    }

    RateLimiter::clear($throttleKey);
    $request->session()->regenerate();

    return redirect()->route('admin.dashboard');
})->name('admin.login.store');

Route::post('/admin/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home');
})->name('admin.logout');

Route::get('/admin', function () use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    return view('admin.dashboard', [
        'booksCount' => Book::count(),
        'postsCount' => Post::count(),
        'subscribersCount' => NewsletterSubscriber::count(),
        'testimonialsCount' => Testimonial::count(),
        'latestPosts' => Post::latest()->take(5)->get(),
        'latestSubscribers' => NewsletterSubscriber::latest()->take(5)->get(),
    ]);
})->name('admin.dashboard');

Route::get('/admin/books', function () use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    return view('admin.books.index', [
        'books' => Book::latest()->get(),
    ]);
})->name('admin.books.index');

Route::get('/admin/books/create', function () use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    return view('admin.books.form', ['book' => new Book]);
})->name('admin.books.create');

Route::post('/admin/books', function (Request $request, SaveWithUniqueSlug $saveWithUniqueSlug, StorePublicImage $storePublicImage) use ($requireAdmin, $parseReviews) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'title' => ['required', 'string', 'max:255'],
        'slug' => ['nullable', 'string', 'max:255'],
        'status' => ['required', Rule::in(['available', 'coming', 'writing'])],
        'genre' => ['nullable', 'string', 'max:255'],
        'description' => ['required', 'string'],
        'synopsis' => ['nullable', 'string'],
        'excerpt' => ['nullable', 'string'],
        'reviews_text' => ['nullable', 'string'],
        'cover' => ['nullable', 'string', 'max:255'],
        'cover_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        'amazon_url' => ['nullable', 'url', 'max:255'],
        'meta_title' => ['nullable', 'string', 'max:255'],
        'meta_description' => ['nullable', 'string'],
        'is_featured' => ['nullable', 'boolean'],
    ]);

    $saveWithUniqueSlug->handle(new Book, [
        ...collect($validated)->except(['reviews_text', 'cover_file', 'slug'])->all(),
        'reviews' => $parseReviews($validated['reviews_text'] ?? null),
        'cover' => $request->hasFile('cover_file')
            ? $storePublicImage->handle($request->file('cover_file'), 'cover_file')
            : ($validated['cover'] ?? null),
        'is_featured' => $request->boolean('is_featured'),
    ], ($validated['slug'] ?? null) ?: $validated['title']);

    return redirect()->route('admin.books.index')->with('admin_status', 'Libro creato.');
})->name('admin.books.store');

Route::get('/admin/books/{book}/edit', function (Book $book) use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    return view('admin.books.form', compact('book'));
})->name('admin.books.edit');

Route::put('/admin/books/{book}', function (Request $request, SaveWithUniqueSlug $saveWithUniqueSlug, StorePublicImage $storePublicImage, Book $book) use ($requireAdmin, $parseReviews) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'title' => ['required', 'string', 'max:255'],
        'slug' => ['nullable', 'string', 'max:255'],
        'status' => ['required', Rule::in(['available', 'coming', 'writing'])],
        'genre' => ['nullable', 'string', 'max:255'],
        'description' => ['required', 'string'],
        'synopsis' => ['nullable', 'string'],
        'excerpt' => ['nullable', 'string'],
        'reviews_text' => ['nullable', 'string'],
        'cover' => ['nullable', 'string', 'max:255'],
        'cover_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        'amazon_url' => ['nullable', 'url', 'max:255'],
        'meta_title' => ['nullable', 'string', 'max:255'],
        'meta_description' => ['nullable', 'string'],
        'is_featured' => ['nullable', 'boolean'],
    ]);

    $saveWithUniqueSlug->handle($book, [
        ...collect($validated)->except(['reviews_text', 'cover_file', 'slug'])->all(),
        'reviews' => $parseReviews($validated['reviews_text'] ?? null),
        'cover' => $request->hasFile('cover_file')
            ? $storePublicImage->handle($request->file('cover_file'), 'cover_file')
            : ($validated['cover'] ?? null),
        'is_featured' => $request->boolean('is_featured'),
    ], ($validated['slug'] ?? null) ?: $validated['title']);

    return redirect()->route('admin.books.index')->with('admin_status', 'Libro aggiornato.');
})->name('admin.books.update');

Route::delete('/admin/books/{book}', function (Book $book) use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    $book->delete();

    return redirect()->route('admin.books.index')->with('admin_status', 'Libro eliminato.');
})->name('admin.books.destroy');

Route::get('/admin/posts', function () use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    return view('admin.posts.index', [
        'posts' => Post::latest()->get(),
    ]);
})->name('admin.posts.index');

Route::get('/admin/posts/create', function () use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    return view('admin.posts.form', ['post' => new Post]);
})->name('admin.posts.create');

Route::post('/admin/posts', function (Request $request, SaveWithUniqueSlug $saveWithUniqueSlug, StorePublicImage $storePublicImage) use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'title' => ['required', 'string', 'max:255'],
        'slug' => ['nullable', 'string', 'max:255'],
        'category' => ['nullable', 'string', 'max:255'],
        'excerpt' => ['nullable', 'string'],
        'body' => ['nullable', 'string'],
        'image' => ['nullable', 'string', 'max:255'],
        'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        'meta_title' => ['nullable', 'string', 'max:255'],
        'meta_description' => ['nullable', 'string'],
        'is_published' => ['nullable', 'boolean'],
    ]);

    $saveWithUniqueSlug->handle(new Post, [
        ...collect($validated)->except(['image_file', 'slug'])->all(),
        'image' => $request->hasFile('image_file')
            ? $storePublicImage->handle($request->file('image_file'), 'image_file')
            : ($validated['image'] ?? null),
        'is_published' => $request->boolean('is_published'),
    ], ($validated['slug'] ?? null) ?: $validated['title']);

    return redirect()->route('admin.posts.index')->with('admin_status', 'Articolo creato.');
})->name('admin.posts.store');

Route::get('/admin/posts/{post:slug}/edit', function (Post $post) use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    return view('admin.posts.form', compact('post'));
})->name('admin.posts.edit');

Route::put('/admin/posts/{post:slug}', function (Request $request, SaveWithUniqueSlug $saveWithUniqueSlug, StorePublicImage $storePublicImage, Post $post) use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'title' => ['required', 'string', 'max:255'],
        'slug' => ['nullable', 'string', 'max:255'],
        'category' => ['nullable', 'string', 'max:255'],
        'excerpt' => ['nullable', 'string'],
        'body' => ['nullable', 'string'],
        'image' => ['nullable', 'string', 'max:255'],
        'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        'meta_title' => ['nullable', 'string', 'max:255'],
        'meta_description' => ['nullable', 'string'],
        'is_published' => ['nullable', 'boolean'],
    ]);

    $saveWithUniqueSlug->handle($post, [
        ...collect($validated)->except(['image_file', 'slug'])->all(),
        'image' => $request->hasFile('image_file')
            ? $storePublicImage->handle($request->file('image_file'), 'image_file')
            : ($validated['image'] ?? null),
        'is_published' => $request->boolean('is_published'),
    ], ($validated['slug'] ?? null) ?: $validated['title']);

    return redirect()->route('admin.posts.index')->with('admin_status', 'Articolo aggiornato.');
})->name('admin.posts.update');

Route::delete('/admin/posts/{post:slug}', function (Post $post) use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    $post->delete();

    return redirect()->route('admin.posts.index')->with('admin_status', 'Articolo eliminato.');
})->name('admin.posts.destroy');

Route::get('/admin/newsletter', function () use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    return view('admin.newsletter.index', [
        'subscribers' => NewsletterSubscriber::latest()->paginate(50),
    ]);
})->name('admin.newsletter.index');

Route::get('/admin/newsletter/export', function () use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    $rows = NewsletterSubscriber::latest()->get();
    $csv = "email,source,subscribed_at\n".$rows
        ->map(fn ($row) => sprintf('"%s","%s","%s"', $row->email, $row->source, optional($row->subscribed_at)->toDateTimeString()))
        ->implode("\n");

    return response($csv, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="newsletter.csv"',
    ]);
})->name('admin.newsletter.export');

Route::delete('/admin/newsletter/{subscriber}', function (NewsletterSubscriber $subscriber) use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    $subscriber->delete();

    return redirect()->route('admin.newsletter.index')->with('admin_status', 'Iscritto rimosso.');
})->name('admin.newsletter.destroy');

Route::get('/admin/testimonials', function () use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    return view('admin.testimonials.index', [
        'testimonials' => Testimonial::latest()->get(),
    ]);
})->name('admin.testimonials.index');

Route::get('/admin/testimonials/create', function () use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    return view('admin.testimonials.form', ['testimonial' => new Testimonial]);
})->name('admin.testimonials.create');

Route::post('/admin/testimonials', function (Request $request) use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'quote' => ['required', 'string'],
        'author' => ['nullable', 'string', 'max:255'],
        'context' => ['nullable', 'string', 'max:255'],
        'is_active' => ['nullable', 'boolean'],
    ]);

    Testimonial::create([...$validated, 'is_active' => $request->boolean('is_active')]);

    return redirect()->route('admin.testimonials.index')->with('admin_status', 'Testimonianza creata.');
})->name('admin.testimonials.store');

Route::get('/admin/testimonials/{testimonial}/edit', function (Testimonial $testimonial) use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    return view('admin.testimonials.form', compact('testimonial'));
})->name('admin.testimonials.edit');

Route::put('/admin/testimonials/{testimonial}', function (Request $request, Testimonial $testimonial) use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'quote' => ['required', 'string'],
        'author' => ['nullable', 'string', 'max:255'],
        'context' => ['nullable', 'string', 'max:255'],
        'is_active' => ['nullable', 'boolean'],
    ]);

    $testimonial->update([...$validated, 'is_active' => $request->boolean('is_active')]);

    return redirect()->route('admin.testimonials.index')->with('admin_status', 'Testimonianza aggiornata.');
})->name('admin.testimonials.update');

Route::delete('/admin/testimonials/{testimonial}', function (Testimonial $testimonial) use ($requireAdmin) {
    if ($redirect = $requireAdmin()) {
        return $redirect;
    }

    $testimonial->delete();

    return redirect()->route('admin.testimonials.index')->with('admin_status', 'Testimonianza eliminata.');
})->name('admin.testimonials.destroy');
