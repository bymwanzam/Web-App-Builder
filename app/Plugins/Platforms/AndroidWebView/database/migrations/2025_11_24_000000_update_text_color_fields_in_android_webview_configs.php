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
        Schema::table('android_webview_configs', function (Blueprint $table) {
            // Add new text color fields for light and dark modes
            $table->string('text_color_light', 7)->default('#000000')->after('theme_color');
            $table->string('text_color_dark', 7)->default('#FFFFFF')->after('text_color_light');
        });

        // Migrate existing title_color enum values to new hex color fields
        $configs = DB::table('android_webview_configs')->get();
        foreach ($configs as $config) {
            $titleColor = $config->title_color ?? 'automatic';

            switch ($titleColor) {
                case 'light':
                    // Light text for dark backgrounds
                    DB::table('android_webview_configs')
                        ->where('id', $config->id)
                        ->update([
                            'text_color_light' => '#FFFFFF',
                            'text_color_dark' => '#FFFFFF',
                        ]);
                    break;

                case 'dark':
                    // Dark text for light backgrounds
                    DB::table('android_webview_configs')
                        ->where('id', $config->id)
                        ->update([
                            'text_color_light' => '#000000',
                            'text_color_dark' => '#000000',
                        ]);
                    break;

                default: // 'automatic'
                    // Use contrast-aware defaults
                    DB::table('android_webview_configs')
                        ->where('id', $config->id)
                        ->update([
                            'text_color_light' => '#000000', // Dark text for light mode
                            'text_color_dark' => '#FFFFFF',  // Light text for dark mode
                        ]);
                    break;
            }
        }

        // Drop the old title_color column after migrating data
        Schema::table('android_webview_configs', function (Blueprint $table) {
            $table->dropColumn('title_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('android_webview_configs', function (Blueprint $table) {
            // Restore the old title_color column
            $table->enum('title_color', ['automatic', 'light', 'dark'])->default('automatic')->after('theme_color');
        });

        // Drop the new text color fields
        Schema::table('android_webview_configs', function (Blueprint $table) {
            $table->dropColumn(['text_color_light', 'text_color_dark']);
        });
    }
};
