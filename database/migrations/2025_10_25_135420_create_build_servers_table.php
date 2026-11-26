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
        Schema::create('build_servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->integer('port');
            $table->string('server_key')->unique();
            $table->integer('max_queue')->default(5);
            $table->enum('platform', ['android'])->default('android');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('current_builds_count')->default(0);
            $table->integer('total_builds_processed')->default(0);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('build_servers');
    }
};
