<?php

namespace Tests\Unit\Models;

use App\Models\AiConversation;
use App\Models\AiUsage;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AiUsageTest extends TestCase
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
    public function it_can_create_usage_record()
    {
        $usage = AiUsage::create([
            'user_id' => $this->user->id,
            'provider' => 'openai',
            'model' => 'gpt-4',
            'prompt_tokens' => 100,
            'completion_tokens' => 50,
            'total_tokens' => 150,
            'estimated_cost' => 0.005,
        ]);

        $this->assertDatabaseHas('ai_usage', [
            'id' => $usage->id,
            'user_id' => $this->user->id,
            'provider' => 'openai',
            'total_tokens' => 150,
        ]);
    }

    #[Test]
    public function it_belongs_to_user()
    {
        $usage = AiUsage::create([
            'user_id' => $this->user->id,
            'provider' => 'openai',
            'model' => 'gpt-4',
            'prompt_tokens' => 100,
            'completion_tokens' => 50,
            'total_tokens' => 150,
        ]);

        $this->assertInstanceOf(User::class, $usage->user);
        $this->assertEquals($this->user->id, $usage->user->id);
    }

    #[Test]
    public function it_can_belong_to_conversation()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'messages' => [],
        ]);

        $usage = AiUsage::create([
            'user_id' => $this->user->id,
            'ai_conversation_id' => $conversation->id,
            'provider' => 'openai',
            'model' => 'gpt-4',
            'prompt_tokens' => 100,
            'completion_tokens' => 50,
            'total_tokens' => 150,
        ]);

        $this->assertInstanceOf(AiConversation::class, $usage->conversation);
        $this->assertEquals($conversation->id, $usage->conversation->id);
    }

    #[Test]
    public function it_calculates_monthly_usage_for_user()
    {
        // Create usage records for current month
        AiUsage::create([
            'user_id' => $this->user->id,
            'provider' => 'openai',
            'model' => 'gpt-4',
            'prompt_tokens' => 100,
            'completion_tokens' => 50,
            'total_tokens' => 150,
        ]);

        AiUsage::create([
            'user_id' => $this->user->id,
            'provider' => 'openai',
            'model' => 'gpt-4',
            'prompt_tokens' => 200,
            'completion_tokens' => 100,
            'total_tokens' => 300,
        ]);

        $monthlyUsage = AiUsage::getMonthlyUsageForUser($this->user->id);

        $this->assertEquals(450, $monthlyUsage);
    }

    #[Test]
    public function it_excludes_other_users_from_monthly_usage()
    {
        $otherUser = User::factory()->create(['plan_id' => $this->plan->id]);

        AiUsage::create([
            'user_id' => $this->user->id,
            'provider' => 'openai',
            'model' => 'gpt-4',
            'prompt_tokens' => 100,
            'completion_tokens' => 50,
            'total_tokens' => 150,
        ]);

        AiUsage::create([
            'user_id' => $otherUser->id,
            'provider' => 'openai',
            'model' => 'gpt-4',
            'prompt_tokens' => 500,
            'completion_tokens' => 500,
            'total_tokens' => 1000,
        ]);

        $monthlyUsage = AiUsage::getMonthlyUsageForUser($this->user->id);

        $this->assertEquals(150, $monthlyUsage);
    }

    #[Test]
    public function it_returns_zero_for_user_with_no_usage()
    {
        $monthlyUsage = AiUsage::getMonthlyUsageForUser($this->user->id);

        $this->assertEquals(0, $monthlyUsage);
    }

    #[Test]
    public function it_casts_estimated_cost_to_decimal()
    {
        $usage = AiUsage::create([
            'user_id' => $this->user->id,
            'provider' => 'openai',
            'model' => 'gpt-4',
            'prompt_tokens' => 100,
            'completion_tokens' => 50,
            'total_tokens' => 150,
            'estimated_cost' => 0.005678,
        ]);

        $usage->refresh();

        $this->assertIsString($usage->estimated_cost);
    }
}
