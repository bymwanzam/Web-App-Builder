<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\UserConsent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserConsentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_consent_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $consent = UserConsent::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $consent->user);
        $this->assertEquals($user->id, $consent->user->id);
    }

    public function test_user_consent_casts_consented_to_boolean(): void
    {
        $consent = UserConsent::factory()->create(['consented' => 1]);

        $this->assertTrue($consent->consented);
        $this->assertIsBool($consent->consented);
    }

    public function test_user_consent_casts_dates_correctly(): void
    {
        $consent = UserConsent::factory()->create([
            'consented_at' => now(),
            'withdrawn_at' => now()->addDay(),
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $consent->consented_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $consent->withdrawn_at);
    }

    public function test_record_consent_creates_new_consent(): void
    {
        $user = User::factory()->create();

        $consent = UserConsent::recordConsent(
            user: $user,
            type: 'marketing',
            consented: true,
            version: '1.0'
        );

        $this->assertDatabaseHas('user_consents', [
            'user_id' => $user->id,
            'consent_type' => 'marketing',
            'consented' => true,
            'version' => '1.0',
        ]);
    }

    public function test_record_consent_updates_existing_consent(): void
    {
        $user = User::factory()->create();

        // First consent
        UserConsent::recordConsent($user, 'marketing', true, '1.0');

        // Update consent
        UserConsent::recordConsent($user, 'marketing', false, '1.1');

        $this->assertDatabaseCount('user_consents', 1);
        $this->assertDatabaseHas('user_consents', [
            'user_id' => $user->id,
            'consent_type' => 'marketing',
            'consented' => false,
            'version' => '1.1',
        ]);
    }

    public function test_record_consent_captures_ip_and_user_agent(): void
    {
        $user = User::factory()->create();

        $consent = UserConsent::recordConsent($user, 'marketing', true);

        $this->assertNotNull($consent->ip_address);
        $this->assertNotNull($consent->user_agent);
    }

    public function test_user_has_consents_relationship(): void
    {
        $user = User::factory()->create();
        $consentTypes = ['marketing', 'third_party', 'ai_services'];

        foreach ($consentTypes as $type) {
            UserConsent::factory()->create([
                'user_id' => $user->id,
                'consent_type' => $type,
            ]);
        }

        $this->assertCount(3, $user->consents);
    }

    public function test_user_consents_are_deleted_on_cascade(): void
    {
        $user = User::factory()->create();
        UserConsent::factory()->create(['user_id' => $user->id, 'consent_type' => 'marketing']);
        UserConsent::factory()->create(['user_id' => $user->id, 'consent_type' => 'third_party']);

        $user->delete();

        $this->assertDatabaseCount('user_consents', 0);
    }
}
