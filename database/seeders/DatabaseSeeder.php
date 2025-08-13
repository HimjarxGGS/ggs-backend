<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test Admin',
            'username' => 'bebek',
            'password' => Hash::make('bebek'),
            'role' => 'admin'
        ]);

        User::factory()->create([
            'name' => 'Test Member',
            'username' => 'ayam',
            'password' => Hash::make('ayam'),
            'role' => 'member'
        ]);

        Event::factory(10)->create();

        $this->call([
            UserSeeder::class,
            MemberSeeder::class,
        ]);

        $pendaftar = \App\Models\Pendaftar::inRandomOrder()->first();
        
        if ($pendaftar) {
            $eventsToAttach = \App\Models\Event::inRandomOrder()->take(3)->pluck('id');
            
            $pendaftar->events()->attach($eventsToAttach, [
                'status' => 'Finished', 
                'created_at' => now(), 
                'updated_at' => now()
            ]);
        }
    }
}
