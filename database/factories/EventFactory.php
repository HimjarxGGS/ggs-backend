<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'name' => fake()->text(),
            'description' => fake()->text(),
            'event_date' => fake()->date(),
            'status' => 'active',
            'event_format' => 'online',
            'location' => fake()->text(),
            'poster' => fake()->text(),
        ];
    }
}
