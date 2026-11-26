<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Remove iOS support from the application.
     * This migration is fully reversible via the down() method.
     */
    public function up(): void
    {
        // 1. Drop foreign key constraints from apps table
        if (Schema::hasColumn('apps', 'ios_certificate_id')) {
            Schema::table('apps', function (Blueprint $table) {
                $table->dropForeign(['ios_certificate_id']);
                $table->dropForeign(['android_keystore_id']); // Temporarily drop to recreate
            });
        }

        // 2. Drop foreign key constraints from app_builds table
        if (Schema::hasColumn('app_builds', 'certificate_id')) {
            Schema::table('app_builds', function (Blueprint $table) {
                $table->dropForeign(['certificate_id']);
                $table->dropForeign(['keystore_id']); // Temporarily drop to recreate
            });
        }

        // 3. Remove iOS-related columns from apps table
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn([
                'ios_version_name',
                'ios_bundle_id',
                'ios_certificate_id',
            ]);
        });

        // 4. Remove certificate_id from app_builds table
        Schema::table('app_builds', function (Blueprint $table) {
            $table->dropColumn('certificate_id');
        });

        // 5. Re-add foreign keys that we dropped (Android only now)
        Schema::table('apps', function (Blueprint $table) {
            $table->foreign('android_keystore_id')
                ->references('id')
                ->on('keystores')
                ->onDelete('set null');
        });

        Schema::table('app_builds', function (Blueprint $table) {
            $table->foreign('keystore_id')
                ->references('id')
                ->on('keystores')
                ->onDelete('set null');
        });

        // 6. Drop certificates table entirely
        Schema::dropIfExists('certificates');

        // 7. Update app_builds to remove iOS-specific data
        // Remove any builds with platform = 'ios' or status = 'building_ios'
        DB::table('app_builds')->where('platform', 'ios')->delete();
        DB::table('app_builds')->where('status', 'building_ios')->update(['status' => 'failed']);
    }

    /**
     * Reverse the migrations - Restore iOS support capability.
     * This allows easy restoration of iOS functionality in the future.
     */
    public function down(): void
    {
        // 1. Recreate certificates table with full schema
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->string('team_id', 20)->nullable();
            $table->text('encrypted_certificate'); // P12 certificate encrypted
            $table->text('encrypted_certificate_password'); // Certificate password encrypted
            $table->text('encrypted_provisioning_profile')->nullable(); // Provisioning profile encrypted
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('expires_at');
        });

        // 2. Drop existing foreign keys to recreate with certificate support
        Schema::table('apps', function (Blueprint $table) {
            $table->dropForeign(['android_keystore_id']);
        });

        Schema::table('app_builds', function (Blueprint $table) {
            $table->dropForeign(['keystore_id']);
        });

        // 3. Add iOS columns back to apps table
        Schema::table('apps', function (Blueprint $table) {
            $table->string('ios_version_name', 20)->nullable()->default('1.0.0')->after('android_version_name');
            $table->string('ios_bundle_id')->nullable()->after('android_package_name');
            $table->foreignId('ios_certificate_id')
                ->nullable()
                ->after('android_keystore_id')
                ->constrained('certificates')
                ->onDelete('set null');
        });

        // 4. Add certificate_id back to app_builds table
        Schema::table('app_builds', function (Blueprint $table) {
            $table->foreignId('certificate_id')
                ->nullable()
                ->after('keystore_id')
                ->constrained('certificates')
                ->onDelete('set null');
        });

        // 5. Re-add foreign keys
        Schema::table('apps', function (Blueprint $table) {
            $table->foreign('android_keystore_id')
                ->references('id')
                ->on('keystores')
                ->onDelete('set null');
        });

        Schema::table('app_builds', function (Blueprint $table) {
            $table->foreign('keystore_id')
                ->references('id')
                ->on('keystores')
                ->onDelete('set null');
        });
    }
};
