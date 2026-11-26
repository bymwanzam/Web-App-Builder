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
            // Add index for cleanup query performance
            $table->index('appetize_uploaded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_builds', function (Blueprint $table) {
            $table->dropIndex(['appetize_uploaded_at']);
        });
    }
};
