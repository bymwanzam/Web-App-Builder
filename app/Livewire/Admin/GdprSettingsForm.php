<?php

namespace App\Livewire\Admin;

use App\Models\SystemSetting;
use Livewire\Component;

class GdprSettingsForm extends Component
{
    // Policy versions
    public string $privacyPolicyVersion = '1.0';

    public string $termsPolicyVersion = '1.0';

    public string $cookiePolicyVersion = '1.0';

    // Data retention periods (in days)
    public int $retentionTransactions = 2555; // 7 years

    public int $retentionInactiveAccounts = 730; // 2 years

    public int $retentionExpiredBuilds = 90;

    public int $retentionAuditLogs = 365; // 1 year

    public int $retentionDataExports = 7;

    // Grace periods
    public int $accountDeletionGraceDays = 7;

    public int $dataExportRateLimitHours = 24;

    // Feature toggles
    public bool $cookieConsentEnabled = true;

    public bool $dataExportEnabled = true;

    public bool $accountDeletionEnabled = true;

    public function mount(): void
    {
        // Load existing settings
        $this->privacyPolicyVersion = SystemSetting::get('privacy_policy_version', '1.0');
        $this->termsPolicyVersion = SystemSetting::get('terms_policy_version', '1.0');
        $this->cookiePolicyVersion = SystemSetting::get('cookie_policy_version', '1.0');

        $this->retentionTransactions = (int) SystemSetting::get('data_retention_days_transactions', 2555);
        $this->retentionInactiveAccounts = (int) SystemSetting::get('data_retention_days_inactive_accounts', 730);
        $this->retentionExpiredBuilds = (int) SystemSetting::get('data_retention_days_expired_builds', 90);
        $this->retentionAuditLogs = (int) SystemSetting::get('data_retention_days_audit_logs', 365);
        $this->retentionDataExports = (int) SystemSetting::get('data_retention_days_data_exports', 7);

        $this->accountDeletionGraceDays = (int) SystemSetting::get('account_deletion_grace_days', 7);
        $this->dataExportRateLimitHours = (int) SystemSetting::get('data_export_rate_limit_hours', 24);

        $this->cookieConsentEnabled = (bool) SystemSetting::get('cookie_consent_enabled', true);
        $this->dataExportEnabled = (bool) SystemSetting::get('data_export_enabled', true);
        $this->accountDeletionEnabled = (bool) SystemSetting::get('account_deletion_enabled', true);
    }

    public function save(): void
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast', type: 'error', message: 'Settings cannot be modified in demo mode.');

            return;
        }

        $this->validate([
            'privacyPolicyVersion' => 'required|string|max:20',
            'termsPolicyVersion' => 'required|string|max:20',
            'cookiePolicyVersion' => 'required|string|max:20',
            'retentionTransactions' => 'required|integer|min:365',
            'retentionInactiveAccounts' => 'required|integer|min:30',
            'retentionExpiredBuilds' => 'required|integer|min:7',
            'retentionAuditLogs' => 'required|integer|min:30',
            'retentionDataExports' => 'required|integer|min:1',
            'accountDeletionGraceDays' => 'required|integer|min:1|max:30',
            'dataExportRateLimitHours' => 'required|integer|min:1',
        ]);

        // Save policy versions
        SystemSetting::set('privacy_policy_version', $this->privacyPolicyVersion);
        SystemSetting::set('terms_policy_version', $this->termsPolicyVersion);
        SystemSetting::set('cookie_policy_version', $this->cookiePolicyVersion);

        // Save retention periods
        SystemSetting::set('data_retention_days_transactions', (string) $this->retentionTransactions);
        SystemSetting::set('data_retention_days_inactive_accounts', (string) $this->retentionInactiveAccounts);
        SystemSetting::set('data_retention_days_expired_builds', (string) $this->retentionExpiredBuilds);
        SystemSetting::set('data_retention_days_audit_logs', (string) $this->retentionAuditLogs);
        SystemSetting::set('data_retention_days_data_exports', (string) $this->retentionDataExports);

        // Save grace periods
        SystemSetting::set('account_deletion_grace_days', (string) $this->accountDeletionGraceDays);
        SystemSetting::set('data_export_rate_limit_hours', (string) $this->dataExportRateLimitHours);

        // Save feature toggles
        SystemSetting::set('cookie_consent_enabled', $this->cookieConsentEnabled ? '1' : '0');
        SystemSetting::set('data_export_enabled', $this->dataExportEnabled ? '1' : '0');
        SystemSetting::set('account_deletion_enabled', $this->accountDeletionEnabled ? '1' : '0');

        $this->dispatch('show-toast', type: 'success', message: 'GDPR settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.admin.gdpr-settings-form');
    }
}
