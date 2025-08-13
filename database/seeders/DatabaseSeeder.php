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

        // ...
        // --- LOGIKA DISTRIBUSI RIWAYAT EVENT ---

        // 1. Ambil semua member dan event yang ada
        $members = \App\Models\User::where('role', 'member')->get();
        $events = \App\Models\Event::all();

        if ($events->isEmpty()) {
            return;
        }

        $validHistoryStatuses = ['Active', 'Finished'];

        foreach ($members as $member) {
            if ($member->pendaftar) {
                $randomNumber = rand(1, 10);

                if ($randomNumber <= 3) {
                    continue;
                } elseif ($randomNumber <= 6) {
                    $randomEvent = $events->random(1)->pluck('id');
                    $randomStatus = $validHistoryStatuses[array_rand($validHistoryStatuses)];
                    $member->pendaftar->events()->attach($randomEvent, ['status' => $randomStatus]);
                } else {
                    $eventCount = rand(2, 5);
                    if ($eventCount > $events->count()) {
                        $eventCount = $events->count();
                    }
                    $randomEvents = $events->random($eventCount)->pluck('id');
                    $randomStatus = $validHistoryStatuses[array_rand($validHistoryStatuses)];
                    $member->pendaftar->events()->attach($randomEvents, ['status' => $randomStatus]);
                }
            }
        }
    }
}
