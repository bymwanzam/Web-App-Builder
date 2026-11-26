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
        Schema::create('cron_logs', function (Blueprint $table) {
            $table->id();
            $table->string('job_name');
            $table->string('job_class');
            $table->enum('status', ['success', 'failed', 'running'])->default('running');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration')->nullable()->comment('Duration in seconds');
            $table->string('triggered_by', 50)->default('cron')->comment('cron|manual|user:{id}');
            $table->json('servers_triggered')->nullable()->comment('Array of server IDs that were triggered');
            $table->integer('builds_processed')->nullable()->default(0);
            $table->text('message')->nullable();
            $table->text('exception')->nullable()->comment('Exception message and stack trace');
            $table->timestamps();

            // Indexes for performance
            $table->index('job_name');
            $table->index('status');
            $table->index('started_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cron_logs');
    }
};
