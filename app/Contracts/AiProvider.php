<?php

namespace App\Contracts;

use App\DataTransferObjects\AiChatRequest;
use App\DataTransferObjects\AiChatResponse;

interface AiProvider
{
    /**
     * Get the provider identifier.
     */
    public function getProviderId(): string;

    /**
     * Get display name for the provider.
     */
    public function getName(): string;

    /**
     * Get available models for this provider.
     *
     * @return array<string, array{name: string, context_window: int, input_cost: float, output_cost: float}>
     */
    public function getAvailableModels(): array;

    /**
     * Get the default model for this provider.
     */
    public function getDefaultModel(): string;

    /**
     * Send a chat completion request.
     */
    public function chat(AiChatRequest $request): AiChatResponse;

    /**
     * Stream a chat completion request.
     *
     * @param  callable  $onChunk  Called with each text chunk as it arrives
     */
    public function streamChat(AiChatRequest $request, callable $onChunk): AiChatResponse;

    /**
     * Validate the API key format.
     */
    public function validateApiKey(string $apiKey): bool;

    /**
     * Test the API connection.
     */
    public function testConnection(string $apiKey): bool;
}
