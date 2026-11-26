<?php

namespace Database\Factories;

use App\Models\AppBuilder;
use App\Services\PluginManager;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppBuilder>
 */
class AppBuilderFactory extends Factory
{
    protected $model = AppBuilder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Default to first active platform, or fallback to android-webview
        $pluginManager = app(PluginManager::class);
        $firstPlatform = $pluginManager->getActivePlatforms()->first();
        $defaultPlatform = $firstPlatform ? $firstPlatform->getPlatformId() : 'android-webview';

        return [
            'name' => fake()->words(2, true).' Builder',
            'server_key' => Str::random(32),
            'url' => 'http://localhost',
            'port' => fake()->numberBetween(8000, 9000),
            'platforms' => [$defaultPlatform],
            'status' => 'active',
            'max_queue' => 5,
            'current_builds_count' => 0,
            'total_builds_processed' => 0,
        ];
    }

    /**
     * Indicate that the server is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the server handles AndroidWebView builds.
     */
    public function androidWebview(): static
    {
        return $this->state(fn (array $attributes) => [
            'platforms' => ['android-webview'],
        ]);
    }

    /**
     * Indicate that the server handles all active platforms.
     */
    public function allPlatforms(): static
    {
        return $this->state(function (array $attributes) {
            $pluginManager = app(PluginManager::class);

            return [
                'platforms' => $pluginManager->getActivePlatforms()
                    ->map(fn ($p) => $p->getPlatformId())
                    ->values()
                    ->toArray(),
            ];
        });
    }

    /**
     * Indicate that the server handles a specific platform.
     */
    public function forPlatform(string $platform): static
    {
        return $this->state(fn (array $attributes) => [
            'platforms' => [$platform],
        ]);
    }
}
