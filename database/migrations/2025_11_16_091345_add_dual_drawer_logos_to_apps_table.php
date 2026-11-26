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
            // Add dual logo fields for light and dark mode
            $table->string('drawer_logo_light')
                ->nullable()
                ->after('drawer_logo');
            $table->string('drawer_logo_dark')
                ->nullable()
                ->after('drawer_logo_light');
        });

        // Migrate existing drawer_logo data to both new fields for backward compatibility
        DB::table('apps')
            ->whereNotNull('drawer_logo')
            ->where('drawer_logo', '!=', '')
            ->update([
                'drawer_logo_light' => DB::raw('drawer_logo'),
                'drawer_logo_dark' => DB::raw('drawer_logo'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn(['drawer_logo_light', 'drawer_logo_dark']);
        });
    }
};
