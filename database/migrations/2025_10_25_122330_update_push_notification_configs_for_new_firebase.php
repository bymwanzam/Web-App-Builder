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
        Schema::table('push_notification_configs', function (Blueprint $table) {
            // Add new column for Firebase service account JSON
            $table->text('firebase_credentials_file')->nullable()->after('google_services_file');

            // Drop deprecated columns
            $table->dropColumn(['firebase_server_key', 'firebase_sender_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('push_notification_configs', function (Blueprint $table) {
            // Restore deprecated columns
            $table->string('firebase_server_key')->nullable()->after('google_services_file');
            $table->string('firebase_sender_id')->nullable()->after('firebase_server_key');

            // Drop new column
            $table->dropColumn('firebase_credentials_file');
        });
    }
};
