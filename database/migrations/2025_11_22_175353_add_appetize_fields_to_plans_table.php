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
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('can_use_appetize')
                ->default(false)
                ->after('can_use_custom_code')
                ->comment('Whether plan allows Appetize.io previews');

            $table->integer('monthly_appetize_minutes')
                ->nullable()
                ->after('can_use_appetize')
                ->comment('-1 for unlimited, null for disabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'can_use_appetize',
                'monthly_appetize_minutes',
            ]);
        });
    }
};
