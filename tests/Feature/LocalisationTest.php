<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalisationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_homepages_have_distinct_locales_canonicals_alternates_and_continuous_links(): void
    {
        $book = Book::create($this->bookAttributes());
        $post = Post::create($this->postAttributes());

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<html lang="it">', false)
            ->assertSee('Scrivo per chi')
            ->assertSee(route('books.show', $book), false)
            ->assertSee(route('posts.show', $post), false)
            ->assertSee(route('home.en'), false);

        $this->get(route('home.en'))
            ->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('I write for those')
            ->assertSee(__('site.content.fallback_notice', [], 'en'))
            ->assertSee(route('books.show.en', $book), false)
            ->assertSee(route('posts.show.en', $post), false)
            ->assertSee(route('home'), false);
    }

    public function test_detail_pages_preserve_locale_and_switch_to_the_equivalent_page(): void
    {
        $book = Book::create($this->bookAttributes());
        $post = Post::create($this->postAttributes());

        $this->get(route('books.show.en', $book))
            ->assertOk()
            ->assertSee('Back to books')
            ->assertSee(route('books.show', $book), false)
            ->assertSee('rel="canonical" href="'.route('books.show.en', $book).'"', false);

        $this->get(route('posts.show.en', $post))
            ->assertOk()
            ->assertSee('Back to the blog')
            ->assertSee(route('posts.show', $post), false)
            ->assertSee('Link copied', false);
    }

    public function test_newsletter_feedback_is_localized_and_sitemap_contains_both_locales(): void
    {
        $book = Book::create($this->bookAttributes());
        $post = Post::create($this->postAttributes());

        $this->from(route('home.en'))->post(route('newsletter.store.en'), ['email' => 'reader@example.com'])
            ->assertRedirect(route('home.en'))
            ->assertSessionHas('newsletter_status', 'Subscription received. Thank you for being here.');

        $this->from(route('home.en'))->post(route('newsletter.store.en'), ['email' => 'invalid'])
            ->assertRedirect(route('home.en'))
            ->assertSessionHasErrors(['email' => 'The email field must be a valid email address.']);

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertSee(route('home.en'), false)
            ->assertSee(route('books.show.en', $book), false)
            ->assertSee(route('posts.show.en', $post), false);
    }

    /** @return array<string, mixed> */
    private function bookAttributes(): array
    {
        return ['title' => 'Libro italiano', 'slug' => 'libro-italiano', 'status' => 'available', 'description' => 'Testo italiano'];
    }

    /** @return array<string, mixed> */
    private function postAttributes(): array
    {
        return ['title' => 'Articolo italiano', 'slug' => 'articolo-italiano', 'excerpt' => 'Estratto italiano', 'body' => 'Corpo italiano', 'is_published' => true];
    }
}
