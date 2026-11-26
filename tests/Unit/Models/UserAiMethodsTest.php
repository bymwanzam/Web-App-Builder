<?php

namespace Tests\Unit\Models;

use App\Models\AiConversation;
use App\Models\AiUsage;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserAiSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserAiMethodsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_checks_can_use_ai_chat_with_plan()
    {
        $planWithAi = Plan::factory()->create(['can_use_ai_chat' => true]);
        $planWithoutAi = Plan::factory()->create(['can_use_ai_chat' => false]);

        $userWithAi = User::factory()->create(['plan_id' => $planWithAi->id]);
        $userWithoutAi = User::factory()->create(['plan_id' => $planWithoutAi->id]);

        $this->assertTrue($userWithAi->canUseAiChat());
        $this->assertFalse($userWithoutAi->canUseAiChat());
    }

    #[Test]
    public function it_returns_false_for_can_use_ai_chat_without_plan()
    {
        $user = User::factory()->create(['plan_id' => null]);

        $this->assertFalse($user->canUseAiChat());
    }

    #[Test]
    public function it_checks_can_use_own_ai_api_key()
    {
        $planWithKey = Plan::factory()->create(['allow_user_ai_api_key' => true]);
        $planWithoutKey = Plan::factory()->create(['allow_user_ai_api_key' => false]);

        $userWithKey = User::factory()->create(['plan_id' => $planWithKey->id]);
        $userWithoutKey = User::factory()->create(['plan_id' => $planWithoutKey->id]);

        $this->assertTrue($userWithKey->canUseOwnAiApiKey());
        $this->assertFalse($userWithoutKey->canUseOwnAiApiKey());
    }

    #[Test]
    public function it_calculates_remaining_ai_tokens()
    {
        $plan = Plan::factory()->create([
            'can_use_ai_chat' => true,
            'monthly_ai_tokens' => 10000,
        ]);

        $user = User::factory()->create(['plan_id' => $plan->id]);

        // Use 3000 tokens
        AiUsage::create([
            'user_id' => $user->id,
            'provider' => 'openai',
            'model' => 'gpt-4',
            'prompt_tokens' => 1500,
            'completion_tokens' => 1500,
            'total_tokens' => 3000,
        ]);

        $this->assertEquals(7000, $user->getRemainingAiTokens());
    }

    #[Test]
    public function it_returns_zero_remaining_when_ai_disabled()
    {
        $plan = Plan::factory()->create([
            'can_use_ai_chat' => false,
            'monthly_ai_tokens' => 10000,
        ]);

        $user = User::factory()->create(['plan_id' => $plan->id]);

        $this->assertEquals(0, $user->getRemainingAiTokens());
    }

    #[Test]
    public function it_returns_max_int_for_unlimited_tokens()
    {
        $plan = Plan::factory()->create([
            'can_use_ai_chat' => true,
            'monthly_ai_tokens' => -1,
        ]);

        $user = User::factory()->create(['plan_id' => $plan->id]);

        $this->assertEquals(PHP_INT_MAX, $user->getRemainingAiTokens());
    }

    #[Test]
    public function it_returns_zero_when_quota_exhausted()
    {
        $plan = Plan::factory()->create([
            'can_use_ai_chat' => true,
            'monthly_ai_tokens' => 1000,
        ]);

        $user = User::factory()->create(['plan_id' => $plan->id]);

        // Use all tokens
        AiUsage::create([
            'user_id' => $user->id,
            'provider' => 'openai',
            'model' => 'gpt-4',
            'prompt_tokens' => 500,
            'completion_tokens' => 500,
            'total_tokens' => 1000,
        ]);

        $this->assertEquals(0, $user->getRemainingAiTokens());
    }

    #[Test]
    public function it_does_not_go_negative_when_over_quota()
    {
        $plan = Plan::factory()->create([
            'can_use_ai_chat' => true,
            'monthly_ai_tokens' => 1000,
        ]);

        $user = User::factory()->create(['plan_id' => $plan->id]);

        // Use more than quota
        AiUsage::create([
            'user_id' => $user->id,
            'provider' => 'openai',
            'model' => 'gpt-4',
            'prompt_tokens' => 1000,
            'completion_tokens' => 500,
            'total_tokens' => 1500,
        ]);

        $this->assertEquals(0, $user->getRemainingAiTokens());
    }

    #[Test]
    public function it_checks_is_using_own_api_key_when_configured()
    {
        $plan = Plan::factory()->create(['allow_user_ai_api_key' => true]);
        $user = User::factory()->create(['plan_id' => $plan->id]);

        UserAiSettings::create([
            'user_id' => $user->id,
            'preferred_provider' => 'openai',
            'openai_api_key' => 'sk-test-key',
        ]);

        $this->assertTrue($user->isUsingOwnAiApiKey());
    }

    #[Test]
    public function it_returns_false_for_own_key_when_using_system()
    {
        $plan = Plan::factory()->create(['allow_user_ai_api_key' => true]);
        $user = User::factory()->create(['plan_id' => $plan->id]);

        UserAiSettings::create([
            'user_id' => $user->id,
            'preferred_provider' => 'system',
        ]);

        $this->assertFalse($user->isUsingOwnAiApiKey());
    }

    #[Test]
    public function it_returns_false_for_own_key_when_no_settings()
    {
        $plan = Plan::factory()->create(['allow_user_ai_api_key' => true]);
        $user = User::factory()->create(['plan_id' => $plan->id]);

        $this->assertFalse($user->isUsingOwnAiApiKey());
    }

    #[Test]
    public function it_returns_false_for_own_key_when_plan_disallows()
    {
        $plan = Plan::factory()->create(['allow_user_ai_api_key' => false]);
        $user = User::factory()->create(['plan_id' => $plan->id]);

        // Even if they have settings, plan doesn't allow
        UserAiSettings::create([
            'user_id' => $user->id,
            'preferred_provider' => 'openai',
            'openai_api_key' => 'sk-test-key',
        ]);

        $this->assertFalse($user->isUsingOwnAiApiKey());
    }

    #[Test]
    public function it_returns_false_for_own_key_when_key_missing()
    {
        $plan = Plan::factory()->create(['allow_user_ai_api_key' => true]);
        $user = User::factory()->create(['plan_id' => $plan->id]);

        // Provider set but no key
        UserAiSettings::create([
            'user_id' => $user->id,
            'preferred_provider' => 'openai',
        ]);

        $this->assertFalse($user->isUsingOwnAiApiKey());
    }

    #[Test]
    public function it_has_ai_settings_relationship()
    {
        $plan = Plan::factory()->create();
        $user = User::factory()->create(['plan_id' => $plan->id]);

        $settings = UserAiSettings::create([
            'user_id' => $user->id,
            'preferred_provider' => 'system',
        ]);

        $this->assertInstanceOf(UserAiSettings::class, $user->aiSettings);
        $this->assertEquals($settings->id, $user->aiSettings->id);
    }

    #[Test]
    public function it_has_ai_conversations_relationship()
    {
        $plan = Plan::factory()->create();
        $user = User::factory()->create(['plan_id' => $plan->id]);

        AiConversation::create([
            'user_id' => $user->id,
            'messages' => [],
        ]);

        AiConversation::create([
            'user_id' => $user->id,
            'messages' => [],
        ]);

        $this->assertCount(2, $user->aiConversations);
    }

    #[Test]
    public function it_has_ai_usage_relationship()
    {
        $plan = Plan::factory()->create();
        $user = User::factory()->create(['plan_id' => $plan->id]);

        AiUsage::create([
            'user_id' => $user->id,
            'provider' => 'openai',
            'model' => 'gpt-4',
            'prompt_tokens' => 100,
            'completion_tokens' => 50,
            'total_tokens' => 150,
        ]);

        $this->assertCount(1, $user->aiUsage);
    }
}
