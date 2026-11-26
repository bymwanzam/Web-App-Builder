<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            // Add app bar buttons
            $table->json('app_bar_buttons')
                ->nullable()
                ->after('swipe_navigation');
        });

        // Migrate existing navigation_items to new structure
        // Old: {label, url, icon, external}
        // New: {label, action_type, value, icon}
        DB::table('apps')->whereNotNull('navigation_items')->get()->each(function ($app) {
            $navigationItems = json_decode($app->navigation_items, true);

            if (is_array($navigationItems)) {
                $migratedItems = array_map(function ($item) {
                    $actionType = isset($item['external']) && $item['external']
                        ? 'external'
                        : 'internal';

                    return [
                        'label' => $item['label'] ?? '',
                        'action_type' => $actionType,
                        'value' => $item['url'] ?? '',
                        'icon' => $item['icon'] ?? 'home',
                    ];
                }, $navigationItems);

                DB::table('apps')
                    ->where('id', $app->id)
                    ->update(['navigation_items' => json_encode($migratedItems)]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn('app_bar_buttons');
        });

        // Revert navigation_items to old structure
        DB::table('apps')->whereNotNull('navigation_items')->get()->each(function ($app) {
            $navigationItems = json_decode($app->navigation_items, true);

            if (is_array($navigationItems)) {
                $revertedItems = array_map(function ($item) {
                    return [
                        'label' => $item['label'] ?? '',
                        'url' => $item['value'] ?? '',
                        'icon' => $item['icon'] ?? 'home',
                        'external' => ($item['action_type'] ?? 'internal') === 'external',
                    ];
                }, $navigationItems);

                DB::table('apps')
                    ->where('id', $app->id)
                    ->update(['navigation_items' => json_encode($revertedItems)]);
            }
        });
    }
};
