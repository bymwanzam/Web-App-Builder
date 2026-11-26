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
            $table->boolean('permission_location')->default(false)->after('custom_js');
            $table->boolean('permission_camera')->default(false)->after('permission_location');
            $table->boolean('permission_storage')->default(false)->after('permission_camera');
            $table->boolean('permission_record_audio')->default(false)->after('permission_storage');
            $table->boolean('permission_read_contacts')->default(false)->after('permission_record_audio');
            $table->boolean('permission_vibrate')->default(false)->after('permission_read_contacts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn([
                'permission_location',
                'permission_camera',
                'permission_storage',
                'permission_record_audio',
                'permission_read_contacts',
                'permission_vibrate',
            ]);
        });
    }
};
