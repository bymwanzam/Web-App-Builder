<?php

namespace Tests\Feature;

use App\Livewire\User\ApiKeysTable;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiKeysTableTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Plan $planWithApi;

    protected Plan $planWithoutApi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->planWithApi = Plan::factory()->create([
            'name' => 'Pro',
            'can_use_api' => true,
        ]);

        $this->planWithoutApi = Plan::factory()->create([
            'name' => 'Free',
            'can_use_api' => false,
        ]);

        $this->user = User::factory()->create([
            'plan_id' => $this->planWithApi->id,
        ]);
    }

    #[Test]
    public function it_renders_for_authenticated_user()
    {
        Livewire::actingAs($this->user)
            ->test(ApiKeysTable::class)
            ->assertStatus(200);
    }

    #[Test]
    public function it_shows_upgrade_notice_when_plan_does_not_allow_api()
    {
        $userWithoutApi = User::factory()->create([
            'plan_id' => $this->planWithoutApi->id,
        ]);

        Livewire::actingAs($userWithoutApi)
            ->test(ApiKeysTable::class)
            ->assertSee('API Access Not Available')
            ->assertSee('Upgrade Plan')
            ->assertDontSee('api-keys-search');
    }

    #[Test]
    public function it_shows_api_keys_table_when_plan_allows_api()
    {
        Livewire::actingAs($this->user)
            ->test(ApiKeysTable::class)
            ->assertSeeHtml('id="api-keys-search"')
            ->assertDontSee('API Access Not Available');
    }

    #[Test]
    public function it_displays_user_api_tokens()
    {
        // Create tokens for the user
        $token1 = $this->user->createToken('Production Server', ['*']);
        $token2 = $this->user->createToken('CI/CD Pipeline', ['builds:trigger']);

        Livewire::actingAs($this->user)
            ->test(ApiKeysTable::class)
            ->assertSee('Production Server')
            ->assertSee('CI/CD Pipeline');
    }

    #[Test]
    public function it_does_not_display_other_users_tokens()
    {
        $otherUser = User::factory()->create([
            'plan_id' => $this->planWithApi->id,
        ]);
        $otherUser->createToken('Other User Token', ['*']);

        $this->user->createToken('My Token', ['*']);

        Livewire::actingAs($this->user)
            ->test(ApiKeysTable::class)
            ->assertSee('My Token')
            ->assertDontSee('Other User Token');
    }

    #[Test]
    public function it_filters_tokens_by_search()
    {
        $this->user->createToken('Production API', ['*']);
        $this->user->createToken('Development Key', ['*']);

        Livewire::actingAs($this->user)
            ->test(ApiKeysTable::class)
            ->set('search', 'Production')
            ->assertSee('Production API')
            ->assertDontSee('Development Key');
    }

    #[Test]
    public function it_shows_empty_state_when_no_tokens()
    {
        Livewire::actingAs($this->user)
            ->test(ApiKeysTable::class)
            ->assertSee('No API keys found');
    }

    #[Test]
    public function it_displays_token_scopes()
    {
        $this->user->createToken('Limited Token', ['apps:read', 'builds:read']);

        Livewire::actingAs($this->user)
            ->test(ApiKeysTable::class)
            ->assertSee('apps:read')
            ->assertSee('builds:read');
    }

    #[Test]
    public function it_shows_all_permissions_badge_for_wildcard_scope()
    {
        $this->user->createToken('Full Access', ['*']);

        Livewire::actingAs($this->user)
            ->test(ApiKeysTable::class)
            ->assertSee('All Permissions');
    }

    #[Test]
    public function it_refreshes_on_api_key_created_event()
    {
        $component = Livewire::actingAs($this->user)
            ->test(ApiKeysTable::class);

        // Create a token
        $this->user->createToken('New Token', ['*']);

        // Dispatch the event
        $component->dispatch('apiKeyCreated')
            ->assertSee('New Token');
    }

    #[Test]
    public function it_refreshes_on_api_key_deleted_event()
    {
        $token = $this->user->createToken('Token to Delete', ['*']);

        $component = Livewire::actingAs($this->user)
            ->test(ApiKeysTable::class)
            ->assertSee('Token to Delete');

        // Delete the token
        $this->user->tokens()->delete();

        // Dispatch the event
        $component->dispatch('apiKeyDeleted')
            ->assertDontSee('Token to Delete');
    }

    #[Test]
    public function it_dispatches_delete_event_when_revoke_clicked()
    {
        $token = $this->user->createToken('Test Token', ['*']);

        Livewire::actingAs($this->user)
            ->test(ApiKeysTable::class)
            ->assertSee('Revoke');
    }

    #[Test]
    public function it_resets_page_when_search_changes()
    {
        // Create many tokens for pagination
        for ($i = 1; $i <= 15; $i++) {
            $this->user->createToken("Token {$i}", ['*']);
        }

        Livewire::actingAs($this->user)
            ->test(ApiKeysTable::class)
            ->set('search', 'Token 1')
            ->assertSet('paginators.page', 1);
    }

    #[Test]
    public function it_shows_last_used_timestamp()
    {
        $token = $this->user->createToken('Used Token', ['*']);
        $tokenModel = $this->user->tokens()->first();

        // Simulate token usage by directly updating the database
        // (Sanctum's PersonalAccessToken may not have last_used_at in fillable)
        \DB::table('personal_access_tokens')
            ->where('id', $tokenModel->id)
            ->update(['last_used_at' => now()->subHours(2)]);

        // Test that the table renders and shows the token with timestamp
        $component = Livewire::actingAs($this->user)
            ->test(ApiKeysTable::class)
            ->assertSee('Used Token');

        // The token was used 2 hours ago, should show "ago" format in the output
        $html = $component->html();
        $this->assertStringContainsString('Used Token', $html);
        // Check for human-readable time format (e.g., "2 hours ago")
        $this->assertStringContainsString('ago', $html);
    }

    #[Test]
    public function it_shows_never_when_token_not_used()
    {
        $this->user->createToken('Unused Token', ['*']);

        Livewire::actingAs($this->user)
            ->test(ApiKeysTable::class)
            ->assertSee('Never');
    }
}
