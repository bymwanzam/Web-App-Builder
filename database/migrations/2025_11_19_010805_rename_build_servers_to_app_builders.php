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
        // Rename the build_servers table to app_builders
        Schema::rename('build_servers', 'app_builders');

        // Rename the build_server_id column in app_builds table
        Schema::table('app_builds', function (Blueprint $table) {
            $table->renameColumn('build_server_id', 'app_builder_id');
        });

        // Rename the allowed_build_servers column in plans table
        Schema::table('plans', function (Blueprint $table) {
            $table->renameColumn('allowed_build_servers', 'allowed_builders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename the app_builders table back to build_servers
        Schema::rename('app_builders', 'build_servers');

        // Rename the app_builder_id column back to build_server_id
        Schema::table('app_builds', function (Blueprint $table) {
            $table->renameColumn('app_builder_id', 'build_server_id');
        });

        // Rename the allowed_builders column back to allowed_build_servers
        Schema::table('plans', function (Blueprint $table) {
            $table->renameColumn('allowed_builders', 'allowed_build_servers');
        });
    }
};
