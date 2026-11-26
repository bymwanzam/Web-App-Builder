<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class DemoAiSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Guard: Only run in demo mode with a valid OpenAI key
        if (! $this->shouldSeed()) {
            $this->command?->info('Skipping demo AI settings (APP_DEMO not true or APP_OPENAI_KEY not set)');

            return;
        }

        $this->command?->info('Seeding demo AI settings...');

        // Set OpenAI API key
        SystemSetting::set('ai_provider_api_key', env('APP_OPENAI_KEY'), 'string', 'ai');

        // Auto-configure AI settings
        SystemSetting::set('ai_chat_enabled', true, 'boolean', 'ai');
        SystemSetting::set('ai_provider', 'openai', 'string', 'ai');
        SystemSetting::set('ai_default_model', 'gpt-4.1-mini', 'string', 'ai');

        $this->command?->info('Demo AI settings seeded successfully.');
    }

    private function shouldSeed(): bool
    {
        return env('APP_DEMO') === true
            && ! empty(env('APP_OPENAI_KEY'));
    }
}
