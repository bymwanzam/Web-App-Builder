<?php

namespace Database\Factories;

use App\Models\Plugin;
use Illuminate\Database\Eloquent\Factories\Factory;

class PluginFactory extends Factory
{
    protected $model = Plugin::class;

    public function definition(): array
    {
        $types = ['platform', 'payment_gateway', 'bridge'];
        $type = $this->faker->randomElement($types);
        $name = $this->faker->words(2, true);
        $slug = str_replace(' ', '-', strtolower($name));

        return [
            'name' => ucwords($name),
            'slug' => $slug,
            'type' => $type,
            'class' => 'App\\Plugins\\'.ucfirst($type).'\\'.str_replace(' ', '', ucwords($name)).'\\TestPlugin',
            'version' => $this->faker->semver(),
            'status' => 'active',
            'config' => [],
            'migrations' => [],
            'metadata' => [],
            'installed_at' => now(),
        ];
    }

    public function platform(): self
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'platform',
        ]);
    }

    public function paymentGateway(): self
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'payment_gateway',
        ]);
    }

    public function inactive(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
