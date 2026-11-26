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
            // Background type selector
            $table->enum('splashscreen_background_type', ['color', 'image'])
                ->default('color')
                ->after('icon_extracted_path');

            // Logo settings
            $table->boolean('splashscreen_logo_enabled')
                ->default(false)
                ->after('splashscreen_animation');
            $table->string('splashscreen_logo')
                ->nullable()
                ->after('splashscreen_logo_enabled');
            $table->string('splashscreen_title')
                ->nullable()
                ->after('splashscreen_logo');
            $table->string('splashscreen_subtitle')
                ->nullable()
                ->after('splashscreen_title');

            // Text theme
            $table->enum('splashscreen_text_theme', ['light', 'dark'])
                ->default('dark')
                ->after('splashscreen_subtitle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn([
                'splashscreen_background_type',
                'splashscreen_logo_enabled',
                'splashscreen_logo',
                'splashscreen_title',
                'splashscreen_subtitle',
                'splashscreen_text_theme',
            ]);
        });
    }
};
