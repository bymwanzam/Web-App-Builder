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
        // First, ensure all remaining apps have their drawer_logo migrated to new fields
        DB::table('apps')
            ->whereNotNull('drawer_logo')
            ->where('drawer_logo', '!=', '')
            ->where(function ($query) {
                $query->whereNull('drawer_logo_light')
                    ->orWhereNull('drawer_logo_dark');
            })
            ->update([
                'drawer_logo_light' => DB::raw('drawer_logo'),
                'drawer_logo_dark' => DB::raw('drawer_logo'),
            ]);

        // Now drop the old drawer_logo column
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn('drawer_logo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->string('drawer_logo')->nullable()->after('drawer_logo_enabled');
        });

        // Restore data from drawer_logo_light (prefer light mode logo)
        DB::table('apps')
            ->whereNotNull('drawer_logo_light')
            ->where('drawer_logo_light', '!=', '')
            ->update([
                'drawer_logo' => DB::raw('drawer_logo_light'),
            ]);
    }
};
