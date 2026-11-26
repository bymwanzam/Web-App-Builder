<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_policy_page_is_accessible(): void
    {
        $response = $this->get('/privacy');

        $response->assertStatus(200);
        $response->assertSee('Privacy Policy');
    }

    public function test_cookie_policy_page_is_accessible(): void
    {
        $response = $this->get('/cookies');

        $response->assertStatus(200);
        $response->assertSee('Cookie Policy');
    }

    public function test_terms_of_service_page_is_accessible(): void
    {
        $response = $this->get('/terms');

        $response->assertStatus(200);
        $response->assertSee('Terms of Service');
    }

    public function test_privacy_page_has_required_sections(): void
    {
        $response = $this->get('/privacy');

        $response->assertStatus(200);
        $response->assertSee('Information We Collect');
        $response->assertSee('How We Use Your Information');
        $response->assertSee('Your Rights');
        $response->assertSee('Contact Us');
    }

    public function test_terms_page_has_required_sections(): void
    {
        $response = $this->get('/terms');

        $response->assertStatus(200);
        $response->assertSee('Terms of Service');
        $response->assertSee('Acceptance of Terms');
    }

    public function test_cookie_page_has_required_sections(): void
    {
        $response = $this->get('/cookies');

        $response->assertStatus(200);
        $response->assertSee('Cookie Policy');
        $response->assertSee('What Are Cookies');
    }

    public function test_legal_pages_use_guest_layout(): void
    {
        // All legal pages should be accessible without authentication
        $this->get('/privacy')->assertStatus(200);
        $this->get('/cookies')->assertStatus(200);
        $this->get('/terms')->assertStatus(200);
    }
}
