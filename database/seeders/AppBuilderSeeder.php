<?php

namespace Database\Seeders;

use App\Models\AppBuilder;
use App\Services\PluginManager;
use Illuminate\Database\Seeder;

class AppBuilderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active platform IDs dynamically from PluginManager
        $pluginManager = app(PluginManager::class);
        $allPlatformIds = $pluginManager->getActivePlatforms()
            ->map(fn ($p) => $p->getPlatformId())
            ->values()
            ->toArray();

        AppBuilder::create([
            'name' => 'Android Builder',
            'url' => 'http://localhost',
            'port' => 8845,
            'server_key' => '123456',
            'max_queue' => 5,
            'platforms' => $allPlatformIds,
            'status' => 'active',
            'credit_cost' => 1,
        ]);
    }
}
