<?php

namespace Tests\Unit\Models;

use App\Models\AiConversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AiConversationEncryptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_messages_are_encrypted_in_database(): void
    {
        $user = User::factory()->create();
        $messages = [
            ['role' => 'user', 'content' => 'Hello, this is sensitive data'],
            ['role' => 'assistant', 'content' => 'I understand your private information'],
        ];

        $conversation = AiConversation::create([
            'user_id' => $user->id,
            'conversation_type' => 'app_editor',
            'title' => 'Test Conversation',
            'messages' => $messages,
        ]);

        // Get raw value from database
        $rawValue = DB::table('ai_conversations')
            ->where('id', $conversation->id)
            ->value('messages');

        // Raw value should not contain the original message content (it's encrypted)
        $this->assertStringNotContainsString('sensitive data', $rawValue ?? '');
        $this->assertStringNotContainsString('private information', $rawValue ?? '');

        // But the model should decrypt it correctly
        $this->assertEquals($messages, $conversation->fresh()->messages);
    }

    public function test_context_summary_is_encrypted_in_database(): void
    {
        $user = User::factory()->create();
        $summary = 'This is a sensitive summary of the conversation';

        $conversation = AiConversation::create([
            'user_id' => $user->id,
            'conversation_type' => 'app_editor',
            'title' => 'Test Conversation',
            'messages' => [],
            'context_summary' => $summary,
        ]);

        // Get raw value from database
        $rawValue = DB::table('ai_conversations')
            ->where('id', $conversation->id)
            ->value('context_summary');

        // Raw value should not match the original summary (it's encrypted)
        $this->assertNotEquals($summary, $rawValue);

        // But the model should decrypt it correctly
        $this->assertEquals($summary, $conversation->fresh()->context_summary);
    }

    public function test_null_context_summary_is_handled_correctly(): void
    {
        $user = User::factory()->create();

        $conversation = AiConversation::create([
            'user_id' => $user->id,
            'conversation_type' => 'app_editor',
            'title' => 'Empty Conversation',
            'messages' => [],
            'context_summary' => null,
        ]);

        // Messages column has NOT NULL constraint, so we use empty array
        $this->assertEquals([], $conversation->fresh()->messages);
        $this->assertNull($conversation->fresh()->context_summary);
    }

    public function test_messages_can_be_updated(): void
    {
        $user = User::factory()->create();
        $originalMessages = [['role' => 'user', 'content' => 'Original']];
        $newMessages = [
            ['role' => 'user', 'content' => 'Original'],
            ['role' => 'assistant', 'content' => 'New response'],
        ];

        $conversation = AiConversation::create([
            'user_id' => $user->id,
            'conversation_type' => 'app_editor',
            'title' => 'Test',
            'messages' => $originalMessages,
        ]);

        $conversation->update(['messages' => $newMessages]);

        $this->assertEquals($newMessages, $conversation->fresh()->messages);
    }

    public function test_empty_array_messages_work_correctly(): void
    {
        $user = User::factory()->create();

        $conversation = AiConversation::create([
            'user_id' => $user->id,
            'conversation_type' => 'app_editor',
            'title' => 'Empty Messages',
            'messages' => [],
        ]);

        $this->assertEquals([], $conversation->fresh()->messages);
    }
}
