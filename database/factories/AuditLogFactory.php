<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'actor_id' => User::factory(),
            'action' => fake()->randomElement(['data_export', 'account_deletion', 'consent_change', 'admin_action', 'data_access']),
            'entity_type' => null,
            'entity_id' => null,
            'old_values' => null,
            'new_values' => null,
            'metadata' => null,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    public function forEntity(string $type, int $id): static
    {
        return $this->state(fn (array $attributes) => [
            'entity_type' => $type,
            'entity_id' => $id,
        ]);
    }

    public function withChanges(array $old, array $new): static
    {
        return $this->state(fn (array $attributes) => [
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }
}
