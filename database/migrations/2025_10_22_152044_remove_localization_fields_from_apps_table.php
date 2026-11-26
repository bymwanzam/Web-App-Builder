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
            // Remove localization-related fields
            $table->dropColumn([
                'supported_languages',
                'rtl_support',
                'date_format',
                'time_format',
                'currency_format',
                'translations',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            // Restore localization fields
            $table->json('supported_languages')->nullable();
            $table->boolean('rtl_support')->default(false);
            $table->string('date_format')->default('MM/DD/YYYY');
            $table->enum('time_format', ['12h', '24h'])->default('12h');
            $table->string('currency_format')->default('USD');
            $table->json('translations')->nullable();
        });
    }
};
