<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'site_name', 'value' => 'Appy', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Build native mobile apps for your website', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Build native apps', 'type' => 'string', 'group' => 'general'],
            ['key' => 'logo_path', 'value' => null, 'type' => 'string', 'group' => 'general'],
            ['key' => 'logo_dark_path', 'value' => null, 'type' => 'string', 'group' => 'general'],
            ['key' => 'favicon_path', 'value' => null, 'type' => 'string', 'group' => 'general'],
            ['key' => 'default_theme', 'value' => 'system', 'type' => 'string', 'group' => 'general'],
            ['key' => 'enable_registration', 'value' => '1', 'type' => 'boolean', 'group' => 'general'],
            ['key' => 'landing_page_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'general'],

            // Email Configuration
            ['key' => 'mail_from_name', 'value' => 'Appy', 'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_from_address', 'value' => 'noreply@appy.com', 'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_mailer', 'value' => 'sendmail', 'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_host', 'value' => 'smtp.mailtrap.io', 'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_port', 'value' => '2525', 'type' => 'integer', 'group' => 'email'],
            ['key' => 'mail_username', 'value' => null, 'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_password', 'value' => null, 'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_encryption', 'value' => 'tls', 'type' => 'string', 'group' => 'email'],

            // Admin Notification Configuration
            ['key' => 'admin_notification_email', 'value' => null, 'type' => 'string', 'group' => 'email'],
            ['key' => 'admin_notification_events', 'value' => '["subscription_activated","payment_completed","payment_failed","subscription_cancelled","subscription_expired","bank_transfer_pending","user_registered","build_completed","build_failed"]', 'type' => 'json', 'group' => 'email'],

            // Build Configuration
            ['key' => 'default_plan_id', 'value' => '1', 'type' => 'integer', 'group' => 'app'],

            // Installation & Purchase
            ['key' => 'installation_completed', 'value' => app()->environment(['local', 'testing']) ? '1' : '0', 'type' => 'boolean', 'group' => 'app'],
            ['key' => 'purchase_code', 'value' => null, 'type' => 'string', 'group' => 'general'],

            // Social Login - Google
            ['key' => 'google_login_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'social'],
            ['key' => 'google_client_id', 'value' => null, 'type' => 'string', 'group' => 'social'],
            ['key' => 'google_client_secret', 'value' => null, 'type' => 'string', 'group' => 'social'],

            // Social Login - Facebook
            ['key' => 'facebook_login_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'social'],
            ['key' => 'facebook_client_id', 'value' => null, 'type' => 'string', 'group' => 'social'],
            ['key' => 'facebook_client_secret', 'value' => null, 'type' => 'string', 'group' => 'social'],

            // Social Login - GitHub
            ['key' => 'github_login_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'social'],
            ['key' => 'github_client_id', 'value' => null, 'type' => 'string', 'group' => 'social'],
            ['key' => 'github_client_secret', 'value' => null, 'type' => 'string', 'group' => 'social'],

            // Authentication Settings
            ['key' => 'require_email_verification', 'value' => '0', 'type' => 'boolean', 'group' => 'auth'],
            ['key' => 'recaptcha_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'auth'],
            ['key' => 'recaptcha_site_key', 'value' => null, 'type' => 'string', 'group' => 'auth'],
            ['key' => 'recaptcha_secret_key', 'value' => null, 'type' => 'string', 'group' => 'auth'],

            // Appetize.io Integration
            ['key' => 'appetize_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'appetize'],
            ['key' => 'appetize_api_token', 'value' => null, 'type' => 'string', 'group' => 'appetize'],
            ['key' => 'appetize_auto_upload', 'value' => '1', 'type' => 'boolean', 'group' => 'appetize'],
            ['key' => 'appetize_retention_days', 'value' => '7', 'type' => 'integer', 'group' => 'appetize'],
            ['key' => 'appetize_session_timeout', 'value' => '300', 'type' => 'integer', 'group' => 'appetize'],

            // AI Chat Settings
            ['key' => 'ai_chat_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'ai'],
            ['key' => 'ai_provider', 'value' => 'openai', 'type' => 'string', 'group' => 'ai'],
            ['key' => 'ai_provider_api_key', 'value' => null, 'type' => 'string', 'group' => 'ai'],
            ['key' => 'ai_default_model', 'value' => 'gpt-4.1-mini', 'type' => 'string', 'group' => 'ai'],
            ['key' => 'ai_max_tokens_per_request', 'value' => '4096', 'type' => 'integer', 'group' => 'ai'],
            ['key' => 'ai_temperature', 'value' => '0.7', 'type' => 'string', 'group' => 'ai'],

            // AI Summarization Settings
            ['key' => 'ai_summarization_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'ai'],
            ['key' => 'ai_summarization_threshold', 'value' => '20', 'type' => 'integer', 'group' => 'ai'],
            ['key' => 'ai_summarization_keep_recent', 'value' => '6', 'type' => 'integer', 'group' => 'ai'],
            ['key' => 'ai_summarization_max_length', 'value' => '500', 'type' => 'integer', 'group' => 'ai'],

            // AI Widget Settings
            ['key' => 'ai_widget_position', 'value' => 'bottom-right', 'type' => 'string', 'group' => 'ai'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
