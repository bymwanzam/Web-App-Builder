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
        // First, change layout_template to string to allow updating values
        Schema::table('apps', function (Blueprint $table) {
            $table->string('layout_template')->nullable()->change();
        });

        // Update existing layout_template values to new enum values
        DB::table('apps')->where('layout_template', 'drawer')->update(['layout_template' => 'app_bar_drawer']);
        DB::table('apps')->where('layout_template', 'tabs')->update(['layout_template' => 'app_bar_tabs']);
        DB::table('apps')->where('layout_template', 'bar')->update(['layout_template' => 'app_bar']);
        // 'blank' stays the same

        Schema::table('apps', function (Blueprint $table) {
            // Progress indicator settings
            $table->enum('progress_indicator', ['disable', 'linear', 'circular'])
                ->default('linear')
                ->after('accent_color');
            $table->string('progress_indicator_color', 7)
                ->nullable()
                ->after('progress_indicator');

            // App bar settings
            $table->boolean('show_app_bar_title')
                ->default(true)
                ->after('progress_indicator_color');

            // Navigation tab colors
            $table->string('nav_tab_icon_color', 7)
                ->nullable()
                ->after('show_app_bar_title');
            $table->string('active_tab_color', 7)
                ->nullable()
                ->after('nav_tab_icon_color');

            // Update layout_template to use new enum values
            $table->enum('layout_template', ['app_bar_drawer', 'app_bar_tabs', 'app_bar', 'blank'])
                ->default('app_bar_drawer')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn([
                'progress_indicator',
                'progress_indicator_color',
                'show_app_bar_title',
                'nav_tab_icon_color',
                'active_tab_color',
            ]);

            // Revert layout_template (if needed)
            $table->string('layout_template')->nullable()->change();
        });
    }
};
