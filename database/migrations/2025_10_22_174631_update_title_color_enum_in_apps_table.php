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
        // Update existing data to valid values
        DB::table('apps')
            ->whereIn('title_color', ['white', 'black'])
            ->update(['title_color' => 'automatic']);

        // Modify column using Schema builder for database-agnostic approach
        Schema::table('apps', function (Blueprint $table) {
            $table->string('title_color', 20)->default('automatic')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->string('title_color', 20)->default('white')->change();
        });
    }
};
