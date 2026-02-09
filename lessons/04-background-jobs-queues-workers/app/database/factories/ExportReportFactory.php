<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExportReport>
 */
class ExportReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'topic' => fake()->randomElement([
                'weekly-product-metrics',
                'active-users-summary',
                'queue-performance-report',
            ]),
            'status' => 'queued',
            'attempt_count' => 0,
            'summary' => null,
            'error_message' => null,
            'processed_at' => null,
        ];
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => 'failed',
            'attempt_count' => 3,
            'error_message' => 'Simulated failure',
        ]);
    }
}
