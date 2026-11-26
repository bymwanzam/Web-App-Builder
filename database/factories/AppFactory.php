<?php

namespace Database\Factories;

use App\Models\App;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\App>
 */
class AppFactory extends Factory
{
    protected $model = App::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(2, true),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (App $app) {
            // Set the platform plugin ID to android-webview by default
            $platformPlugin = \App\Models\Plugin::where('type', 'platform')
                ->where('slug', 'android-webview')
                ->first();

            if ($platformPlugin && ! $app->platform_plugin_id) {
                $app->platform_plugin_id = $platformPlugin->id;
                $app->save();
            }

            // Create platform-specific config for androidwebview
            // Only if the table exists (it's a plugin migration, might not exist in tests)
            if ($platformPlugin && \Illuminate\Support\Facades\Schema::hasTable('android_webview_configs')) {
                $slug = strtolower($app->name);
                $slug = preg_replace('/[^a-z0-9]/', '', $slug);

                \App\Models\AndroidWebViewConfig::create([
                    'app_id' => $app->id,
                    'url' => fake()->url(),
                    'package_name' => 'com.'.$slug.'.app',
                    'version_name' => '1.0.0',
                    'version_code' => 1,
                    'theme_color' => '#3b82f6',
                    'title_color_scheme' => 'automatic',
                    'enable_dark_mode' => false,
                    'navigation_bar_type' => 'app_bar_drawer',
                    'bottom_bar_visibility' => 'visible',
                    'bottom_bar_items' => [],
                    'enable_drawer' => false,
                    'drawer_position' => 'left',
                    'drawer_items' => [],
                    'icon_path' => null,
                    'adaptive_icon_background_color' => '#FFFFFF',
                    'adaptive_icon_foreground_path' => null,
                    'adaptive_icon_background_path' => null,
                    'splash_background_type' => 'color',
                    'splash_background_color' => '#FFFFFF',
                    'splash_background_image_path' => null,
                    'splash_icon_type' => 'default',
                    'splash_icon_path' => null,
                    'splash_text' => null,
                    'custom_html_head' => null,
                    'custom_html_body' => null,
                    'custom_css' => null,
                    'custom_javascript' => null,
                    'permissions' => [],
                    'enable_javascript' => true,
                    'enable_zoom_controls' => false,
                    'user_agent_string' => null,
                    'cache_mode' => 'LOAD_DEFAULT',
                    'show_loading_indicator' => true,
                    'error_page_url' => null,
                ]);
            }
        });
    }

    /**
     * Set a specific user for the app.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Set app with navigation tabs layout.
     */
    public function withTabsLayout(): static
    {
        return $this->afterCreating(function (App $app) {
            $config = $app->androidWebViewConfig;
            if ($config) {
                $config->update([
                    'navigation_bar_type' => 'app_bar_tabs',
                ]);
            }
        });
    }

    /**
     * Set app with drawer layout.
     */
    public function withDrawerLayout(): static
    {
        return $this->afterCreating(function (App $app) {
            $config = $app->androidWebViewConfig;
            if ($config) {
                $config->update([
                    'navigation_bar_type' => 'app_bar_drawer',
                    'enable_drawer' => true,
                ]);
            }
        });
    }
}
