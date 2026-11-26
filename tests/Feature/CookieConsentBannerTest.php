<?php

namespace Tests\Feature;

use App\Livewire\CookieConsentBanner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CookieConsentBannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cookie_consent_banner_component_renders(): void
    {
        Livewire::test(CookieConsentBanner::class)
            ->assertStatus(200);
    }

    public function test_banner_shows_when_no_consent_cookie(): void
    {
        Livewire::test(CookieConsentBanner::class)
            ->assertSet('show', true);
    }

    public function test_accept_all_sets_all_preferences(): void
    {
        Livewire::test(CookieConsentBanner::class)
            ->call('acceptAll')
            ->assertSet('show', false)
            ->assertDispatched('cookie-consent-saved');
    }

    public function test_accept_essential_only_sets_minimal_preferences(): void
    {
        Livewire::test(CookieConsentBanner::class)
            ->call('acceptEssential')
            ->assertSet('show', false)
            ->assertDispatched('cookie-consent-saved');
    }

    public function test_can_toggle_preferences_view(): void
    {
        Livewire::test(CookieConsentBanner::class)
            ->assertSet('showPreferences', false)
            ->set('showPreferences', true)
            ->assertSet('showPreferences', true);
    }

    public function test_can_save_custom_preferences(): void
    {
        Livewire::test(CookieConsentBanner::class)
            ->set('analytics', true)
            ->set('marketing', false)
            ->call('savePreferences')
            ->assertSet('show', false)
            ->assertDispatched('cookie-consent-saved');
    }

    public function test_banner_is_included_in_guest_layout(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeLivewire('cookie-consent-banner');
    }
}
