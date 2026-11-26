<?php

namespace Tests\Feature\Landing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoIframeBlockerTest extends TestCase
{
    use RefreshDatabase;

    public function test_blocker_component_not_rendered_when_demo_mode_disabled(): void
    {
        config(['app.demo' => false]);

        $response = $this->get('/');

        $response->assertDontSee('Open Demo in New Window');
        $response->assertDontSee('appy.titansystems.ph');
    }

    public function test_blocker_component_rendered_when_demo_mode_enabled(): void
    {
        config(['app.demo' => true]);

        $response = $this->get('/');

        $response->assertSee('Open Demo in New Window');
        $response->assertSee('appy.titansystems.ph');
        $response->assertSee('codecanyon.net');
    }

    public function test_blocker_has_alpine_iframe_detection_script(): void
    {
        config(['app.demo' => true]);

        $response = $this->get('/');

        $response->assertSee('window.self !== window.top');
        $response->assertSee('document.referrer');
    }

    public function test_blocker_has_correct_demo_url(): void
    {
        config(['app.demo' => true]);

        $response = $this->get('/');

        // The URL appears in the Alpine.js x-data attribute
        $response->assertSee('https://appy.titansystems.ph');
    }
}
