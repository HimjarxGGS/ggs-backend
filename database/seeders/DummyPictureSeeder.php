<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pendaftar; 

class DummyPictureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $dummyPath = 'images/dummy.png';
        Pendaftar::whereNull('registrant_picture')
            ->orWhere('registrant_picture', '')
            ->update(['registrant_picture' => $dummyPath]);
    }
}
