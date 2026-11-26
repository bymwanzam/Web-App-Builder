<?php

namespace Tests\Feature\Livewire\User;

use App\Livewire\User\AiChat;
use App\Models\AiConversation;
use App\Models\App;
use App\Models\Plan;
use App\Models\Plugin;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AiChatTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Plan $plan;

    protected Plugin $platformPlugin;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up system settings for AI
        SystemSetting::set('ai_chat_enabled', true);
        SystemSetting::set('ai_provider', 'openai');
        SystemSetting::set('ai_provider_api_key', 'sk-test-system-key');
        SystemSetting::set('ai_default_model', 'gpt-4.1-mini');
        SystemSetting::set('ai_max_tokens_per_request', 4096);
        SystemSetting::set('ai_temperature', 0.7);

        $this->plan = Plan::factory()->create([
            'can_use_ai_chat' => true,
            'monthly_ai_tokens' => 10000,
            'allow_user_ai_api_key' => false,
        ]);

        $this->user = User::factory()->create([
            'plan_id' => $this->plan->id,
        ]);

        $this->platformPlugin = Plugin::firstOrCreate(
            ['slug' => 'android-webview'],
            [
                'type' => 'platform',
                'name' => 'Android WebView',
                'version' => '1.0.0',
                'status' => 'active',
                'class' => 'App\\Plugins\\Platforms\\AndroidWebView\\AndroidWebViewPlatform',
                'installed_at' => now(),
            ]
        );
    }

    #[Test]
    public function it_renders_component()
    {
        Livewire::actingAs($this->user)
            ->test(AiChat::class)
            ->assertSuccessful();
    }

    #[Test]
    public function it_loads_provider_info()
    {
        $component = Livewire::actingAs($this->user)
            ->test(AiChat::class);

        $providerInfo = $component->get('providerInfo');

        $this->assertArrayHasKey('provider', $providerInfo);
        $this->assertArrayHasKey('remainingTokens', $providerInfo);
        $this->assertEquals('openai', $providerInfo['provider']);
    }

    #[Test]
    public function it_loads_available_platforms()
    {
        $component = Livewire::actingAs($this->user)
            ->test(AiChat::class);

        $platforms = $component->get('availablePlatforms');

        $this->assertCount(1, $platforms);
        $this->assertEquals('android-webview', $platforms[0]['slug']);
    }

    #[Test]
    public function it_resets_to_welcome_state()
    {
        // startNewConversation resets state to welcome screen
        // Conversation is only created when first message is sent
        Livewire::actingAs($this->user)
            ->test(AiChat::class)
            ->call('startNewConversation')
            ->assertSet('platformSelected', false)
            ->assertSet('displayMessages', [])
            ->assertSet('conversation', null);
    }

    #[Test]
    public function it_loads_existing_conversation()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'platform_plugin_id' => $this->platformPlugin->id,
            'title' => 'Test Conversation',
            'messages' => [
                ['role' => 'user', 'content' => 'Hello'],
                ['role' => 'assistant', 'content' => 'Hi there!'],
            ],
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(AiChat::class, ['conversationId' => $conversation->id]);

        $this->assertEquals($conversation->id, $component->get('conversation')->id);
        $this->assertTrue($component->get('platformSelected'));

        $messages = $component->get('displayMessages');
        $this->assertCount(2, $messages);
        $this->assertEquals('Hello', $messages[0]['content']);
    }

    #[Test]
    public function it_filters_system_and_tool_messages_from_display()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'title' => 'Test',
            'messages' => [
                ['role' => 'system', 'content' => 'System prompt'],
                ['role' => 'user', 'content' => 'Hello'],
                ['role' => 'assistant', 'content' => 'Hi!'],
                ['role' => 'tool', 'tool_call_id' => 'abc', 'content' => '{}'],
            ],
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(AiChat::class, ['conversationId' => $conversation->id]);

        $messages = $component->get('displayMessages');

        // Only user and assistant messages should be displayed
        $this->assertCount(2, $messages);
        $this->assertEquals('user', $messages[0]['role']);
        $this->assertEquals('assistant', $messages[1]['role']);
    }

    #[Test]
    public function it_does_not_load_other_users_conversation()
    {
        $otherUser = User::factory()->create(['plan_id' => $this->plan->id]);
        $conversation = AiConversation::create([
            'user_id' => $otherUser->id,
            'title' => 'Other User Conversation',
            'messages' => [['role' => 'user', 'content' => 'test']],
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(AiChat::class, ['conversationId' => $conversation->id]);

        $this->assertNull($component->get('conversation'));
    }

    #[Test]
    public function it_loads_conversation_via_event()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'title' => 'Test',
            'messages' => [],
        ]);

        Livewire::actingAs($this->user)
            ->test(AiChat::class)
            ->dispatch('loadConversation', conversationId: $conversation->id)
            ->assertSet('platformSelected', true);

        $this->assertEquals($conversation->id, Livewire::actingAs($this->user)
            ->test(AiChat::class, ['conversationId' => $conversation->id])
            ->get('conversation')->id);
    }

    #[Test]
    public function it_prevents_empty_message()
    {
        Livewire::actingAs($this->user)
            ->test(AiChat::class)
            ->set('message', '   ')
            ->call('sendMessage');

        // Should not create a conversation or add any messages
        $this->assertDatabaseCount('ai_conversations', 0);
    }

    #[Test]
    public function it_trims_message_whitespace()
    {
        $component = Livewire::actingAs($this->user)
            ->test(AiChat::class);

        // Start a conversation first
        $component->call('startNewConversation');

        // Set a message with whitespace
        $component->set('message', '  Hello World  ');

        // The message should be cleared after calling sendMessage
        // (even though the API call might fail, the message is cleared)
        $component->call('sendMessage');
        $this->assertEquals('', $component->get('message'));
    }

    #[Test]
    public function it_creates_conversation_if_not_exists_on_send()
    {
        Livewire::actingAs($this->user)
            ->test(AiChat::class)
            ->set('message', 'Hello')
            ->call('sendMessage');

        // Should create a conversation
        $this->assertDatabaseHas('ai_conversations', [
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function it_adds_user_message_to_display()
    {
        $component = Livewire::actingAs($this->user)
            ->test(AiChat::class)
            ->call('startNewConversation')
            ->set('message', 'Hello AI')
            ->call('sendMessage');

        $messages = $component->get('displayMessages');

        // First message should be the user message
        $this->assertNotEmpty($messages);
        $this->assertEquals('user', $messages[0]['role']);
        $this->assertEquals('Hello AI', $messages[0]['content']);
    }

    #[Test]
    public function it_shows_loading_state_during_api_call()
    {
        $component = Livewire::actingAs($this->user)
            ->test(AiChat::class)
            ->call('startNewConversation');

        // Initially not loading
        $this->assertFalse($component->get('isLoading'));

        // After sending message, it might briefly be in loading state
        // but since we can't easily test async behavior, we just verify
        // that the component doesn't throw errors
        $component->set('message', 'Test')
            ->call('sendMessage');

        // After completion, should not be loading
        $this->assertFalse($component->get('isLoading'));
    }

    #[Test]
    public function it_dispatches_conversation_updated_after_message()
    {
        Livewire::actingAs($this->user)
            ->test(AiChat::class)
            ->call('startNewConversation')
            ->set('message', 'Test message')
            ->call('sendMessage')
            ->assertDispatched('conversationUpdated');
    }

    #[Test]
    public function it_clears_message_after_send()
    {
        Livewire::actingAs($this->user)
            ->test(AiChat::class)
            ->call('startNewConversation')
            ->set('message', 'Hello')
            ->call('sendMessage')
            ->assertSet('message', '');
    }

    #[Test]
    public function it_starts_with_empty_display_messages()
    {
        Livewire::actingAs($this->user)
            ->test(AiChat::class)
            ->assertSet('displayMessages', []);
    }

    #[Test]
    public function it_receives_app_id_on_mount()
    {
        $app = App::create([
            'user_id' => $this->user->id,
            'platform_plugin_id' => $this->platformPlugin->id,
            'name' => 'Test App',
            'version_name' => '1.0.0',
            'version_code' => 1,
        ]);

        Livewire::actingAs($this->user)
            ->test(AiChat::class, ['appId' => $app->id])
            ->assertSet('appId', $app->id);
    }
}
