<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            // Drawer mode and appearance
            $table->enum('drawer_mode', ['default', 'color', 'image'])
                ->default('default')
                ->after('active_tab_color');
            $table->string('drawer_background_color', 7)
                ->nullable()
                ->after('drawer_mode');
            $table->string('drawer_background_image')
                ->nullable()
                ->after('drawer_background_color');

            // Drawer logo settings
            $table->boolean('drawer_logo_enabled')
                ->default(false)
                ->after('drawer_background_image');
            $table->string('drawer_logo')
                ->nullable()
                ->after('drawer_logo_enabled');
            $table->string('drawer_title')
                ->nullable()
                ->after('drawer_logo');
            $table->string('drawer_subtitle')
                ->nullable()
                ->after('drawer_title');

            // Drawer text color
            $table->enum('drawer_text_theme', ['light', 'dark'])
                ->default('dark')
                ->after('drawer_subtitle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn([
                'drawer_mode',
                'drawer_background_color',
                'drawer_background_image',
                'drawer_logo_enabled',
                'drawer_logo',
                'drawer_title',
                'drawer_subtitle',
                'drawer_text_theme',
            ]);
        });
    }
};
