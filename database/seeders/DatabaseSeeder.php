<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PlanSeeder::class,
            UserSeeder::class,
            PlatformPluginsSeeder::class,
            AppSeeder::class,
            SubscriptionSeeder::class,
            TransactionSeeder::class,
            LanguageSeeder::class,
            AppBuilderSeeder::class,
            SystemSettingSeeder::class,
            PaymentGatewayPluginsSeeder::class,
            EmailTemplateSeeder::class,
            DemoAiSettingsSeeder::class,
        ]);
    }
}
