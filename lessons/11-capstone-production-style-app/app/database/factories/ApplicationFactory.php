<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\JobPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        return [
            'job_post_id' => JobPost::factory(),
            'candidate_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'source' => fake()->randomElement(['career_site', 'referral', 'agency', 'linkedin']),
            'stage' => fake()->randomElement([
                Application::STAGE_APPLIED,
                Application::STAGE_SCREENING,
                Application::STAGE_INTERVIEW,
                Application::STAGE_OFFER,
            ]),
            'years_experience' => fake()->numberBetween(0, 12),
            'fit_score' => fake()->numberBetween(45, 95),
            'cover_letter' => fake()->optional()->paragraph(),
            'resume_text' => fake()->paragraphs(3, true),
            'applied_at' => now()->subDays(fake()->numberBetween(0, 20)),
        ];
    }
}
