<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\GdprSettingsForm;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GdprSettingsFormTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_gdpr_settings_form_renders(): void
    {
        Livewire::actingAs($this->admin)
            ->test(GdprSettingsForm::class)
            ->assertOk()
            ->assertSee('GDPR', escape: false)
            ->assertSee('Privacy Settings', escape: false);
    }

    public function test_loads_existing_settings(): void
    {
        SystemSetting::set('privacy_policy_version', '2.0');
        SystemSetting::set('data_retention_days_transactions', '3650');
        SystemSetting::set('cookie_consent_enabled', '1');

        Livewire::actingAs($this->admin)
            ->test(GdprSettingsForm::class)
            ->assertSet('privacyPolicyVersion', '2.0')
            ->assertSet('retentionTransactions', 3650)
            ->assertSet('cookieConsentEnabled', true);
    }

    public function test_can_save_gdpr_settings(): void
    {
        Livewire::actingAs($this->admin)
            ->test(GdprSettingsForm::class)
            ->set('privacyPolicyVersion', '3.0')
            ->set('termsPolicyVersion', '2.5')
            ->set('cookiePolicyVersion', '1.5')
            ->set('retentionTransactions', 1825)
            ->set('retentionInactiveAccounts', 365)
            ->set('retentionExpiredBuilds', 60)
            ->set('retentionAuditLogs', 180)
            ->set('retentionDataExports', 14)
            ->set('accountDeletionGraceDays', 14)
            ->set('dataExportRateLimitHours', 48)
            ->set('cookieConsentEnabled', false)
            ->set('dataExportEnabled', true)
            ->set('accountDeletionEnabled', false)
            ->call('save')
            ->assertDispatched('show-toast');

        $this->assertEquals('3.0', SystemSetting::get('privacy_policy_version'));
        $this->assertEquals('2.5', SystemSetting::get('terms_policy_version'));
        $this->assertEquals('1.5', SystemSetting::get('cookie_policy_version'));
        $this->assertEquals('1825', SystemSetting::get('data_retention_days_transactions'));
        $this->assertEquals('365', SystemSetting::get('data_retention_days_inactive_accounts'));
        $this->assertEquals('60', SystemSetting::get('data_retention_days_expired_builds'));
        $this->assertEquals('180', SystemSetting::get('data_retention_days_audit_logs'));
        $this->assertEquals('14', SystemSetting::get('data_retention_days_data_exports'));
        $this->assertEquals('14', SystemSetting::get('account_deletion_grace_days'));
        $this->assertEquals('48', SystemSetting::get('data_export_rate_limit_hours'));
        $this->assertEquals('0', SystemSetting::get('cookie_consent_enabled'));
        $this->assertEquals('1', SystemSetting::get('data_export_enabled'));
        $this->assertEquals('0', SystemSetting::get('account_deletion_enabled'));
    }

    public function test_validates_retention_periods(): void
    {
        Livewire::actingAs($this->admin)
            ->test(GdprSettingsForm::class)
            ->set('retentionTransactions', 100) // Min is 365
            ->call('save')
            ->assertHasErrors(['retentionTransactions']);
    }

    public function test_validates_grace_period_max(): void
    {
        Livewire::actingAs($this->admin)
            ->test(GdprSettingsForm::class)
            ->set('accountDeletionGraceDays', 45) // Max is 30
            ->call('save')
            ->assertHasErrors(['accountDeletionGraceDays']);
    }

    public function test_visible_on_admin_settings_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/settings')
            ->assertStatus(200)
            ->assertSeeLivewire(GdprSettingsForm::class);
    }

    public function test_cannot_save_in_demo_mode(): void
    {
        config(['app.demo' => true]);

        Livewire::actingAs($this->admin)
            ->test(GdprSettingsForm::class)
            ->set('privacyPolicyVersion', '5.0')
            ->call('save')
            ->assertDispatched('show-toast', type: 'error', message: 'Settings cannot be modified in demo mode.');

        // Verify settings were not changed
        $this->assertNotEquals('5.0', SystemSetting::get('privacy_policy_version'));
    }
}
