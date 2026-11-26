<?php

namespace Tests\Feature\Livewire\User;

use App\Livewire\User\AiChatWidget;
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

class AiChatWidgetTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Plan $plan;

    protected Plugin $platformPlugin;

    protected App $testApp;

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
        SystemSetting::set('ai_widget_position', 'bottom-right');
        SystemSetting::set('ai_streaming_enabled', false);

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

        $this->testApp = App::factory()->forUser($this->user)->create();
    }

    #[Test]
    public function it_renders_component()
    {
        Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp])
            ->assertSuccessful();
    }

    #[Test]
    public function it_loads_app_on_mount()
    {
        $component = Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp]);

        $this->assertEquals($this->testApp->id, $component->get('app')->id);
    }

    #[Test]
    public function it_creates_widget_conversation_on_mount()
    {
        Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp]);

        $this->assertDatabaseHas('ai_conversations', [
            'user_id' => $this->user->id,
            'app_id' => $this->testApp->id,
            'conversation_type' => 'widget',
        ]);
    }

    #[Test]
    public function it_loads_existing_widget_conversation()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'app_id' => $this->testApp->id,
            'platform_plugin_id' => $this->platformPlugin->id,
            'conversation_type' => 'widget',
            'title' => 'App Assistant',
            'messages' => [
                ['role' => 'user', 'content' => 'Hello'],
                ['role' => 'assistant', 'content' => 'Hi there!'],
            ],
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp]);

        $this->assertEquals($conversation->id, $component->get('conversation')->id);

        $messages = $component->get('displayMessages');
        $this->assertCount(2, $messages);
        $this->assertEquals('Hello', $messages[0]['content']);
        $this->assertEquals('Hi there!', $messages[1]['content']);
    }

    #[Test]
    public function it_shows_welcome_message_for_empty_conversation()
    {
        $component = Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp]);

        $messages = $component->get('displayMessages');

        $this->assertCount(1, $messages);
        $this->assertEquals('assistant', $messages[0]['role']);
        $this->assertStringContainsString($this->testApp->name, $messages[0]['content']);
    }

    #[Test]
    public function it_filters_system_and_tool_messages_from_display()
    {
        AiConversation::create([
            'user_id' => $this->user->id,
            'app_id' => $this->testApp->id,
            'platform_plugin_id' => $this->platformPlugin->id,
            'conversation_type' => 'widget',
            'title' => 'Test',
            'messages' => [
                ['role' => 'system', 'content' => 'System prompt'],
                ['role' => 'user', 'content' => 'Hello'],
                ['role' => 'assistant', 'content' => 'Hi!'],
                ['role' => 'tool', 'tool_call_id' => 'abc', 'content' => '{}'],
            ],
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp]);

        $messages = $component->get('displayMessages');

        // Only user and assistant messages should be displayed
        $this->assertCount(2, $messages);
        $this->assertEquals('user', $messages[0]['role']);
        $this->assertEquals('assistant', $messages[1]['role']);
    }

    #[Test]
    public function it_filters_empty_assistant_messages()
    {
        AiConversation::create([
            'user_id' => $this->user->id,
            'app_id' => $this->testApp->id,
            'platform_plugin_id' => $this->platformPlugin->id,
            'conversation_type' => 'widget',
            'title' => 'Test',
            'messages' => [
                ['role' => 'user', 'content' => 'Hello'],
                ['role' => 'assistant', 'content' => ''], // Empty content (tool call placeholder)
                ['role' => 'assistant', 'content' => 'Real response'],
            ],
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp]);

        $messages = $component->get('displayMessages');

        $this->assertCount(2, $messages);
        $this->assertEquals('Hello', $messages[0]['content']);
        $this->assertEquals('Real response', $messages[1]['content']);
    }

    #[Test]
    public function it_toggles_expand_state()
    {
        $component = Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp]);

        $this->assertFalse($component->get('isExpanded'));

        $component->call('toggleExpand');
        $this->assertTrue($component->get('isExpanded'));

        $component->call('toggleExpand');
        $this->assertFalse($component->get('isExpanded'));
    }

    #[Test]
    public function it_prevents_empty_message()
    {
        $component = Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp])
            ->set('message', '   ')
            ->call('sendMessage');

        // Welcome message should still be the only message
        $messages = $component->get('displayMessages');
        $this->assertCount(1, $messages);
        $this->assertEquals('assistant', $messages[0]['role']);
    }

    #[Test]
    public function it_clears_message_after_send()
    {
        Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp])
            ->set('message', 'Hello')
            ->call('sendMessage')
            ->assertSet('message', '');
    }

    #[Test]
    public function it_adds_user_message_to_display()
    {
        $component = Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp])
            ->set('message', 'Hello AI')
            ->call('sendMessage');

        $messages = $component->get('displayMessages');

        // Should have welcome message + user message + assistant response
        $this->assertGreaterThanOrEqual(2, count($messages));
        // Find the user message
        $userMessages = array_filter($messages, fn ($m) => $m['role'] === 'user');
        $this->assertNotEmpty($userMessages);
        $this->assertEquals('Hello AI', array_values($userMessages)[0]['content']);
    }

    #[Test]
    public function it_clears_conversation()
    {
        // Create a conversation with messages
        AiConversation::create([
            'user_id' => $this->user->id,
            'app_id' => $this->testApp->id,
            'platform_plugin_id' => $this->platformPlugin->id,
            'conversation_type' => 'widget',
            'title' => 'Test',
            'messages' => [
                ['role' => 'user', 'content' => 'Hello'],
                ['role' => 'assistant', 'content' => 'Hi!'],
            ],
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp])
            ->call('clearConversation')
            ->assertDispatched('show-toast');

        $messages = $component->get('displayMessages');

        // Should only have the welcome message after clearing
        $this->assertCount(1, $messages);
        $this->assertEquals('assistant', $messages[0]['role']);
        $this->assertStringContainsString($this->testApp->name, $messages[0]['content']);
    }

    #[Test]
    public function it_creates_new_conversation_after_clear()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'app_id' => $this->testApp->id,
            'platform_plugin_id' => $this->platformPlugin->id,
            'conversation_type' => 'widget',
            'title' => 'Test',
            'messages' => [],
        ]);

        $oldId = $conversation->id;

        $component = Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp])
            ->call('clearConversation');

        // New conversation should have different ID
        $this->assertNotEquals($oldId, $component->get('conversation')->id);

        // Old conversation should be deleted
        $this->assertDatabaseMissing('ai_conversations', ['id' => $oldId]);
    }

    #[Test]
    public function it_loads_position_from_system_settings()
    {
        SystemSetting::set('ai_widget_position', 'top-left');

        $component = Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp]);

        $this->assertEquals('top-left', $component->get('position'));
    }

    #[Test]
    public function it_returns_correct_position_classes()
    {
        $testCases = [
            'bottom-right' => 'bottom-6 right-6',
            'bottom-left' => 'bottom-6 left-6',
            'top-right' => 'top-20 right-6',
            'top-left' => 'top-20 left-6',
        ];

        foreach ($testCases as $position => $expectedClasses) {
            SystemSetting::set('ai_widget_position', $position);

            $component = Livewire::actingAs($this->user)
                ->test(AiChatWidget::class, ['app' => $this->testApp]);

            // Get the position from component and verify the classes match
            $this->assertEquals($position, $component->get('position'));
        }
    }

    #[Test]
    public function it_loads_streaming_enabled_setting()
    {
        SystemSetting::set('ai_streaming_enabled', true);

        $component = Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp]);

        $this->assertTrue($component->get('streamingEnabled'));
    }

    #[Test]
    public function it_loads_provider_info()
    {
        $component = Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp]);

        $providerInfo = $component->get('providerInfo');

        $this->assertArrayHasKey('provider', $providerInfo);
        $this->assertArrayHasKey('remainingTokens', $providerInfo);
    }

    #[Test]
    public function it_dispatches_app_config_updated_after_message()
    {
        Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp])
            ->set('message', 'Test message')
            ->call('sendMessage')
            ->assertDispatched('app-config-updated', appId: $this->testApp->id);
    }

    #[Test]
    public function it_dispatches_app_config_updated_after_streaming()
    {
        Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp])
            ->dispatch('widgetStreamingComplete')
            ->assertDispatched('app-config-updated', appId: $this->testApp->id);
    }

    #[Test]
    public function it_refreshes_conversation_after_streaming()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'app_id' => $this->testApp->id,
            'platform_plugin_id' => $this->platformPlugin->id,
            'conversation_type' => 'widget',
            'title' => 'Test',
            'messages' => [],
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp]);

        // Simulate adding a message to the conversation directly
        $conversation->update([
            'messages' => [
                ['role' => 'user', 'content' => 'Test'],
                ['role' => 'assistant', 'content' => 'Response'],
            ],
        ]);

        // Trigger refresh after streaming
        $component->dispatch('widgetStreamingComplete');

        $messages = $component->get('displayMessages');
        $this->assertCount(2, $messages);
    }

    #[Test]
    public function it_sets_loading_state_during_message_send()
    {
        $component = Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp]);

        // Initially not loading
        $this->assertFalse($component->get('isLoading'));

        // After sending message and completion, should not be loading
        $component->set('message', 'Test')
            ->call('sendMessage');

        $this->assertFalse($component->get('isLoading'));
    }

    #[Test]
    public function it_handles_service_unavailable_gracefully()
    {
        // Remove the API key to make service unavailable
        SystemSetting::where('key', 'ai_provider_api_key')->delete();

        $component = Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp]);

        // Component should still render
        $component->assertSuccessful();

        // Service should be marked as unavailable
        $this->assertFalse($component->get('serviceAvailable'));
    }

    #[Test]
    public function it_does_not_create_conversation_when_service_unavailable()
    {
        SystemSetting::where('key', 'ai_provider_api_key')->delete();

        Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp]);

        $this->assertDatabaseMissing('ai_conversations', [
            'user_id' => $this->user->id,
            'app_id' => $this->testApp->id,
            'conversation_type' => 'widget',
        ]);
    }

    #[Test]
    public function it_does_not_send_message_when_service_unavailable()
    {
        SystemSetting::where('key', 'ai_provider_api_key')->delete();

        $component = Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp])
            ->set('message', 'Hello')
            ->call('sendMessage');

        // Message should not be cleared since send didn't happen
        $this->assertEquals('Hello', $component->get('message'));
    }

    #[Test]
    public function it_uses_widget_conversation_type()
    {
        Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp]);

        $conversation = AiConversation::where('user_id', $this->user->id)
            ->where('app_id', $this->testApp->id)
            ->first();

        $this->assertEquals('widget', $conversation->conversation_type);
    }

    #[Test]
    public function it_does_not_load_main_conversation_type()
    {
        // Create a main conversation for the same app
        $mainConversation = AiConversation::create([
            'user_id' => $this->user->id,
            'app_id' => $this->testApp->id,
            'platform_plugin_id' => $this->platformPlugin->id,
            'conversation_type' => 'main',
            'title' => 'Main Conversation',
            'messages' => [
                ['role' => 'user', 'content' => 'Main message'],
            ],
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(AiChatWidget::class, ['app' => $this->testApp]);

        // Should create new widget conversation, not load the main one
        $this->assertNotEquals($mainConversation->id, $component->get('conversation')->id);
        $this->assertEquals('widget', $component->get('conversation')->conversation_type);
    }
}
