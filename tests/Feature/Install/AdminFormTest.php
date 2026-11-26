<?php

namespace Tests\Feature\Install;

use App\Livewire\Install\AdminForm;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class AdminFormTest extends InstallerTestCase
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
        Livewire::test(AdminForm::class)
            ->assertStatus(200);
    }

    #[Test]
    public function component_has_default_site_name(): void
    {
        Livewire::test(AdminForm::class)
            ->assertSet('site_name', 'Appy');
    }

    #[Test]
    public function validates_name_is_required(): void
    {
        Livewire::test(AdminForm::class)
            ->set('name', '')
            ->set('email', 'admin@example.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->set('site_name', 'My Site')
            ->call('save')
            ->assertHasErrors(['name']);
    }

    #[Test]
    public function validates_email_is_required(): void
    {
        Livewire::test(AdminForm::class)
            ->set('name', 'Admin User')
            ->set('email', '')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->set('site_name', 'My Site')
            ->call('save')
            ->assertHasErrors(['email']);
    }

    #[Test]
    public function validates_email_format(): void
    {
        Livewire::test(AdminForm::class)
            ->set('name', 'Admin User')
            ->set('email', 'invalid-email')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->set('site_name', 'My Site')
            ->call('save')
            ->assertHasErrors(['email']);
    }

    #[Test]
    public function validates_password_is_required(): void
    {
        Livewire::test(AdminForm::class)
            ->set('name', 'Admin User')
            ->set('email', 'admin@example.com')
            ->set('password', '')
            ->set('password_confirmation', '')
            ->set('site_name', 'My Site')
            ->call('save')
            ->assertHasErrors(['password']);
    }

    #[Test]
    public function validates_password_confirmation(): void
    {
        Livewire::test(AdminForm::class)
            ->set('name', 'Admin User')
            ->set('email', 'admin@example.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'DifferentPassword!')
            ->set('site_name', 'My Site')
            ->call('save')
            ->assertHasErrors(['password']);
    }

    #[Test]
    public function validates_site_name_is_required(): void
    {
        Livewire::test(AdminForm::class)
            ->set('name', 'Admin User')
            ->set('email', 'admin@example.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->set('site_name', '')
            ->call('save')
            ->assertHasErrors(['site_name']);
    }

    #[Test]
    public function purchase_code_is_optional(): void
    {
        Livewire::test(AdminForm::class)
            ->set('name', 'Admin User')
            ->set('email', 'admin@example.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->set('site_name', 'My Site')
            ->set('purchase_code', '')
            ->call('save')
            ->assertHasNoErrors(['purchase_code']);
    }

    #[Test]
    public function validates_max_lengths(): void
    {
        // Test name max length
        Livewire::test(AdminForm::class)
            ->set('name', str_repeat('a', 256))
            ->set('email', 'admin@example.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->set('site_name', 'My Site')
            ->call('save')
            ->assertHasErrors(['name']);

        // Test email max length
        Livewire::test(AdminForm::class)
            ->set('name', 'Admin User')
            ->set('email', str_repeat('a', 250).'@example.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->set('site_name', 'My Site')
            ->call('save')
            ->assertHasErrors(['email']);

        // Test site_name max length
        Livewire::test(AdminForm::class)
            ->set('name', 'Admin User')
            ->set('email', 'admin@example.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->set('site_name', str_repeat('a', 256))
            ->call('save')
            ->assertHasErrors(['site_name']);

        // Test purchase_code max length
        Livewire::test(AdminForm::class)
            ->set('name', 'Admin User')
            ->set('email', 'admin@example.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->set('site_name', 'My Site')
            ->set('purchase_code', str_repeat('a', 256))
            ->call('save')
            ->assertHasErrors(['purchase_code']);
    }

    #[Test]
    public function empty_properties_on_initial_load(): void
    {
        Livewire::test(AdminForm::class)
            ->assertSet('name', '')
            ->assertSet('email', '')
            ->assertSet('password', '')
            ->assertSet('password_confirmation', '')
            ->assertSet('purchase_code', '');
    }
}
