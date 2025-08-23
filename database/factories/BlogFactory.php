<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence();
        return [
            'title' => $title,
            'content'=> fake()->randomHtml(20),
            'slug' => Str::slug($title),
            'tag'=> ['ad', 'bebek', 'quuack quack'],
            'author' => fake()->name(),
            'pic'=> 1,
        ];
    }
}
