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
        Schema::table('plans', function (Blueprint $table) {
            $table->json('allowed_build_servers')->nullable()->after('can_use_custom_code');
        });

        // Backfill existing plans with currently active server IDs
        $activeServerIds = DB::table('build_servers')
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();

        if (count($activeServerIds) > 0) {
            DB::table('plans')->update([
                'allowed_build_servers' => json_encode($activeServerIds),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('allowed_build_servers');
        });
    }
};
