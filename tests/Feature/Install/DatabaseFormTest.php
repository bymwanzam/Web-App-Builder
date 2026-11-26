<?php

namespace Tests\Feature\Install;

use App\Livewire\Install\DatabaseForm;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class DatabaseFormTest extends InstallerTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Remove installation flag to allow installer access
        if (\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
            SystemSetting::where('key', 'installation_completed')->delete();
        }
    }

    #[Test]
    public function component_can_render(): void
    {
        Livewire::test(DatabaseForm::class)
            ->assertStatus(200);
    }

    #[Test]
    public function component_has_expected_properties(): void
    {
        // In test environment, default is sqlite, in production it defaults to mysql
        Livewire::test(DatabaseForm::class)
            ->assertSet('port', '3306');
    }

    #[Test]
    public function validates_db_type_is_required(): void
    {
        Livewire::test(DatabaseForm::class)
            ->set('dbType', '')
            ->call('save')
            ->assertHasErrors(['dbType']);
    }

    #[Test]
    public function validates_db_type_must_be_mysql_or_sqlite(): void
    {
        Livewire::test(DatabaseForm::class)
            ->set('dbType', 'postgres')
            ->call('save')
            ->assertHasErrors(['dbType']);
    }

    #[Test]
    public function validates_host_required_for_mysql(): void
    {
        Livewire::test(DatabaseForm::class)
            ->set('dbType', 'mysql')
            ->set('host', '')
            ->set('port', '3306')
            ->set('database', 'test_db')
            ->set('username', 'root')
            ->call('save')
            ->assertHasErrors(['host']);
    }

    #[Test]
    public function validates_port_required_for_mysql(): void
    {
        Livewire::test(DatabaseForm::class)
            ->set('dbType', 'mysql')
            ->set('host', 'localhost')
            ->set('port', '')
            ->set('database', 'test_db')
            ->set('username', 'root')
            ->call('save')
            ->assertHasErrors(['port']);
    }

    #[Test]
    public function validates_port_range(): void
    {
        Livewire::test(DatabaseForm::class)
            ->set('dbType', 'mysql')
            ->set('host', 'localhost')
            ->set('port', '99999')
            ->set('database', 'test_db')
            ->set('username', 'root')
            ->call('save')
            ->assertHasErrors(['port']);
    }

    #[Test]
    public function validates_port_minimum(): void
    {
        Livewire::test(DatabaseForm::class)
            ->set('dbType', 'mysql')
            ->set('host', 'localhost')
            ->set('port', '0')
            ->set('database', 'test_db')
            ->set('username', 'root')
            ->call('save')
            ->assertHasErrors(['port']);
    }

    #[Test]
    public function validates_database_required_for_mysql(): void
    {
        Livewire::test(DatabaseForm::class)
            ->set('dbType', 'mysql')
            ->set('host', 'localhost')
            ->set('port', '3306')
            ->set('database', '')
            ->set('username', 'root')
            ->call('save')
            ->assertHasErrors(['database']);
    }

    #[Test]
    public function validates_username_required_for_mysql(): void
    {
        Livewire::test(DatabaseForm::class)
            ->set('dbType', 'mysql')
            ->set('host', 'localhost')
            ->set('port', '3306')
            ->set('database', 'test_db')
            ->set('username', '')
            ->call('save')
            ->assertHasErrors(['username']);
    }

    #[Test]
    public function password_is_optional_for_mysql(): void
    {
        // Password is nullable, should not cause validation error
        Livewire::test(DatabaseForm::class)
            ->set('dbType', 'mysql')
            ->set('host', 'localhost')
            ->set('port', '3306')
            ->set('database', 'test_db')
            ->set('username', 'root')
            ->set('password', '')
            ->call('save')
            ->assertHasNoErrors(['password']);
    }

    #[Test]
    public function sqlite_does_not_require_mysql_fields(): void
    {
        Livewire::test(DatabaseForm::class)
            ->set('dbType', 'sqlite')
            ->set('host', '')
            ->set('port', '')
            ->set('database', '')
            ->set('username', '')
            ->call('save')
            ->assertHasNoErrors(['host', 'port', 'database', 'username']);
    }

    #[Test]
    public function shows_error_on_invalid_mysql_connection(): void
    {
        Livewire::test(DatabaseForm::class)
            ->set('dbType', 'mysql')
            ->set('host', 'invalid-host-that-does-not-exist')
            ->set('port', '3306')
            ->set('database', 'nonexistent_db')
            ->set('username', 'invalid_user')
            ->set('password', 'invalid_pass')
            ->call('save')
            ->assertHasErrors(['database']);
    }

    #[Test]
    public function validates_max_length_for_mysql_fields(): void
    {
        // Test host max length
        Livewire::test(DatabaseForm::class)
            ->set('dbType', 'mysql')
            ->set('host', str_repeat('a', 256))
            ->set('port', '3306')
            ->set('database', 'test_db')
            ->set('username', 'root')
            ->call('save')
            ->assertHasErrors(['host']);

        // Test database max length
        Livewire::test(DatabaseForm::class)
            ->set('dbType', 'mysql')
            ->set('host', 'localhost')
            ->set('port', '3306')
            ->set('database', str_repeat('a', 256))
            ->set('username', 'root')
            ->call('save')
            ->assertHasErrors(['database']);

        // Test username max length
        Livewire::test(DatabaseForm::class)
            ->set('dbType', 'mysql')
            ->set('host', 'localhost')
            ->set('port', '3306')
            ->set('database', 'test_db')
            ->set('username', str_repeat('a', 256))
            ->call('save')
            ->assertHasErrors(['username']);
    }
}
