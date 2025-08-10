<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pendaftar;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "Member {$i}",
                'username' => "member{$i}",
                'email' => "member{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => 'member',
            ]);

            Pendaftar::create([
                'user_id' => $user->id,
                'nama_lengkap' => "Nama Lengkap {$i}",
                'no_telepon' => '08' . rand(1000000000, 9999999999),
                'asal_instansi' => "Instansi {$i}",
                'date_of_birth' => now()->subYears(rand(18, 40))->format('Y-m-d'),
                'riwayat_penyakit' => ['-', 'Asma', 'Diabetes'][rand(0, 2)],
            ]);
        }
    }
}
