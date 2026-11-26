<?php

namespace Tests\Unit\Models;

use App\Models\Plan;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PlanAiFeaturesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_checks_can_use_ai_chat()
    {
        $planWithAi = Plan::factory()->create(['can_use_ai_chat' => true]);
        $planWithoutAi = Plan::factory()->create(['can_use_ai_chat' => false]);

        $this->assertTrue($planWithAi->canUseAiChat());
        $this->assertFalse($planWithoutAi->canUseAiChat());
    }

    #[Test]
    public function it_gets_monthly_ai_tokens()
    {
        $plan = Plan::factory()->create([
            'can_use_ai_chat' => true,
            'monthly_ai_tokens' => 50000,
        ]);

        $this->assertEquals(50000, $plan->getMonthlyAiTokens());
    }

    #[Test]
    public function it_returns_zero_tokens_when_ai_disabled()
    {
        $plan = Plan::factory()->create([
            'can_use_ai_chat' => false,
            'monthly_ai_tokens' => 50000,
        ]);

        $this->assertEquals(0, $plan->getMonthlyAiTokens());
    }

    #[Test]
    public function it_checks_unlimited_ai_tokens()
    {
        $unlimitedPlan = Plan::factory()->create([
            'can_use_ai_chat' => true,
            'monthly_ai_tokens' => -1,
        ]);

        $limitedPlan = Plan::factory()->create([
            'can_use_ai_chat' => true,
            'monthly_ai_tokens' => 10000,
        ]);

        $this->assertTrue($unlimitedPlan->hasUnlimitedAiTokens());
        $this->assertFalse($limitedPlan->hasUnlimitedAiTokens());
    }

    #[Test]
    public function it_returns_false_for_unlimited_when_ai_disabled()
    {
        $plan = Plan::factory()->create([
            'can_use_ai_chat' => false,
            'monthly_ai_tokens' => -1,
        ]);

        $this->assertFalse($plan->hasUnlimitedAiTokens());
    }

    #[Test]
    public function it_checks_allows_user_ai_api_key()
    {
        $planWithKey = Plan::factory()->create(['allow_user_ai_api_key' => true]);
        $planWithoutKey = Plan::factory()->create(['allow_user_ai_api_key' => false]);

        $this->assertTrue($planWithKey->allowsUserAiApiKey());
        $this->assertFalse($planWithoutKey->allowsUserAiApiKey());
    }

    #[Test]
    public function it_generates_ai_features_dynamically_when_enabled()
    {
        SystemSetting::set('ai_chat_enabled', true);

        $plan = Plan::factory()->create([
            'can_use_ai_chat' => true,
            'monthly_ai_tokens' => 50000,
            'features' => [['name' => 'Custom Feature', 'included' => true]],
        ]);

        $allFeatures = $plan->all_features;

        $aiFeatureNames = collect($allFeatures)->pluck('name')->toArray();

        $this->assertContains('50,000 AI tokens/month', $aiFeatureNames);
    }

    #[Test]
    public function it_shows_unlimited_ai_in_features()
    {
        SystemSetting::set('ai_chat_enabled', true);

        $plan = Plan::factory()->create([
            'can_use_ai_chat' => true,
            'monthly_ai_tokens' => -1,
            'features' => [],
        ]);

        $allFeatures = $plan->all_features;

        $aiFeatureNames = collect($allFeatures)->pluck('name')->toArray();

        $this->assertContains('Unlimited AI Chat', $aiFeatureNames);
    }

    #[Test]
    public function it_shows_ai_not_included_when_disabled_on_plan()
    {
        SystemSetting::set('ai_chat_enabled', true);

        $plan = Plan::factory()->create([
            'can_use_ai_chat' => false,
            'features' => [],
        ]);

        $allFeatures = $plan->all_features;

        $aiFeature = collect($allFeatures)->firstWhere('name', 'AI Chat Assistant');

        $this->assertNotNull($aiFeature);
        $this->assertFalse($aiFeature['included']);
    }

    #[Test]
    public function it_hides_ai_features_when_globally_disabled()
    {
        SystemSetting::set('ai_chat_enabled', false);

        $plan = Plan::factory()->create([
            'can_use_ai_chat' => true,
            'monthly_ai_tokens' => 50000,
            'features' => [['name' => 'Other Feature', 'included' => true]],
        ]);

        $allFeatures = $plan->all_features;

        $aiFeatureNames = collect($allFeatures)->pluck('name')->toArray();

        $this->assertNotContains('AI Chat Assistant', $aiFeatureNames);
        $this->assertNotContains('Unlimited AI Chat', $aiFeatureNames);
    }

    #[Test]
    public function it_shows_user_api_key_feature_when_allowed()
    {
        SystemSetting::set('ai_chat_enabled', true);

        $plan = Plan::factory()->create([
            'can_use_ai_chat' => true,
            'monthly_ai_tokens' => 10000,
            'allow_user_ai_api_key' => true,
            'features' => [],
        ]);

        $allFeatures = $plan->all_features;

        $apiKeyFeature = collect($allFeatures)->firstWhere('name', 'Use your own AI API key');

        $this->assertNotNull($apiKeyFeature);
        $this->assertTrue($apiKeyFeature['included']);
    }
}
