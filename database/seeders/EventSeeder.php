<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            'Greenovation Batch 1',
            'Greenectivity',
            'Greenovation Batch 2',
            'TEM Chapter 1',
            'Trashformers Batch 2',
            'Greenfest',
            'Trashformers Batch 1',
            'GGBeraksi Batch 2',
            'GGBeraksi Batch 1',
            'Webinar Daur Ulang',
        ];

        foreach ($events as $eventName) {
            Event::firstOrCreate(
                ['name' => $eventName],
                [
                    'description' => fake()->paragraph(2),
                    'event_date' => Carbon::now()->subMonths(rand(1, 12))->addDays(rand(1, 30)),
                    'status' => fake()->randomElement(['Active', 'Finished']),
                    'event_format' => fake()->randomElement(['online', 'offline']),
                    'location' => 'Surabaya',
                    'poster' => 'images/dummy.png', 
                    // 'need_registrant_picture' => fake()->boolean(),
                    'need_registrant_picture' => fake()->randomElement(['ya', 'tidak']),
                ]
            );
        }
    }
}