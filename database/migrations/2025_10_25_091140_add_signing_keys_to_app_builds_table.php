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
            $table->foreignId('keystore_id')->nullable()->after('build_format')->constrained('keystores')->onDelete('set null');
            $table->foreignId('certificate_id')->nullable()->after('keystore_id')->constrained('certificates')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_builds', function (Blueprint $table) {
            $table->dropForeign(['keystore_id']);
            $table->dropForeign(['certificate_id']);
            $table->dropColumn(['keystore_id', 'certificate_id']);
        });
    }
};
