<?php

namespace Tests\Unit\Services;

use App\Models\AiConversation;
use App\Models\AiUsage;
use App\Models\Plan;
use App\Models\Plugin;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\UserAiSettings;
use App\Services\AiChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AiChatServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Plan $plan;

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
    }

    #[Test]
    public function it_can_be_instantiated()
    {
        $service = new AiChatService($this->user);

        $this->assertInstanceOf(AiChatService::class, $service);
    }

    #[Test]
    public function it_checks_can_chat_with_quota()
    {
        $service = new AiChatService($this->user);

        $this->assertTrue($service->canChat());
    }

    #[Test]
    public function it_returns_false_for_can_chat_when_quota_exhausted()
    {
        // Use all tokens
        AiUsage::create([
            'user_id' => $this->user->id,
            'provider' => 'openai',
            'model' => 'gpt-4',
            'prompt_tokens' => 5000,
            'completion_tokens' => 5000,
            'total_tokens' => 10000,
        ]);

        $service = new AiChatService($this->user);

        $this->assertFalse($service->canChat());
    }

    #[Test]
    public function it_returns_false_for_can_chat_when_plan_disallows()
    {
        $restrictedPlan = Plan::factory()->create(['can_use_ai_chat' => false]);
        $restrictedUser = User::factory()->create(['plan_id' => $restrictedPlan->id]);

        $service = new AiChatService($restrictedUser);

        $this->assertFalse($service->canChat());
    }

    #[Test]
    public function it_always_allows_chat_with_own_api_key()
    {
        $keyPlan = Plan::factory()->create([
            'can_use_ai_chat' => true,
            'monthly_ai_tokens' => 100,
            'allow_user_ai_api_key' => true,
        ]);
        $keyUser = User::factory()->create(['plan_id' => $keyPlan->id]);

        // Use all system tokens
        AiUsage::create([
            'user_id' => $keyUser->id,
            'provider' => 'openai',
            'model' => 'gpt-4',
            'prompt_tokens' => 50,
            'completion_tokens' => 50,
            'total_tokens' => 100,
        ]);

        // Set up own API key
        UserAiSettings::create([
            'user_id' => $keyUser->id,
            'preferred_provider' => 'openai',
            'openai_api_key' => 'sk-user-key',
        ]);

        $service = new AiChatService($keyUser);

        $this->assertTrue($service->canChat());
        $this->assertTrue($service->isUsingOwnKey());
    }

    #[Test]
    public function it_returns_provider_info()
    {
        $service = new AiChatService($this->user);

        $info = $service->getProviderInfo();

        $this->assertArrayHasKey('provider', $info);
        $this->assertArrayHasKey('providerName', $info);
        $this->assertArrayHasKey('model', $info);
        $this->assertArrayHasKey('usingOwnKey', $info);
        $this->assertArrayHasKey('remainingTokens', $info);
        $this->assertArrayHasKey('monthlyLimit', $info);
        $this->assertArrayHasKey('unlimitedTokens', $info);

        $this->assertEquals('openai', $info['provider']);
        $this->assertEquals('OpenAI', $info['providerName']);
        $this->assertFalse($info['usingOwnKey']);
        $this->assertEquals(10000, $info['remainingTokens']);
        $this->assertEquals(10000, $info['monthlyLimit']);
    }

    #[Test]
    public function it_returns_null_tokens_when_using_own_key()
    {
        $keyPlan = Plan::factory()->create(['allow_user_ai_api_key' => true]);
        $keyUser = User::factory()->create(['plan_id' => $keyPlan->id]);

        UserAiSettings::create([
            'user_id' => $keyUser->id,
            'preferred_provider' => 'openai',
            'openai_api_key' => 'sk-user-key',
        ]);

        $service = new AiChatService($keyUser);
        $info = $service->getProviderInfo();

        $this->assertTrue($info['usingOwnKey']);
        $this->assertNull($info['remainingTokens']);
        $this->assertNull($info['monthlyLimit']);
    }

    #[Test]
    public function it_returns_available_platforms()
    {
        Plugin::firstOrCreate(
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

        $service = new AiChatService($this->user);
        $platforms = $service->getAvailablePlatforms();

        $this->assertGreaterThanOrEqual(1, count($platforms));
        // Check that our platform is in the list (might be others from seeders)
        $ourPlatform = collect($platforms)->firstWhere('slug', 'android-webview');
        $this->assertNotNull($ourPlatform);
        $this->assertEquals('android-webview', $ourPlatform['slug']);
    }

    #[Test]
    public function it_creates_conversation()
    {
        $service = new AiChatService($this->user);

        $conversation = $service->createConversation();

        $this->assertInstanceOf(AiConversation::class, $conversation);
        $this->assertEquals($this->user->id, $conversation->user_id);
        $this->assertEmpty($conversation->messages);
    }

    #[Test]
    public function it_creates_conversation_with_app_and_platform()
    {
        $plugin = Plugin::firstOrCreate(
            ['slug' => 'android-webview-test'],
            [
                'type' => 'platform',
                'name' => 'Android WebView',
                'version' => '1.0.0',
                'status' => 'active',
                'class' => 'App\\Plugins\\Platforms\\AndroidWebView\\AndroidWebViewPlatform',
                'installed_at' => now(),
            ]
        );

        $service = new AiChatService($this->user);
        $conversation = $service->createConversation(null, $plugin->id);

        $this->assertEquals($plugin->id, $conversation->platform_plugin_id);
    }

    #[Test]
    public function it_deletes_conversation()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'messages' => [],
        ]);

        $service = new AiChatService($this->user);
        $result = $service->deleteConversation($conversation);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('ai_conversations', ['id' => $conversation->id]);
    }

    #[Test]
    public function it_does_not_delete_other_users_conversation()
    {
        $otherUser = User::factory()->create(['plan_id' => $this->plan->id]);
        $conversation = AiConversation::create([
            'user_id' => $otherUser->id,
            'messages' => [],
        ]);

        $service = new AiChatService($this->user);
        $result = $service->deleteConversation($conversation);

        $this->assertFalse($result);
        $this->assertDatabaseHas('ai_conversations', ['id' => $conversation->id]);
    }

    #[Test]
    public function it_gets_user_conversations()
    {
        AiConversation::create([
            'user_id' => $this->user->id,
            'title' => 'First',
            'messages' => [],
        ]);

        AiConversation::create([
            'user_id' => $this->user->id,
            'title' => 'Second',
            'messages' => [],
        ]);

        // Other user's conversation
        $otherUser = User::factory()->create(['plan_id' => $this->plan->id]);
        AiConversation::create([
            'user_id' => $otherUser->id,
            'title' => 'Other',
            'messages' => [],
        ]);

        $service = new AiChatService($this->user);
        $conversations = $service->getConversations();

        $this->assertCount(2, $conversations);
    }

    #[Test]
    public function it_limits_conversations()
    {
        for ($i = 0; $i < 25; $i++) {
            AiConversation::create([
                'user_id' => $this->user->id,
                'title' => "Conv {$i}",
                'messages' => [],
            ]);
        }

        $service = new AiChatService($this->user);
        $conversations = $service->getConversations(10);

        $this->assertCount(10, $conversations);
    }

    #[Test]
    public function it_orders_conversations_by_updated_at()
    {
        $old = AiConversation::create([
            'user_id' => $this->user->id,
            'title' => 'Old',
            'messages' => [],
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        $new = AiConversation::create([
            'user_id' => $this->user->id,
            'title' => 'New',
            'messages' => [],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $service = new AiChatService($this->user);
        $conversations = $service->getConversations();

        $this->assertEquals('New', $conversations->first()->title);
        $this->assertEquals('Old', $conversations->last()->title);
    }
}
