<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default role (system, cannot be modified)
        Role::create([
            'name' => 'Default',
            'description' => 'Basic user role assigned to all new users automatically',
            'type' => 'system',
            'badge_type' => 'system',
        ]);

        // Administrator role
        Role::create([
            'name' => 'Administrator',
            'description' => 'Complete control over all platform features and settings',
            'type' => 'custom',
            'badge_type' => 'full_access',
        ]);

        // Developer role
        Role::create([
            'name' => 'Developer',
            'description' => 'Can create and manage apps but not users or billing',
            'type' => 'custom',
            'badge_type' => 'standard',
        ]);
    }
}
