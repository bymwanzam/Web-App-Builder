<?php

namespace Tests\Unit\Models;

use App\Models\Plan;
use App\Models\User;
use App\Models\UserAiSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserAiSettingsTest extends TestCase
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
            'allow_user_ai_api_key' => true,
        ]);

        $this->user = User::factory()->create([
            'plan_id' => $this->plan->id,
        ]);
    }

    #[Test]
    public function it_can_create_settings()
    {
        $settings = UserAiSettings::create([
            'user_id' => $this->user->id,
            'preferred_provider' => 'openai',
            'preferred_model' => 'gpt-4',
            'openai_api_key' => 'sk-test-key',
        ]);

        $this->assertDatabaseHas('user_ai_settings', [
            'id' => $settings->id,
            'user_id' => $this->user->id,
            'preferred_provider' => 'openai',
            'preferred_model' => 'gpt-4',
        ]);
    }

    #[Test]
    public function it_belongs_to_user()
    {
        $settings = UserAiSettings::create([
            'user_id' => $this->user->id,
            'preferred_provider' => 'system',
        ]);

        $this->assertInstanceOf(User::class, $settings->user);
        $this->assertEquals($this->user->id, $settings->user->id);
    }

    #[Test]
    public function it_encrypts_api_keys()
    {
        $settings = UserAiSettings::create([
            'user_id' => $this->user->id,
            'preferred_provider' => 'openai',
            'openai_api_key' => 'sk-test-openai-key',
            'anthropic_api_key' => 'sk-ant-test-key',
        ]);

        // Raw database value should be encrypted (not equal to original)
        $rawValue = \DB::table('user_ai_settings')
            ->where('id', $settings->id)
            ->value('openai_api_key');

        $this->assertNotEquals('sk-test-openai-key', $rawValue);

        // But model should decrypt it
        $settings->refresh();
        $this->assertEquals('sk-test-openai-key', $settings->openai_api_key);
        $this->assertEquals('sk-ant-test-key', $settings->anthropic_api_key);
    }

    #[Test]
    public function it_hides_api_keys_from_serialization()
    {
        $settings = UserAiSettings::create([
            'user_id' => $this->user->id,
            'preferred_provider' => 'openai',
            'openai_api_key' => 'sk-test-key',
            'anthropic_api_key' => 'sk-ant-test-key',
        ]);

        $array = $settings->toArray();

        $this->assertArrayNotHasKey('openai_api_key', $array);
        $this->assertArrayNotHasKey('anthropic_api_key', $array);
    }

    #[Test]
    public function it_checks_if_has_api_key_for_openai()
    {
        $settings = UserAiSettings::create([
            'user_id' => $this->user->id,
            'preferred_provider' => 'openai',
            'openai_api_key' => 'sk-test-key',
        ]);

        $this->assertTrue($settings->hasApiKeyFor('openai'));
        $this->assertFalse($settings->hasApiKeyFor('anthropic'));
    }

    #[Test]
    public function it_checks_if_has_api_key_for_anthropic()
    {
        $settings = UserAiSettings::create([
            'user_id' => $this->user->id,
            'preferred_provider' => 'anthropic',
            'anthropic_api_key' => 'sk-ant-test-key',
        ]);

        $this->assertFalse($settings->hasApiKeyFor('openai'));
        $this->assertTrue($settings->hasApiKeyFor('anthropic'));
    }

    #[Test]
    public function it_returns_false_for_unknown_provider()
    {
        $settings = UserAiSettings::create([
            'user_id' => $this->user->id,
            'preferred_provider' => 'system',
        ]);

        $this->assertFalse($settings->hasApiKeyFor('unknown'));
    }

    #[Test]
    public function it_gets_api_key_for_provider()
    {
        $settings = UserAiSettings::create([
            'user_id' => $this->user->id,
            'preferred_provider' => 'openai',
            'openai_api_key' => 'sk-test-openai',
            'anthropic_api_key' => 'sk-ant-test',
        ]);

        $this->assertEquals('sk-test-openai', $settings->getApiKeyFor('openai'));
        $this->assertEquals('sk-ant-test', $settings->getApiKeyFor('anthropic'));
        $this->assertNull($settings->getApiKeyFor('unknown'));
    }

    #[Test]
    public function it_returns_null_for_empty_api_key()
    {
        $settings = UserAiSettings::create([
            'user_id' => $this->user->id,
            'preferred_provider' => 'system',
        ]);

        $this->assertNull($settings->getApiKeyFor('openai'));
        $this->assertNull($settings->getApiKeyFor('anthropic'));
    }
}
