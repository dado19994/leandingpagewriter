<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call(WebsiteSeeder::class);

        User::factory()->create([
            'name' => 'Virginia Admin',
            'email' => env('WRITER_ADMIN_EMAIL', 'admin@example.com'),
            'password' => env('WRITER_ADMIN_PASSWORD', 'admin'),
            'is_admin' => true,
        ]);
    }
}
