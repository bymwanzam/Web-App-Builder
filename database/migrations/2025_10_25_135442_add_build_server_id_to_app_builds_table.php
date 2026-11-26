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
        Schema::table('app_builds', function (Blueprint $table) {
            $table->foreignId('build_server_id')->nullable()->after('app_id')->constrained('build_servers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_builds', function (Blueprint $table) {
            $table->dropForeign(['build_server_id']);
            $table->dropColumn('build_server_id');
        });
    }
};
