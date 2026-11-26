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
            $table->boolean('can_use_hosted_pages')->default(false)->after('can_use_api');
            $table->integer('max_hosted_pages_per_app')->nullable()->after('can_use_hosted_pages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['can_use_hosted_pages', 'max_hosted_pages_per_app']);
        });
    }
};
