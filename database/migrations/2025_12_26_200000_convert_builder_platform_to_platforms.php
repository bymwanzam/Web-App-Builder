<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add new platforms JSON column
        Schema::table('app_builders', function (Blueprint $table) {
            $table->json('platforms')->nullable()->after('platform');
        });

        // Get all available platform IDs dynamically from PluginManager
        $pluginManager = app(\App\Services\PluginManager::class);
        $allPlatformIds = $pluginManager->getActivePlatforms()
            ->map(fn ($p) => $p->getPlatformId())
            ->values()
            ->toArray();

        // Migrate existing data
        DB::table('app_builders')->get()->each(function ($builder) use ($allPlatformIds) {
            $platforms = [];
            if ($builder->platform === 'both') {
                // Convert 'both' to ALL currently active platforms (loaded dynamically)
                $platforms = $allPlatformIds;
            } elseif ($builder->platform) {
                $platforms = [$builder->platform];
            }
            DB::table('app_builders')
                ->where('id', $builder->id)
                ->update(['platforms' => json_encode($platforms)]);
        });

        // Drop old column
        Schema::table('app_builders', function (Blueprint $table) {
            $table->dropColumn('platform');
        });
    }

    public function down(): void
    {
        // Add back platform column
        Schema::table('app_builders', function (Blueprint $table) {
            $table->string('platform', 50)->default('android-webview')->after('max_queue');
        });

        // Migrate data back (use first platform or 'both' if multiple)
        DB::table('app_builders')->get()->each(function ($builder) {
            $platforms = json_decode($builder->platforms, true) ?? [];
            $platform = count($platforms) > 1 ? 'both' : ($platforms[0] ?? 'android-webview');
            DB::table('app_builders')
                ->where('id', $builder->id)
                ->update(['platform' => $platform]);
        });

        // Drop platforms column
        Schema::table('app_builders', function (Blueprint $table) {
            $table->dropColumn('platforms');
        });
    }
};
