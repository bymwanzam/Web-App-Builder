<?php

namespace Tests\Unit\Models;

use App\Models\AiConversation;
use App\Models\AiUsage;
use App\Models\App;
use App\Models\Plan;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AiConversationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::factory()->create([
            'can_use_ai_chat' => true,
            'monthly_ai_tokens' => 10000,
        ]);

        $this->user = User::factory()->create([
            'plan_id' => $this->plan->id,
        ]);
    }

    #[Test]
    public function it_can_create_a_conversation()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'title' => 'Test Conversation',
            'messages' => [
                ['role' => 'user', 'content' => 'Hello'],
                ['role' => 'assistant', 'content' => 'Hi there!'],
            ],
            'provider' => 'openai',
            'model' => 'gpt-4',
            'total_tokens' => 100,
        ]);

        $this->assertDatabaseHas('ai_conversations', [
            'id' => $conversation->id,
            'user_id' => $this->user->id,
            'title' => 'Test Conversation',
            'provider' => 'openai',
            'model' => 'gpt-4',
        ]);
    }

    #[Test]
    public function it_casts_messages_to_array()
    {
        $messages = [
            ['role' => 'user', 'content' => 'Hello'],
            ['role' => 'assistant', 'content' => 'Hi there!'],
        ];

        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'messages' => $messages,
        ]);

        $conversation->refresh();

        $this->assertIsArray($conversation->messages);
        $this->assertCount(2, $conversation->messages);
        $this->assertEquals('user', $conversation->messages[0]['role']);
    }

    #[Test]
    public function it_belongs_to_user()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'messages' => [],
        ]);

        $this->assertInstanceOf(User::class, $conversation->user);
        $this->assertEquals($this->user->id, $conversation->user->id);
    }

    #[Test]
    public function it_can_belong_to_app()
    {
        $plugin = Plugin::factory()->create(['type' => 'platform', 'slug' => 'test-platform']);

        // Create app directly without factory's afterCreating callback
        // which tries to create platform-specific config tables
        $app = App::create([
            'user_id' => $this->user->id,
            'platform_plugin_id' => $plugin->id,
            'name' => 'Test App',
            'version_name' => '1.0.0',
            'version_code' => 1,
        ]);

        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'app_id' => $app->id,
            'messages' => [],
        ]);

        $this->assertInstanceOf(App::class, $conversation->app);
        $this->assertEquals($app->id, $conversation->app->id);
    }

    #[Test]
    public function it_can_belong_to_platform_plugin()
    {
        $plugin = Plugin::factory()->create(['type' => 'platform', 'slug' => 'test-platform']);

        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'platform_plugin_id' => $plugin->id,
            'messages' => [],
        ]);

        $this->assertInstanceOf(Plugin::class, $conversation->platformPlugin);
        $this->assertEquals($plugin->id, $conversation->platformPlugin->id);
    }

    #[Test]
    public function it_has_many_usage_records()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'messages' => [],
        ]);

        AiUsage::create([
            'user_id' => $this->user->id,
            'ai_conversation_id' => $conversation->id,
            'provider' => 'openai',
            'model' => 'gpt-4',
            'prompt_tokens' => 50,
            'completion_tokens' => 50,
            'total_tokens' => 100,
        ]);

        AiUsage::create([
            'user_id' => $this->user->id,
            'ai_conversation_id' => $conversation->id,
            'provider' => 'openai',
            'model' => 'gpt-4',
            'prompt_tokens' => 30,
            'completion_tokens' => 20,
            'total_tokens' => 50,
        ]);

        $this->assertCount(2, $conversation->usage);
    }
}
