<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPostSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_generates_normalizes_and_accepts_custom_post_slugs(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('admin.posts.store'), $this->postPayload('L’estate è qui', ''))->assertRedirect();
        $this->post(route('admin.posts.store'), $this->postPayload('Custom', '  Una Slug Speciale!  '))->assertRedirect();

        $this->assertDatabaseHas('posts', ['title' => 'L’estate è qui', 'slug' => 'lestate-e-qui']);
        $this->assertDatabaseHas('posts', ['title' => 'Custom', 'slug' => 'una-slug-speciale']);
    }

    public function test_create_allocates_suffixes_across_repeated_post_collisions(): void
    {
        $this->actingAs($this->admin());

        foreach (['One', 'Two', 'Three'] as $title) {
            $this->post(route('admin.posts.store'), $this->postPayload($title, 'same'))->assertRedirect();
        }

        $this->assertSame(['same', 'same-2', 'same-3'], Post::orderBy('id')->pluck('slug')->all());
    }

    public function test_update_preserves_its_slug_and_resolves_a_post_collision(): void
    {
        $this->actingAs($this->admin());
        $first = Post::create($this->postAttributes('First', 'first'));
        $second = Post::create($this->postAttributes('Second', 'second'));

        $this->put(route('admin.posts.update', $first), $this->postPayload('First revised', 'first'))->assertRedirect();
        $this->put(route('admin.posts.update', $second), $this->postPayload('Second revised', 'first'))->assertRedirect();

        $this->assertSame('first', $first->fresh()->slug);
        $this->assertSame('first-2', $second->fresh()->slug);
    }

    public function test_update_regenerates_a_normalized_post_slug_when_left_empty(): void
    {
        $this->actingAs($this->admin());
        $post = Post::create($this->postAttributes('Old title', 'old-title'));

        $this->put(route('admin.posts.update', $post), $this->postPayload('Nuovo titolo è qui', ''))->assertRedirect();

        $this->assertSame('nuovo-titolo-e-qui', $post->fresh()->slug);
    }

    public function test_create_recovers_from_a_write_time_post_slug_race(): void
    {
        $this->actingAs($this->admin());
        $injected = false;

        Post::creating(function (Post $post) use (&$injected): void {
            if ($injected || $post->slug !== 'race') {
                return;
            }

            $injected = true;
            Post::withoutEvents(fn () => Post::create($this->postAttributes('Competing write', 'race')));
        });

        $this->post(route('admin.posts.store'), $this->postPayload('Original write', 'race'))->assertRedirect();

        $this->assertTrue($injected);
        $this->assertDatabaseHas('posts', ['title' => 'Original write', 'slug' => 'race-2']);
    }

    /** @return array<string, mixed> */
    private function postPayload(string $title, string $slug): array
    {
        return ['title' => $title, 'slug' => $slug, 'excerpt' => 'Excerpt', 'body' => 'Body', 'is_published' => '1'];
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
