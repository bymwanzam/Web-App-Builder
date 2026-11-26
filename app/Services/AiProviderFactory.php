<?php

namespace App\Services;

use App\Contracts\AiProvider;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AiProviders\AnthropicProvider;
use App\Services\AiProviders\NvidiaProvider;
use App\Services\AiProviders\OpenAiProvider;

class AiProviderFactory
{
    /**
     * Get the appropriate AI provider for a user.
     */
    public static function forUser(User $user): AiProvider
    {
        $settings = $user->aiSettings;

        // Check if user can use their own key and has one configured
        if ($user->canUseOwnAiApiKey() && $settings) {
            $provider = $settings->preferred_provider;

            if ($provider !== 'system' && $settings->hasApiKeyFor($provider)) {
                return self::createProvider($provider, $settings->getApiKeyFor($provider));
            }
        }

        // Fall back to system provider
        return self::getSystemProvider();
    }

    /**
     * Get the system-configured AI provider.
     */
    public static function getSystemProvider(): AiProvider
    {
        $provider = SystemSetting::get('ai_provider', 'openai');
        $apiKey = SystemSetting::get('ai_provider_api_key');

        if (empty($apiKey)) {
            throw new \RuntimeException('AI provider API key not configured. Please configure it in Admin Settings.');
        }

        return self::createProvider($provider, $apiKey);
    }

    /**
     * Create a provider instance.
     */
    public static function createProvider(string $provider, string $apiKey): AiProvider
    {
        return match ($provider) {
            'openai' => new OpenAiProvider($apiKey),
            'anthropic' => new AnthropicProvider($apiKey),
            'nvidia' => new NvidiaProvider($apiKey),
            default => throw new \InvalidArgumentException("Unknown AI provider: {$provider}"),
        };
    }

    /**
     * Get all available providers.
     */
    public static function getAvailableProviders(): array
    {
        return [
            'openai' => [
                'id' => 'openai',
                'name' => 'OpenAI',
                'key_prefix' => 'sk-',
                'key_placeholder' => 'sk-...',
            ],
            'anthropic' => [
                'id' => 'anthropic',
                'name' => 'Anthropic (Claude)',
                'key_prefix' => 'sk-ant-',
                'key_placeholder' => 'sk-ant-...',
            ],
            'nvidia' => [
                'id' => 'nvidia',
                'name' => 'NVIDIA (build.nvidia.com)',
                'key_prefix' => '',
                'key_placeholder' => 'nvapi-...',
            ],
        ];
    }

    /**
     * Get available models for a provider.
     */
    public static function getModelsForProvider(string $provider): array
    {
        return match ($provider) {
            'openai' => [
                'gpt-4.1' => 'GPT-4.1',
                'gpt-4.1-mini' => 'GPT-4.1 Mini',
                'gpt-4.1-nano' => 'GPT-4.1 Nano',
                'gpt-4o' => 'GPT-4o',
                'gpt-4o-mini' => 'GPT-4o Mini',
            ],
            'anthropic' => [
                'claude-sonnet-4-6' => 'Claude Sonnet 4.6',
                'claude-opus-4-8' => 'Claude Opus 4.8',
                'claude-haiku-4-5' => 'Claude Haiku 4.5',
            ],
            'nvidia' => array_map(
                fn ($model) => $model['name'],
                (new NvidiaProvider('unused'))->getAvailableModels()
            ),
            default => [],
        };
    }
}
