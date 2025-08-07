<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test Admin', 'username' => 'bebek',
            'password' => Hash::make('bebek'), 'role' => 'admin'
        ]);
        User::factory()->create([
            'name' => 'Test Member', 'username' => 'ayam',
            'password' => Hash::make('ayam'), 'role' => 'member'
        ]);
    }
}