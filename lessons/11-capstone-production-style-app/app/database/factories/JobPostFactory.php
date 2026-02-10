<?php

namespace Database\Factories;

use App\Models\JobPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobPost>
 */
class JobPostFactory extends Factory
{
    protected $model = JobPost::class;

    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'department' => fake()->randomElement(['Engineering', 'Product', 'Operations', 'Design']),
            'location' => fake()->city(),
            'employment_type' => fake()->randomElement(['full_time', 'contract']),
            'status' => fake()->randomElement(['draft', 'open', 'closed']),
            'is_remote' => fake()->boolean(),
            'salary_min' => fake()->numberBetween(80000, 140000),
            'salary_max' => fake()->numberBetween(140001, 220000),
            'description' => fake()->paragraphs(2, true),
            'published_at' => now(),
        ];
    }
}
