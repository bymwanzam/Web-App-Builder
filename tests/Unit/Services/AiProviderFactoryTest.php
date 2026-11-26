<?php

namespace Tests\Unit\Services;

use App\Models\Plan;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\UserAiSettings;
use App\Services\AiProviderFactory;
use App\Services\AiProviders\AnthropicProvider;
use App\Services\AiProviders\OpenAiProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AiProviderFactoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up system settings for AI
        SystemSetting::set('ai_provider', 'openai');
        SystemSetting::set('ai_provider_api_key', 'sk-test-system-key');
    }

    #[Test]
    public function it_creates_openai_provider()
    {
        $provider = AiProviderFactory::createProvider('openai', 'sk-test-key');

        $this->assertInstanceOf(OpenAiProvider::class, $provider);
        $this->assertEquals('openai', $provider->getProviderId());
        $this->assertEquals('OpenAI', $provider->getName());
    }

    #[Test]
    public function it_creates_anthropic_provider()
    {
        $provider = AiProviderFactory::createProvider('anthropic', 'sk-ant-test-key');

        $this->assertInstanceOf(AnthropicProvider::class, $provider);
        $this->assertEquals('anthropic', $provider->getProviderId());
        $this->assertEquals('Anthropic (Claude)', $provider->getName());
    }

    #[Test]
    public function it_throws_exception_for_unknown_provider()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown AI provider: unknown');

        AiProviderFactory::createProvider('unknown', 'some-key');
    }

    #[Test]
    public function it_returns_system_provider()
    {
        $provider = AiProviderFactory::getSystemProvider();

        $this->assertInstanceOf(OpenAiProvider::class, $provider);
    }

    #[Test]
    public function it_throws_exception_when_system_key_not_configured()
    {
        SystemSetting::set('ai_provider_api_key', '');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('AI provider API key not configured');

        AiProviderFactory::getSystemProvider();
    }

    #[Test]
    public function it_returns_user_provider_when_using_own_key()
    {
        $plan = Plan::factory()->create(['allow_user_ai_api_key' => true]);
        $user = User::factory()->create(['plan_id' => $plan->id]);

        UserAiSettings::create([
            'user_id' => $user->id,
            'preferred_provider' => 'openai',
            'openai_api_key' => 'sk-user-key',
        ]);

        $provider = AiProviderFactory::forUser($user);

        $this->assertInstanceOf(OpenAiProvider::class, $provider);
    }

    #[Test]
    public function it_falls_back_to_system_when_user_prefers_system()
    {
        $plan = Plan::factory()->create(['allow_user_ai_api_key' => true]);
        $user = User::factory()->create(['plan_id' => $plan->id]);

        UserAiSettings::create([
            'user_id' => $user->id,
            'preferred_provider' => 'system',
        ]);

        $provider = AiProviderFactory::forUser($user);

        // Should use system provider
        $this->assertInstanceOf(OpenAiProvider::class, $provider);
    }

    #[Test]
    public function it_falls_back_to_system_when_user_has_no_key()
    {
        $plan = Plan::factory()->create(['allow_user_ai_api_key' => true]);
        $user = User::factory()->create(['plan_id' => $plan->id]);

        UserAiSettings::create([
            'user_id' => $user->id,
            'preferred_provider' => 'openai',
            // No API key set
        ]);

        $provider = AiProviderFactory::forUser($user);

        // Should fall back to system provider
        $this->assertInstanceOf(OpenAiProvider::class, $provider);
    }

    #[Test]
    public function it_falls_back_to_system_when_plan_disallows_own_key()
    {
        $plan = Plan::factory()->create(['allow_user_ai_api_key' => false]);
        $user = User::factory()->create(['plan_id' => $plan->id]);

        UserAiSettings::create([
            'user_id' => $user->id,
            'preferred_provider' => 'openai',
            'openai_api_key' => 'sk-user-key',
        ]);

        $provider = AiProviderFactory::forUser($user);

        // Should use system provider since plan doesn't allow own key
        $this->assertInstanceOf(OpenAiProvider::class, $provider);
    }

    #[Test]
    public function it_returns_available_providers()
    {
        $providers = AiProviderFactory::getAvailableProviders();

        $this->assertArrayHasKey('openai', $providers);
        $this->assertArrayHasKey('anthropic', $providers);

        $this->assertEquals('OpenAI', $providers['openai']['name']);
        $this->assertEquals('Anthropic (Claude)', $providers['anthropic']['name']);
    }

    #[Test]
    public function it_returns_models_for_openai()
    {
        $models = AiProviderFactory::getModelsForProvider('openai');

        $this->assertArrayHasKey('gpt-4.1', $models);
        $this->assertArrayHasKey('gpt-4.1-mini', $models);
        $this->assertArrayHasKey('gpt-4o', $models);
    }

    #[Test]
    public function it_returns_models_for_anthropic()
    {
        $models = AiProviderFactory::getModelsForProvider('anthropic');

        $this->assertArrayHasKey('claude-sonnet-4-5-20250514', $models);
        $this->assertArrayHasKey('claude-3-5-haiku-latest', $models);
    }

    #[Test]
    public function it_returns_empty_array_for_unknown_provider_models()
    {
        $models = AiProviderFactory::getModelsForProvider('unknown');

        $this->assertEmpty($models);
    }
}
