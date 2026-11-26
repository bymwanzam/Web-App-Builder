<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Free', 'Pro', 'Enterprise']),
            'price' => fake()->randomFloat(2, 0, 99.99),
            'currency' => 'USD',
            'interval_unit' => 'month',
            'interval_count' => 1,
            'status' => 'active',
            'is_default' => false,
            'features' => [],
            'monthly_build_credits' => fake()->numberBetween(10, 100),
            'can_use_custom_code' => false,
            'can_use_appetize' => false,
            'monthly_appetize_minutes' => 0,
            'allowed_builders' => null, // null means all active builders
        ];
    }
}
