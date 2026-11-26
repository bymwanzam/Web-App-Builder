<?php

namespace Tests\Feature\Install;

use App\Models\SystemSetting;
use App\Services\InstallerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class InstallerServiceIntegrationTest extends InstallerTestCase
{
    use RefreshDatabase;

    private InstallerService $installer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->installer = new InstallerService;

        // Remove installation flag
        if (\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
            SystemSetting::where('key', 'installation_completed')->delete();
        }
    }

    #[Test]
    public function get_db_config_returns_expected_structure(): void
    {
        $config = $this->installer->getDbConfig();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('db_type', $config);
        $this->assertArrayHasKey('host', $config);
        $this->assertArrayHasKey('port', $config);
        $this->assertArrayHasKey('database', $config);
        $this->assertArrayHasKey('username', $config);
        $this->assertArrayHasKey('password', $config);

        // Password should never be returned for security
        $this->assertEmpty($config['password']);
    }

    #[Test]
    public function test_database_connections(): void
    {
        // SQLite should work in test environment
        $sqliteConfig = ['db_type' => 'sqlite'];
        $this->assertTrue($this->installer->testDatabaseConnection($sqliteConfig));

        // Invalid MySQL should fail
        $mysqlConfig = [
            'db_type' => 'mysql',
            'host' => 'invalid.host.that.does.not.exist',
            'port' => '3306',
            'database' => 'nonexistent_database',
            'username' => 'invalid_user',
            'password' => 'invalid_password',
        ];
        $this->assertFalse($this->installer->testDatabaseConnection($mysqlConfig));
    }

    #[Test]
    public function installer_checks_all_requirements(): void
    {
        // Dependencies and permissions are populated
        $this->assertNotEmpty($this->installer->dependencies);
        $this->assertIsArray($this->installer->dependencies);
        $this->assertNotEmpty($this->installer->permissions);
        $this->assertIsArray($this->installer->permissions);

        // All required PHP extensions are checked
        $requiredExtensions = [
            'BCMath', 'Ctype', 'cURL', 'DOM', 'Fileinfo', 'Filter', 'GD',
            'Hash', 'JSON', 'Mbstring', 'OpenSSL', 'PCRE', 'PDO',
            'Session', 'Tokenizer', 'XML', 'Zip',
        ];

        foreach ($requiredExtensions as $extension) {
            $this->assertArrayHasKey($extension, $this->installer->dependencies);
        }

        // Optional database extensions are checked
        $this->assertArrayHasKey('PDO MySQL (Optional)', $this->installer->dependencies);
        $this->assertArrayHasKey('PDO SQLite (Optional)', $this->installer->dependencies);

        // All required directories are checked
        $requiredPaths = [
            '.env', 'bootstrap/cache', 'public', 'storage', 'storage/app',
            'storage/app/public', 'storage/framework', 'storage/framework/cache',
            'storage/framework/cache/data', 'storage/framework/sessions',
            'storage/framework/views', 'storage/logs',
        ];

        foreach ($requiredPaths as $path) {
            $this->assertArrayHasKey($path, $this->installer->permissions);
        }
    }
}
