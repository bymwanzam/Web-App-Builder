<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Free Plan - Access to builder
        Plan::updateOrCreate(
            ['name' => 'Free'],
            [
                'price' => 0.00,
                'status' => 'active',
                'is_default' => true,
                'monthly_build_credits' => 10, // 10 builds per month
                'can_use_custom_code' => false,
                'can_use_appetize' => false,
                'monthly_appetize_minutes' => null,
                'can_use_api' => false,
                'can_use_hosted_pages' => false,
                'max_hosted_pages_per_app' => null,
                'allowed_builders' => [1], // Android builder
                'can_use_ai_chat' => false,
                'monthly_ai_tokens' => 0,
                'allow_user_ai_api_key' => false,
                'features' => [
                    ['name' => '24/7 support', 'included' => false],
                ],
            ]
        );

        // Pro Plan - Access to builder
        Plan::updateOrCreate(
            ['name' => 'Pro'],
            [
                'price' => 29.00,
                'status' => 'active',
                'is_default' => false,
                'monthly_build_credits' => 50, // 50 builds per month
                'can_use_custom_code' => true,
                'can_use_appetize' => true,
                'monthly_appetize_minutes' => 300, // 5 hours
                'can_use_api' => true,
                'can_use_hosted_pages' => true,
                'max_hosted_pages_per_app' => 5, // 5 pages per app
                'allowed_builders' => [1], // Android builder
                'can_use_ai_chat' => true,
                'monthly_ai_tokens' => 500000, // 500K tokens/month
                'allow_user_ai_api_key' => true,
                'features' => [
                    ['name' => '24/7 support', 'included' => false],
                ],
            ]
        );

        // Enterprise Plan - Access to all active builders (unrestricted)
        Plan::updateOrCreate(
            ['name' => 'Enterprise'],
            [
                'price' => 99.00,
                'status' => 'active',
                'is_default' => false,
                'monthly_build_credits' => -1, // Unlimited builds
                'can_use_custom_code' => true,
                'can_use_appetize' => true,
                'monthly_appetize_minutes' => -1, // Unlimited
                'can_use_api' => true,
                'can_use_hosted_pages' => true,
                'max_hosted_pages_per_app' => null, // Unlimited pages
                'allowed_builders' => null, // All active builders
                'can_use_ai_chat' => true,
                'monthly_ai_tokens' => -1, // Unlimited
                'allow_user_ai_api_key' => true,
                'features' => [
                    ['name' => '24/7 support', 'included' => true],
                ],
            ]
        );
    }
}
