<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drops legacy columns that are no longer used:
     * - platform: Replaced by platform_plugin_id foreign key
     * - status_bar_color: Moved to platform-specific config tables
     * - primary_color: Moved to platform-specific config tables
     * - accent_color: Moved to platform-specific config tables
     */
    public function up(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn([
                'platform',
                'status_bar_color',
                'primary_color',
                'accent_color',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->string('platform')->default('androidwebview')->after('platform_plugin_id');
            $table->string('status_bar_color')->nullable()->after('updated_at');
            $table->string('primary_color')->nullable()->after('status_bar_color');
            $table->string('accent_color')->nullable()->after('primary_color');
        });
    }
};
