<?php

namespace App\Livewire\Admin;

use App\Models\SystemSetting;
use App\Services\AiProviderFactory;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AiSettingsForm extends Component
{
    public bool $ai_chat_enabled = false;

    public bool $ai_streaming_enabled = false;

    public string $ai_provider = 'openai';

    public ?string $ai_provider_api_key = null;

    #[Validate('required|string')]
    public string $ai_default_model = 'gpt-4.1-mini';

    #[Validate('required|integer|min:256|max:16384')]
    public int $ai_max_tokens_per_request = 4096;

    #[Validate('required|numeric|min:0|max:2')]
    public float $ai_temperature = 0.7;

    // Summarization settings
    public bool $ai_summarization_enabled = true;

    #[Validate('required|integer|min:10|max:100')]
    public int $ai_summarization_threshold = 20;

    #[Validate('required|integer|min:2|max:20')]
    public int $ai_summarization_keep_recent = 6;

    #[Validate('required|integer|min:100|max:2000')]
    public int $ai_summarization_max_length = 500;

    // Widget settings
    #[Validate('required|string|in:bottom-right,bottom-left,top-right,top-left')]
    public string $ai_widget_position = 'bottom-right';

    public array $availableProviders = [];

    public array $availableModels = [];

    public array $widgetPositions = [
        'bottom-right' => 'Bottom Right',
        'bottom-left' => 'Bottom Left',
        'top-right' => 'Top Right',
        'top-left' => 'Top Left',
    ];

    public function mount()
    {
        $this->ai_chat_enabled = (bool) SystemSetting::get('ai_chat_enabled', false);
        $this->ai_streaming_enabled = (bool) SystemSetting::get('ai_streaming_enabled', false);
        $this->ai_provider = SystemSetting::get('ai_provider', 'openai');
        $this->ai_provider_api_key = SystemSetting::get('ai_provider_api_key');
        $this->ai_default_model = SystemSetting::get('ai_default_model', 'gpt-4.1-mini');
        $this->ai_max_tokens_per_request = (int) SystemSetting::get('ai_max_tokens_per_request', 4096);
        $this->ai_temperature = (float) SystemSetting::get('ai_temperature', 0.7);

        // Summarization settings
        $this->ai_summarization_enabled = (bool) SystemSetting::get('ai_summarization_enabled', true);
        $this->ai_summarization_threshold = (int) SystemSetting::get('ai_summarization_threshold', 20);
        $this->ai_summarization_keep_recent = (int) SystemSetting::get('ai_summarization_keep_recent', 6);
        $this->ai_summarization_max_length = (int) SystemSetting::get('ai_summarization_max_length', 500);

        // Widget settings
        $this->ai_widget_position = SystemSetting::get('ai_widget_position', 'bottom-right');

        // Clear sensitive fields in demo mode
        if (config('app.demo')) {
            $this->ai_provider_api_key = '';
        }

        // Load available providers and models
        $this->availableProviders = AiProviderFactory::getAvailableProviders();
        $this->loadModelsForProvider();
    }

    public function updatedAiProvider()
    {
        $this->loadModelsForProvider();

        // Reset to first model of new provider
        $models = array_keys($this->availableModels);
        $this->ai_default_model = $models[0] ?? '';
    }

    private function loadModelsForProvider()
    {
        $this->availableModels = AiProviderFactory::getModelsForProvider($this->ai_provider);
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
            'ai_provider' => 'required|string|in:openai,anthropic,nvidia',
            'ai_provider_api_key' => 'nullable|string|min:20',
            'ai_default_model' => 'required|string',
            'ai_max_tokens_per_request' => 'required|integer|min:256|max:16384',
            'ai_temperature' => 'required|numeric|min:0|max:2',
            'ai_summarization_threshold' => 'required|integer|min:10|max:100',
            'ai_summarization_keep_recent' => 'required|integer|min:2|max:20',
            'ai_summarization_max_length' => 'required|integer|min:100|max:2000',
            'ai_widget_position' => 'required|string|in:bottom-right,bottom-left,top-right,top-left',
        ]);

        // Core AI settings
        SystemSetting::set('ai_chat_enabled', $this->ai_chat_enabled);
        SystemSetting::set('ai_streaming_enabled', $this->ai_streaming_enabled);
        SystemSetting::set('ai_provider', $this->ai_provider);
        SystemSetting::set('ai_provider_api_key', $this->ai_provider_api_key);
        SystemSetting::set('ai_default_model', $this->ai_default_model);
        SystemSetting::set('ai_max_tokens_per_request', $this->ai_max_tokens_per_request);
        SystemSetting::set('ai_temperature', (string) $this->ai_temperature);

        // Summarization settings
        SystemSetting::set('ai_summarization_enabled', $this->ai_summarization_enabled);
        SystemSetting::set('ai_summarization_threshold', $this->ai_summarization_threshold);
        SystemSetting::set('ai_summarization_keep_recent', $this->ai_summarization_keep_recent);
        SystemSetting::set('ai_summarization_max_length', $this->ai_summarization_max_length);

        // Widget settings
        SystemSetting::set('ai_widget_position', $this->ai_widget_position);

        $this->dispatch('show-toast',
            type: 'success',
            message: 'AI settings saved successfully'
        );
    }

    public function testConnection()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Connection tests are disabled in demo mode.'
            );

            return;
        }

        if (empty($this->ai_provider_api_key)) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Please enter an API key first'
            );

            return;
        }

        try {
            $provider = AiProviderFactory::createProvider($this->ai_provider, $this->ai_provider_api_key);
            $success = $provider->testConnection($this->ai_provider_api_key);

            if ($success) {
                $this->dispatch('show-toast',
                    type: 'success',
                    message: 'Connection successful! API key is valid.'
                );
            } else {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: 'Invalid API key or connection failed'
                );
            }
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Connection failed: '.$e->getMessage()
            );
        }
    }

    public function render()
    {
        return view('livewire.admin.ai-settings-form');
    }
}
