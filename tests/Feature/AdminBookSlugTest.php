<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBookSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_generates_normalizes_and_accepts_custom_book_slugs(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('admin.books.store'), $this->bookPayload('L’amore è qui', ''))
            ->assertRedirect(route('admin.books.index'));
        $this->post(route('admin.books.store'), $this->bookPayload('Custom', '  Una Slug Speciale!  '))
            ->assertRedirect(route('admin.books.index'));

        $this->assertDatabaseHas('books', ['title' => 'L’amore è qui', 'slug' => 'lamore-e-qui']);
        $this->assertDatabaseHas('books', ['title' => 'Custom', 'slug' => 'una-slug-speciale']);
    }

    public function test_create_allocates_suffixes_across_repeated_book_collisions(): void
    {
        $this->actingAs($this->admin());

        foreach (['One', 'Two', 'Three'] as $title) {
            $this->post(route('admin.books.store'), $this->bookPayload($title, 'same'))->assertRedirect();
        }

        $this->assertSame(['same', 'same-2', 'same-3'], Book::orderBy('id')->pluck('slug')->all());
    }

    public function test_update_preserves_its_slug_and_resolves_a_book_collision(): void
    {
        $this->actingAs($this->admin());
        $first = Book::create($this->bookAttributes('First', 'first'));
        $second = Book::create($this->bookAttributes('Second', 'second'));

        $this->put(route('admin.books.update', $first), $this->bookPayload('First revised', 'first'))->assertRedirect();
        $this->put(route('admin.books.update', $second), $this->bookPayload('Second revised', 'first'))->assertRedirect();

        $this->assertSame('first', $first->fresh()->slug);
        $this->assertSame('first-2', $second->fresh()->slug);
    }

    public function test_update_regenerates_a_normalized_book_slug_when_left_empty(): void
    {
        $this->actingAs($this->admin());
        $book = Book::create($this->bookAttributes('Old title', 'old-title'));

        $this->put(route('admin.books.update', $book), $this->bookPayload('Nuovo titolo è qui', ''))->assertRedirect();

        $this->assertSame('nuovo-titolo-e-qui', $book->fresh()->slug);
    }

    public function test_create_recovers_from_a_write_time_book_slug_race(): void
    {
        $this->actingAs($this->admin());
        $injected = false;

        Book::creating(function (Book $book) use (&$injected): void {
            if ($injected || $book->slug !== 'race') {
                return;
            }

            $injected = true;
            Book::withoutEvents(fn () => Book::create($this->bookAttributes('Competing write', 'race')));
        });

        $this->post(route('admin.books.store'), $this->bookPayload('Original write', 'race'))->assertRedirect();

        $this->assertTrue($injected);
        $this->assertDatabaseHas('books', ['title' => 'Original write', 'slug' => 'race-2']);
    }

    /** @return array<string, mixed> */
    private function bookPayload(string $title, string $slug): array
    {
        return ['title' => $title, 'slug' => $slug, 'status' => 'available', 'description' => 'Description'];
    }

    /** @return array<string, mixed> */
    private function bookAttributes(string $title, string $slug): array
    {
        return [...$this->bookPayload($title, $slug), 'reviews' => []];
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }
}
