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
        Schema::create('apps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('url');
            $table->enum('status', ['ready', 'building', 'draft', 'incomplete'])->default('draft');
            $table->string('theme_color')->default('blue');
            $table->enum('title_color', ['white', 'black'])->default('white');
            $table->enum('layout_template', ['drawer', 'tabs', 'bar', 'blank'])->default('drawer');
            $table->string('icon_type')->default('mobile');
            $table->json('platforms');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps');
    }
};
