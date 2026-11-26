<?php

namespace Database\Factories;

use App\Models\App;
use App\Models\PushNotificationConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PushNotificationConfig>
 */
class PushNotificationConfigFactory extends Factory
{
    protected $model = PushNotificationConfig::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'app_id' => App::factory(),
            'provider' => 'fcm',
            'google_services_file' => null,
            'firebase_credentials_file' => null,
            'enabled' => true,
        ];
    }

    /**
     * Set a specific app for the config.
     */
    public function forApp(App $app): static
    {
        return $this->state(fn (array $attributes) => [
            'app_id' => $app->id,
        ]);
    }

    /**
     * Indicate that notifications are disabled.
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'enabled' => false,
        ]);
    }
}
