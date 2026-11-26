<?php

namespace App\Services\AiProviders;

use OpenAI;

/**
 * NVIDIA API catalog (build.nvidia.com).
 *
 * The hosted endpoint speaks the OpenAI chat-completions protocol, so this
 * reuses OpenAiProvider and only changes the base URL, models and key checks.
 */
class NvidiaProvider extends OpenAiProvider
{
    public const BASE_URI = 'https://integrate.api.nvidia.com/v1';

    protected function makeClient(string $apiKey): \OpenAI\Client
    {
        return OpenAI::factory()
            ->withApiKey($apiKey)
            ->withBaseUri(self::BASE_URI)
            ->make();
    }

    public function getProviderId(): string
    {
        return 'nvidia';
    }

    public function getName(): string
    {
        return 'NVIDIA (build.nvidia.com)';
    }

    public function getAvailableModels(): array
    {
        // Costs are 0: the trial is free, so usage is tracked but not priced.
        return [
            'meta/llama-3.3-70b-instruct' => [
                'name' => 'Llama 3.3 70B Instruct',
                'context_window' => 128000,
                'input_cost' => 0,
                'output_cost' => 0,
            ],
            'openai/gpt-oss-120b' => [
                'name' => 'GPT-OSS 120B',
                'context_window' => 128000,
                'input_cost' => 0,
                'output_cost' => 0,
            ],
            'moonshotai/kimi-k2-instruct' => [
                'name' => 'Kimi K2 Instruct',
                'context_window' => 128000,
                'input_cost' => 0,
                'output_cost' => 0,
            ],
            'nvidia/llama-3.3-nemotron-super-49b-v1.5' => [
                'name' => 'Nemotron Super 49B v1.5',
                'context_window' => 128000,
                'input_cost' => 0,
                'output_cost' => 0,
            ],
            'qwen/qwen3-next-80b-a3b-instruct' => [
                'name' => 'Qwen3 Next 80B Instruct',
                'context_window' => 128000,
                'input_cost' => 0,
                'output_cost' => 0,
            ],
        ];
    }

    public function getDefaultModel(): string
    {
        return 'meta/llama-3.3-70b-instruct';
    }

    public function validateApiKey(string $apiKey): bool
    {
        return strlen(trim($apiKey)) > 20;
    }

    /**
     * NVIDIA does not document GET /v1/models, so test with a tiny chat call.
     */
    public function testConnection(string $apiKey): bool
    {
        try {
            $this->makeClient($apiKey)->chat()->create([
                'model' => $this->getDefaultModel(),
                'messages' => [['role' => 'user', 'content' => 'Hi']],
                'max_tokens' => 5,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::warning('NVIDIA connection test failed', ['error' => $e->getMessage()]);

            return false;
        }
    }
}
