<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserConsent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserConsent>
 */
class UserConsentFactory extends Factory
{
    protected $model = UserConsent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'consent_type' => fake()->randomElement(['marketing', 'third_party', 'ai_services', 'privacy_policy', 'terms']),
            'consented' => fake()->boolean(),
            'version' => fake()->semver(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'consented_at' => fake()->boolean(70) ? now() : null,
            'withdrawn_at' => null,
        ];
    }

    public function consented(): static
    {
        return $this->state(fn (array $attributes) => [
            'consented' => true,
            'consented_at' => now(),
            'withdrawn_at' => null,
        ]);
    }

    public function withdrawn(): static
    {
        return $this->state(fn (array $attributes) => [
            'consented' => false,
            'consented_at' => null,
            'withdrawn_at' => now(),
        ]);
    }
}
