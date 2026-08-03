<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\NewsletterSubscriber;
use App\Models\Post;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_guests_cannot_read_exports_or_mutate_admin_content(): void
    {
        $book = Book::create($this->bookAttributes('Existing book', 'existing-book'));
        $post = Post::create($this->postAttributes('Existing post', 'existing-post'));
        $testimonial = Testimonial::create(['quote' => 'Existing quote']);
        $subscriber = NewsletterSubscriber::create(['email' => 'reader@example.com']);

        $requests = [
            fn () => $this->get(route('admin.newsletter.index')),
            fn () => $this->get(route('admin.newsletter.export')),
            fn () => $this->post(route('admin.books.store'), $this->bookPayload('Injected', 'injected')),
            fn () => $this->put(route('admin.books.update', $book), $this->bookPayload('Changed', 'changed')),
            fn () => $this->delete(route('admin.books.destroy', $book)),
            fn () => $this->post(route('admin.posts.store'), $this->postPayload('Injected', 'injected')),
            fn () => $this->put(route('admin.posts.update', $post), $this->postPayload('Changed', 'changed')),
            fn () => $this->delete(route('admin.posts.destroy', $post)),
            fn () => $this->post(route('admin.testimonials.store'), ['quote' => 'Injected']),
            fn () => $this->put(route('admin.testimonials.update', $testimonial), ['quote' => 'Changed']),
            fn () => $this->delete(route('admin.testimonials.destroy', $testimonial)),
            fn () => $this->delete(route('admin.newsletter.destroy', $subscriber)),
        ];

        foreach ($requests as $request) {
            $request()->assertRedirect(route('admin.login'));
        }

        $this->assertDatabaseCount('books', 1);
        $this->assertDatabaseHas('books', ['title' => 'Existing book']);
        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', ['title' => 'Existing post']);
        $this->assertDatabaseCount('testimonials', 1);
        $this->assertDatabaseCount('newsletter_subscribers', 1);
    }

    public function test_non_admin_users_cannot_access_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_manage_books_posts_testimonials_and_subscribers(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('admin.books.store'), $this->bookPayload('Managed book', ''))->assertRedirect();
        $book = Book::sole();
        $this->put(route('admin.books.update', $book), $this->bookPayload('Managed book updated', $book->slug))->assertRedirect();

        $this->post(route('admin.posts.store'), $this->postPayload('Managed post', ''))->assertRedirect();
        $post = Post::sole();
        $this->put(route('admin.posts.update', $post), $this->postPayload('Managed post updated', $post->slug))->assertRedirect();

        $this->post(route('admin.testimonials.store'), ['quote' => 'Managed quote', 'is_active' => '1'])->assertRedirect();
        $testimonial = Testimonial::sole();
        $this->put(route('admin.testimonials.update', $testimonial), ['quote' => 'Managed quote updated'])->assertRedirect();

        $subscriber = NewsletterSubscriber::create(['email' => 'reader@example.com']);
        $this->get(route('admin.newsletter.export'))->assertOk()->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->delete(route('admin.newsletter.destroy', $subscriber))->assertRedirect();

        $this->assertDatabaseHas('books', ['title' => 'Managed book updated']);
        $this->assertDatabaseHas('posts', ['title' => 'Managed post updated']);
        $this->assertDatabaseHas('testimonials', ['quote' => 'Managed quote updated']);
        $this->assertDatabaseMissing('newsletter_subscribers', ['email' => 'reader@example.com']);

        $this->delete(route('admin.books.destroy', $book->fresh()))->assertRedirect();
        $this->delete(route('admin.posts.destroy', $post->fresh()))->assertRedirect();
        $this->delete(route('admin.testimonials.destroy', $testimonial->fresh()))->assertRedirect();

        $this->assertDatabaseCount('books', 0);
        $this->assertDatabaseCount('posts', 0);
        $this->assertDatabaseCount('testimonials', 0);
    }

    public function test_admin_published_changes_are_visible_publicly_in_both_locales(): void
    {
        $this->actingAs($this->admin());
        $this->post(route('admin.books.store'), $this->bookPayload('Visible book', ''))->assertRedirect();
        $this->post(route('admin.posts.store'), $this->postPayload('Visible post', ''))->assertRedirect();

        $this->get(route('home'))->assertOk()->assertSee('Visible book')->assertSee('Visible post');
        $this->get(route('home.en'))->assertOk()->assertSee('Visible book')->assertSee('Visible post');
    }

    /** @return array<string, mixed> */
    private function bookPayload(string $title, string $slug): array
    {
        return ['title' => $title, 'slug' => $slug, 'status' => 'available', 'description' => 'Description'];
    }

    /** @return array<string, mixed> */
    private function postPayload(string $title, string $slug): array
    {
        return ['title' => $title, 'slug' => $slug, 'excerpt' => 'Excerpt', 'body' => 'Body', 'is_published' => '1'];
    }

    /** @return array<string, mixed> */
    private function bookAttributes(string $title, string $slug): array
    {
        return [...$this->bookPayload($title, $slug), 'reviews' => []];
    }

    /** @return array<string, mixed> */
    private function postAttributes(string $title, string $slug): array
    {
        return [...$this->postPayload($title, $slug), 'is_published' => true];
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }
}
