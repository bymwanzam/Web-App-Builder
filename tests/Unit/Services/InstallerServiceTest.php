<?php

namespace Tests\Unit\Services;

use App\Services\InstallerService;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Install\InstallerTestCase;

#[Group('installer')]
class InstallerServiceTest extends InstallerTestCase
{
    private InstallerService $installer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->installer = new InstallerService;
    }

    #[Test]
    public function constructor_populates_dependencies_array(): void
    {
        // Should have PHP version check
        $this->assertArrayHasKey('PHP 8.2+', $this->installer->dependencies);

        // Should have all required extensions
        $requiredExtensions = [
            'BCMath',
            'Ctype',
            'cURL',
            'DOM',
            'Fileinfo',
            'Filter',
            'GD',
            'Hash',
            'JSON',
            'Mbstring',
            'OpenSSL',
            'PCRE',
            'PDO',
            'Session',
            'Tokenizer',
            'XML',
            'Zip',
        ];

        foreach ($requiredExtensions as $extension) {
            $this->assertArrayHasKey($extension, $this->installer->dependencies);
        }

        // Should have optional extensions
        $this->assertArrayHasKey('PDO MySQL (Optional)', $this->installer->dependencies);
        $this->assertArrayHasKey('PDO SQLite (Optional)', $this->installer->dependencies);
    }

    #[Test]
    public function dependencies_values_are_booleans(): void
    {
        foreach ($this->installer->dependencies as $key => $value) {
            $this->assertIsBool($value, "Dependency '{$key}' should be a boolean");
        }
    }

    #[Test]
    public function php_version_check_works_correctly(): void
    {
        // Since we're running on PHP 8.2+, this should be true
        $this->assertTrue($this->installer->dependencies['PHP 8.2+']);
    }

    #[Test]
    public function constructor_populates_permissions_array(): void
    {
        // Should check .env file
        $this->assertArrayHasKey('.env', $this->installer->permissions);

        // Should check required directories
        $requiredDirectories = [
            'bootstrap/cache',
            'public',
            'storage',
            'storage/app',
            'storage/app/public',
            'storage/framework',
            'storage/framework/cache',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',
        ];

        foreach ($requiredDirectories as $directory) {
            $this->assertArrayHasKey($directory, $this->installer->permissions);
        }
    }

    #[Test]
    public function permissions_values_are_booleans(): void
    {
        foreach ($this->installer->permissions as $key => $value) {
            $this->assertIsBool($value, "Permission '{$key}' should be a boolean");
        }
    }

    #[Test]
    public function dependencies_count_is_correct(): void
    {
        // 1 PHP version + 17 required extensions + 2 optional = 20
        $this->assertCount(20, $this->installer->dependencies);
    }

    #[Test]
    public function permissions_count_is_correct(): void
    {
        // 1 file (.env) + 11 directories = 12
        $this->assertCount(12, $this->installer->permissions);
    }
}
