<?php

namespace App\Livewire\User;

use App\Models\UserAiSettings;
use App\Services\AiProviderFactory;
use Livewire\Attributes\On;
use Livewire\Component;

class AiSettingsModal extends Component
{
    public bool $showModal = false;

    public string $preferred_provider = 'system';

    public ?string $preferred_model = null;

    public ?string $openai_api_key = null;

    public ?string $anthropic_api_key = null;

    public ?string $nvidia_api_key = null;

    public bool $canUseOwnKey = false;

    public array $availableProviders = [];

    public array $availableModels = [];

    public function mount()
    {
        $this->loadSettings();
    }

    #[On('open-ai-settings-modal')]
    public function open()
    {
        $this->loadSettings();
        $this->showModal = true;
    }

    private function loadSettings()
    {
        $user = auth()->user();
        $this->canUseOwnKey = $user->canUseOwnAiApiKey();

        $settings = $user->aiSettings;
        if ($settings) {
            $this->preferred_provider = $settings->preferred_provider;
            $this->preferred_model = $settings->preferred_model;
            $this->openai_api_key = $settings->openai_api_key ? '••••••••••••••••' : null;
            $this->anthropic_api_key = $settings->anthropic_api_key ? '••••••••••••••••' : null;
            $this->nvidia_api_key = $settings->nvidia_api_key ? '••••••••••••••••' : null;
        } else {
            $this->preferred_provider = 'system';
            $this->preferred_model = null;
            $this->openai_api_key = null;
            $this->anthropic_api_key = null;
            $this->nvidia_api_key = null;
        }

        $this->availableProviders = AiProviderFactory::getAvailableProviders();
        $this->loadModelsForProvider();
    }

    public function updatedPreferredProvider()
    {
        $this->loadModelsForProvider();
        $this->preferred_model = null;
    }

    private function loadModelsForProvider()
    {
        if ($this->preferred_provider === 'system') {
            $this->availableModels = [];

            return;
        }

        $this->availableModels = AiProviderFactory::getModelsForProvider($this->preferred_provider);
    }

    public function save()
    {
        if (! $this->canUseOwnKey) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Your plan does not allow using your own API keys. Please upgrade.'
            );

            return;
        }

        $user = auth()->user();
        $settings = $user->aiSettings ?? new UserAiSettings(['user_id' => $user->id]);

        $settings->preferred_provider = $this->preferred_provider;
        $settings->preferred_model = $this->preferred_provider !== 'system' ? $this->preferred_model : null;

        if ($this->preferred_provider === 'openai') {
            if ($this->openai_api_key && ! str_starts_with($this->openai_api_key, '•')) {
                $settings->openai_api_key = $this->openai_api_key;
            } elseif (! $this->openai_api_key) {
                $settings->openai_api_key = null;
            }
        }

        if ($this->preferred_provider === 'anthropic') {
            if ($this->anthropic_api_key && ! str_starts_with($this->anthropic_api_key, '•')) {
                $settings->anthropic_api_key = $this->anthropic_api_key;
            } elseif (! $this->anthropic_api_key) {
                $settings->anthropic_api_key = null;
            }
        }

        if ($this->preferred_provider === 'nvidia') {
            if ($this->nvidia_api_key && ! str_starts_with($this->nvidia_api_key, '•')) {
                $settings->nvidia_api_key = trim($this->nvidia_api_key);
            } elseif (! $this->nvidia_api_key) {
                $settings->nvidia_api_key = null;
            }
        }

        $settings->save();

        if ($settings->openai_api_key) {
            $this->openai_api_key = '••••••••••••••••';
        }
        if ($settings->anthropic_api_key) {
            $this->anthropic_api_key = '••••••••••••••••';
        }
        if ($settings->nvidia_api_key) {
            $this->nvidia_api_key = '••••••••••••••••';
        }

        $this->dispatch('show-toast',
            type: 'success',
            message: 'AI settings saved successfully'
        );

        $this->closeModal();
    }

    public function testOpenAiKey()
    {
        if (! $this->openai_api_key || str_starts_with($this->openai_api_key, '•')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Please enter a new API key to test'
            );

            return;
        }

        try {
            $provider = AiProviderFactory::createProvider('openai', $this->openai_api_key);
            $success = $provider->testConnection($this->openai_api_key);

            if ($success) {
                $this->dispatch('show-toast',
                    type: 'success',
                    message: 'OpenAI API key is valid!'
                );
            } else {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: 'Invalid OpenAI API key'
                );
            }
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Connection failed: '.$e->getMessage()
            );
        }
    }

    public function testAnthropicKey()
    {
        if (! $this->anthropic_api_key || str_starts_with($this->anthropic_api_key, '•')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Please enter a new API key to test'
            );

            return;
        }

        try {
            $provider = AiProviderFactory::createProvider('anthropic', $this->anthropic_api_key);
            $success = $provider->testConnection($this->anthropic_api_key);

            if ($success) {
                $this->dispatch('show-toast',
                    type: 'success',
                    message: 'Anthropic API key is valid!'
                );
            } else {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: 'Invalid Anthropic API key'
                );
            }
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Connection failed: '.$e->getMessage()
            );
        }
    }

    public function testNvidiaKey()
    {
        if (! $this->nvidia_api_key || str_starts_with($this->nvidia_api_key, '•')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Please enter a new API key to test'
            );

            return;
        }

        try {
            $key = trim($this->nvidia_api_key);
            $provider = AiProviderFactory::createProvider('nvidia', $key);
            $success = $provider->testConnection($key);

            $this->dispatch('show-toast',
                type: $success ? 'success' : 'error',
                message: $success ? 'NVIDIA API key is valid!' : 'Invalid NVIDIA API key'
            );
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Connection failed: '.$e->getMessage()
            );
        }
    }

    public function clearNvidiaKey()
    {
        $this->nvidia_api_key = null;
    }

    public function clearOpenAiKey()
    {
        $this->openai_api_key = null;
    }

    public function clearAnthropicKey()
    {
        $this->anthropic_api_key = null;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.user.ai-settings-modal');
    }
}
