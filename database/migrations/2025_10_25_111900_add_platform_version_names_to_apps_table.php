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
            $table->string('android_version_name', 20)->nullable()->default('1.0.0')->after('version_name');
            $table->string('ios_version_name', 20)->nullable()->default('1.0.0')->after('android_version_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn(['android_version_name', 'ios_version_name']);
        });
    }
};
