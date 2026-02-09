<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'author_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999),
            'excerpt' => fake()->sentence(12),
            'content' => fake()->paragraphs(4, true),
            'status' => fake()->randomElement(['draft', 'review', 'published']),
            'published_at' => fake()->optional(0.45)->dateTimeBetween('-2 months', 'now'),
            'view_count' => fake()->numberBetween(0, 4000),
            'is_featured' => fake()->boolean(20),
        ];
    }
}
