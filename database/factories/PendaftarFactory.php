<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pendaftar>
 */
class PendaftarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_lengkap' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'date_of_birth' => fake()->date(),
            'asal_instansi' => fake('id_ID')->company(),
            'no_telepon' => fake('id_ID')->phoneNumber(),
            'riwayat_penyakit' => 'Tidak ada',
        ];
    }
}