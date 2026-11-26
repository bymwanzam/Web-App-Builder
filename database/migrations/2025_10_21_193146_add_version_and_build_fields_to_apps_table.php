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
            // Version management
            $table->string('version_name', 20)->default('1.0.0')->after('platforms');
            $table->integer('version_code')->default(1)->after('version_name');

            // Bundle identifiers
            $table->string('android_package_name')->nullable()->after('app_id');
            $table->string('ios_bundle_id')->nullable()->after('android_package_name');

            // Build configuration
            $table->json('build_config')->nullable()->after('version_code');
            $table->json('environment_variables')->nullable()->after('build_config');

            // Signing references
            $table->foreignId('android_keystore_id')->nullable()->constrained('keystores')->onDelete('set null')->after('environment_variables');
            $table->foreignId('ios_certificate_id')->nullable()->constrained('certificates')->onDelete('set null')->after('android_keystore_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropForeign(['android_keystore_id']);
            $table->dropForeign(['ios_certificate_id']);
            $table->dropColumn([
                'version_name',
                'version_code',
                'android_package_name',
                'ios_bundle_id',
                'build_config',
                'environment_variables',
                'android_keystore_id',
                'ios_certificate_id',
            ]);
        });
    }
};
