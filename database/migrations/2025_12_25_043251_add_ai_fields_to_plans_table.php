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
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('can_use_ai_chat')->default(false)->after('can_use_api');
            $table->integer('monthly_ai_tokens')->default(0)->after('can_use_ai_chat');
            $table->boolean('allow_user_ai_api_key')->default(false)->after('monthly_ai_tokens');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['can_use_ai_chat', 'monthly_ai_tokens', 'allow_user_ai_api_key']);
        });
    }
};
