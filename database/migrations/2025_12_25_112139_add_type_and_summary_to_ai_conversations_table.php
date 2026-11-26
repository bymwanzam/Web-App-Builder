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
        Schema::table('ai_conversations', function (Blueprint $table) {
            // Conversation type: 'main' (shown in /user/ai) or 'widget' (hidden, app-specific)
            $table->string('conversation_type')->default('main')->after('platform_plugin_id');

            // Summary of older messages for context management
            $table->text('context_summary')->nullable()->after('messages');

            // Index for efficient widget conversation lookup
            $table->index(['user_id', 'app_id', 'conversation_type'], 'ai_conv_widget_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_conversations', function (Blueprint $table) {
            $table->dropIndex('ai_conv_widget_lookup');
            $table->dropColumn(['conversation_type', 'context_summary']);
        });
    }
};
