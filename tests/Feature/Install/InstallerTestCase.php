<?php

namespace Tests\Feature\Install;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * Base test case for installer tests.
 *
 * This extends Laravel's base TestCase directly instead of our customized
 * Tests\TestCase because installer tests need to test behavior before
 * the application is fully installed (no seeders, fresh database state).
 *
 * IMPORTANT: These tests should be run with --process-isolation or separately:
 *   php artisan test tests/Feature/Install/ --process-isolation
 *   php artisan test --group=installer --process-isolation
 */
#[Group('installer')]
abstract class InstallerTestCase extends BaseTestCase
{
    /**
     * Creates the application.
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Installer tests don't run PlatformPluginsSeeder because
        // they test pre-installation state
    }
}
