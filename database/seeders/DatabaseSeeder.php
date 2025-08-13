<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MemberSeeder::class,
            EventSeeder::class,
        ]);

        $members = \App\Models\User::where('role', 'member')->get();
        $events = \App\Models\Event::all();
        
        if ($events->isEmpty()) {
            return;
        }

        foreach ($members as $member) {
            // check member memiliki detail pendaftar
            if ($member->pendaftar) {
                $randomNumber = rand(1, 10);

                // Skenario 1: Tidak ikut event sama sekali (30% kemungkinan)
                if ($randomNumber <= 3) {
                    continue; // Lanjut ke member berikutnya
                }
                
                // Skenario 2: Ikut 1 event (30% kemungkinan)
                elseif ($randomNumber <= 6) {
                    $randomEvent = $events->random(1)->pluck('id');
                    $member->pendaftar->events()->attach($randomEvent, ['status' => 'Finished']);
                }
                
                // Skenario 3: Ikut beberapa event (40% kemungkinan)
                else {
                    $eventCount = rand(2, 5);
                    if ($eventCount > $events->count()) {
                        $eventCount = $events->count();
                    }
                    $randomEvents = $events->random($eventCount)->pluck('id');
                    $member->pendaftar->events()->attach($randomEvents, ['status' => 'Finished']);
                }
            }
        }
    }
}