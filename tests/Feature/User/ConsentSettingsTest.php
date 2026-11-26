<?php

namespace Tests\Feature\User;

use App\Livewire\User\ConsentSettings;
use App\Models\User;
use App\Models\UserConsent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ConsentSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_consent_settings(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ConsentSettings::class)
            ->assertSuccessful()
            ->assertViewHas('consents');
    }

    public function test_user_can_update_marketing_consent(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ConsentSettings::class)
            ->set('marketing', true)
            ->call('saveConsents')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('user_consents', [
            'user_id' => $user->id,
            'consent_type' => 'marketing',
            'consented' => true,
        ]);
    }

    public function test_user_can_withdraw_consent(): void
    {
        $user = User::factory()->create();

        // First, give consent
        UserConsent::recordConsent($user, 'marketing', true);

        // Then withdraw
        Livewire::actingAs($user)
            ->test(ConsentSettings::class)
            ->set('marketing', false)
            ->call('saveConsents')
            ->assertDispatched('toast');

        $consent = UserConsent::where('user_id', $user->id)
            ->where('consent_type', 'marketing')
            ->first();

        $this->assertFalse($consent->consented);
        $this->assertNotNull($consent->withdrawn_at);
    }

    public function test_consent_settings_loads_existing_consents(): void
    {
        $user = User::factory()->create();

        UserConsent::recordConsent($user, 'marketing', true);
        UserConsent::recordConsent($user, 'third_party', false);

        Livewire::actingAs($user)
            ->test(ConsentSettings::class)
            ->assertSet('marketing', true)
            ->assertSet('thirdParty', false);
    }
}
