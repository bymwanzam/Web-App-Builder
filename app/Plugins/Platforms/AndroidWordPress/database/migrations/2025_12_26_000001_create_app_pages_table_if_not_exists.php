<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the app_pages table if it doesn't already exist.
     * This allows AndroidWordPress to be installed standalone or alongside AndroidWebView.
     */
    public function up(): void
    {
        if (! Schema::hasTable('app_pages')) {
            Schema::create('app_pages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
                $table->string('title', 100);
                $table->string('slug', 100);
                $table->longText('html_content');
                $table->text('css_content')->nullable();
                $table->text('js_content')->nullable();
                $table->enum('status', ['draft', 'published'])->default('draft');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->unique(['app_id', 'slug']);
                $table->index(['app_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * Note: We don't drop the shared table on uninstall to preserve data
     * that may belong to other platform plugins.
     */
    public function down(): void
    {
        // Don't drop shared table - other platforms may be using it
    }
};
