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
            $table->integer('drawer_logo_size')->nullable()->default(80)->after('drawer_logo_dark');
            $table->integer('splashscreen_logo_size')->nullable()->default(120)->after('splashscreen_logo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn(['drawer_logo_size', 'splashscreen_logo_size']);
        });
    }
};
