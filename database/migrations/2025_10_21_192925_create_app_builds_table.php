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
        Schema::create('app_builds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained()->onDelete('cascade');
            $table->enum('platform', ['android', 'ios']);
            $table->enum('build_type', ['debug', 'release'])->default('release');
            $table->string('version_name', 20); // e.g., 1.0.0
            $table->integer('version_code'); // e.g., 1, 2, 3...
            $table->enum('status', [
                'pending',
                'preparing',
                'building_android',
                'building_ios',
                'processing',
                'completed',
                'failed',
                'cancelled',
            ])->default('pending');

            // Build artifacts
            $table->string('artifact_url', 500)->nullable();
            $table->bigInteger('artifact_size')->nullable(); // bytes
            $table->string('install_qr_code', 500)->nullable(); // QR code image URL

            // Build metadata
            $table->longText('build_logs')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('build_duration')->nullable(); // seconds

            // Capacitor config snapshot
            $table->json('capacitor_config')->nullable();

            // Timestamps
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // When build artifacts are deleted
            $table->timestamps();

            // Indexes
            $table->index(['app_id', 'platform']);
            $table->index('status');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_builds');
    }
};
