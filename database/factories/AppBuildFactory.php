<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppBuild>
 */
class AppBuildFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform' => 'android',
            'build_type' => fake()->randomElement(['debug', 'release']),
            'version_name' => '1.0.0',
            'version_code' => fake()->numberBetween(1, 100),
            'status' => 'pending',
            'expires_at' => now()->addDays(30),
        ];
    }
}
