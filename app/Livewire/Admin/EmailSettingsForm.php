<?php

namespace App\Livewire\Admin;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class EmailSettingsForm extends Component
{
    public $mail_from_name;

    public $mail_from_address;

    public $mail_mailer;

    public $mail_host;

    public $mail_port;

    public $mail_username;

    public $mail_password;

    public $mail_encryption;

    public $admin_notification_email;

    public $admin_notification_events = [];

    public function mount()
    {
        $this->mail_from_name = SystemSetting::get('mail_from_name', 'Appy');
        $this->mail_from_address = SystemSetting::get('mail_from_address', 'noreply@appy.com');
        $this->mail_mailer = SystemSetting::get('mail_mailer', 'sendmail');
        $this->mail_host = SystemSetting::get('mail_host', 'smtp.mailtrap.io');
        $this->mail_port = SystemSetting::get('mail_port', 2525);
        $this->mail_username = SystemSetting::get('mail_username');
        $this->mail_password = SystemSetting::get('mail_password');
        $this->mail_encryption = SystemSetting::get('mail_encryption', 'tls');
        $this->admin_notification_email = SystemSetting::get('admin_notification_email');
        $this->admin_notification_events = SystemSetting::get('admin_notification_events', []);

        // Clear sensitive fields in demo mode
        if (config('app.demo')) {
            $this->mail_username = '';
            $this->mail_password = '';
        }
    }

    public function save()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Settings cannot be modified in demo mode.'
            );

            return;
        }

        $this->validate([
            'mail_from_name' => 'required|string|max:255',
            'mail_from_address' => 'required|email|max:255',
            'mail_mailer' => 'required|in:smtp,sendmail',
            'mail_host' => 'required_if:mail_mailer,smtp|nullable|string|max:255',
            'mail_port' => 'required_if:mail_mailer,smtp|nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'required_if:mail_mailer,smtp|nullable|in:tls,ssl,none',
            'admin_notification_email' => 'nullable|email|max:255',
            'admin_notification_events' => 'array',
        ]);

        SystemSetting::set('mail_from_name', $this->mail_from_name, 'string', 'email');
        SystemSetting::set('mail_from_address', $this->mail_from_address, 'string', 'email');
        SystemSetting::set('mail_mailer', $this->mail_mailer, 'string', 'email');
        SystemSetting::set('mail_host', $this->mail_host, 'string', 'email');
        SystemSetting::set('mail_port', $this->mail_port, 'integer', 'email');
        SystemSetting::set('mail_username', $this->mail_username, 'string', 'email');
        SystemSetting::set('mail_password', $this->mail_password, 'string', 'email');
        SystemSetting::set('mail_encryption', $this->mail_encryption, 'string', 'email');
        SystemSetting::set('admin_notification_email', $this->admin_notification_email, 'string', 'email');
        SystemSetting::set('admin_notification_events', $this->admin_notification_events, 'json', 'email');

        $this->dispatch('show-toast',
            type: 'success',
            message: 'Email settings updated successfully!'
        );
    }

    public function sendTestEmail()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Test emails cannot be sent in demo mode.'
            );

            return;
        }

        // Use admin notification email if set, otherwise fall back to current user
        $testEmail = $this->admin_notification_email ?: (auth()->user() ? auth()->user()->email : null);
        $testName = $this->admin_notification_email ? 'Admin' : (auth()->user() ? auth()->user()->name : null);

        if (! $testEmail) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'No email address found. Please set admin notification email or log in.'
            );

            return;
        }

        try {
            // Configure mail with current form values (not saved yet)
            $this->applyCurrentConfig();

            // Send test email
            Mail::raw(
                "This is a test email from your Appy application.\n\n".
                "If you received this email, your SMTP configuration is working correctly.\n\n".
                "Configuration Details:\n".
                "- Mailer: {$this->mail_mailer}\n".
                ($this->mail_mailer === 'smtp' ? "- Host: {$this->mail_host}\n- Port: {$this->mail_port}\n- Encryption: {$this->mail_encryption}\n" : '').
                "- From: {$this->mail_from_name} <{$this->mail_from_address}>\n\n".
                'Sent at: '.now()->format('Y-m-d H:i:s'),
                function ($message) use ($testEmail, $testName) {
                    $message->to($testEmail, $testName)
                        ->subject('Test Email from Appy');
                }
            );

            $this->dispatch('show-toast',
                type: 'success',
                message: "Test email sent successfully to {$testEmail}!"
            );
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to send test email: '.$e->getMessage()
            );
        }
    }

    /**
     * Apply current form configuration to Laravel's mail config.
     */
    protected function applyCurrentConfig(): void
    {
        config([
            'mail.default' => $this->mail_mailer,
            'mail.from.name' => $this->mail_from_name,
            'mail.from.address' => $this->mail_from_address,
        ]);

        if ($this->mail_mailer === 'smtp') {
            config([
                'mail.mailers.smtp.host' => $this->mail_host,
                'mail.mailers.smtp.port' => (int) $this->mail_port,
                'mail.mailers.smtp.username' => $this->mail_username,
                'mail.mailers.smtp.password' => $this->mail_password,
                'mail.mailers.smtp.encryption' => $this->mail_encryption === 'none' ? null : $this->mail_encryption,
            ]);
        }

        // Purge the mail manager to use new config
        app()->forgetInstance('mail.manager');
    }

    public function render()
    {
        return view('livewire.admin.email-settings-form');
    }
}
