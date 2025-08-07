<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat user admin 'bebek' jika belum ada
        User::firstOrCreate(
            ['username' => 'bebek'], 
            [                         
                'name' => 'Test Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('bebek'),
                'role' => 'admin'
            ]
        );

        User::firstOrCreate(
            ['username' => 'ayam'],
            [
                'name' => 'Test Member',
                'email' => 'member@example.com',
                'password' => Hash::make('ayam'),
                'role' => 'member'
            ]
        );

        User::factory(50)->create();
    }
}