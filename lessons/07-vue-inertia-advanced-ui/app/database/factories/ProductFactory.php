<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'sku' => strtoupper(fake()->bothify('SKU-#####')),
            'description' => fake()->sentence(12),
            'status' => fake()->randomElement(['draft', 'published', 'archived']),
            'price' => fake()->randomFloat(2, 9, 499),
            'stock' => fake()->numberBetween(0, 120),
            'is_featured' => fake()->boolean(20),
        ];
    }
}
