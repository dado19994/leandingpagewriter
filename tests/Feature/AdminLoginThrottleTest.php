<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminLoginThrottleTest extends TestCase
{
    use RefreshDatabase;

    public function test_repeated_failed_admin_logins_are_rate_limited(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);
        $key = $this->throttleKey($admin->email);
        RateLimiter::clear($key);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $response = $this->post(route('admin.login.store'), [
                'email' => $admin->email,
                'password' => 'incorrect-password',
            ]);

            $response->assertSessionHasErrors('email');
        }

        $blockedResponse = $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $blockedResponse->assertSessionHasErrors('email');
        $this->assertStringContainsString('Troppi tentativi', session('errors')->first('email'));
        $this->assertGuest();
        $this->assertSame(5, RateLimiter::attempts($key));
    }

    public function test_successful_admin_login_clears_failed_attempts(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);
        $key = $this->throttleKey($admin->email);
        RateLimiter::clear($key);

        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $this->post(route('admin.login.store'), [
                'email' => $admin->email,
                'password' => 'incorrect-password',
            ]);
        }

        $response = $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->assertSame(0, RateLimiter::attempts($key));
    }

    public function test_non_admin_login_attempts_count_toward_the_limit(): void
    {
        $user = User::factory()->create([
            'email' => 'reader@example.com',
            'is_admin' => false,
        ]);
        $key = $this->throttleKey($user->email);
        RateLimiter::clear($key);

        $response = $this->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertSame(1, RateLimiter::attempts($key));
    }

    private function throttleKey(string $email): string
    {
        return Str::transliterate(Str::lower($email)).'|127.0.0.1';
    }
}
