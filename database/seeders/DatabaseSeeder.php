<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        User::factory()->create([
            'name' => 'Test Admin',
            'username' => 'bebek',
            'password' => 'bebek',
            'role' => 'admin'
        ]);

        User::factory()->create([
            'name' => 'Test Member',
            'username' => 'ayam',
            'password' => 'ayam',
            'role' => 'member'
        ]);

        // Event::fact
    }
}
