<?php

namespace App\Services\AiProviders;

use App\Contracts\AiProvider;
use App\DataTransferObjects\AiChatRequest;
use App\DataTransferObjects\AiChatResponse;
use OpenAI;

class OpenAiProvider implements AiProvider
{
    protected \OpenAI\Client $client;

    public function __construct(string $apiKey)
    {
        $this->client = $this->makeClient($apiKey);
    }

    /**
     * Build the API client. Subclasses override this to target
     * OpenAI-compatible endpoints (e.g. NVIDIA).
     */
    protected function makeClient(string $apiKey): \OpenAI\Client
    {
        return OpenAI::client($apiKey);
    }

    public function getProviderId(): string
    {
        return 'openai';
    }

    public function getName(): string
    {
        return 'OpenAI';
    }

    public function getAvailableModels(): array
    {
        return [
            'gpt-4.1' => [
                'name' => 'GPT-4.1',
                'context_window' => 1000000,
                'input_cost' => 0.002,
                'output_cost' => 0.008,
            ],
            'gpt-4.1-mini' => [
                'name' => 'GPT-4.1 Mini',
                'context_window' => 1000000,
                'input_cost' => 0.0004,
                'output_cost' => 0.0016,
            ],
            'gpt-4.1-nano' => [
                'name' => 'GPT-4.1 Nano',
                'context_window' => 1000000,
                'input_cost' => 0.0001,
                'output_cost' => 0.0004,
            ],
            'gpt-4o' => [
                'name' => 'GPT-4o',
                'context_window' => 128000,
                'input_cost' => 0.005,
                'output_cost' => 0.015,
            ],
            'gpt-4o-mini' => [
                'name' => 'GPT-4o Mini',
                'context_window' => 128000,
                'input_cost' => 0.00015,
                'output_cost' => 0.0006,
            ],
        ];
    }

    public function getDefaultModel(): string
    {
        return 'gpt-4.1-mini';
    }

    public function chat(AiChatRequest $request): AiChatResponse
    {
        try {
            $params = [
                'model' => $request->model,
                'messages' => $request->messages,
                'max_tokens' => $request->maxTokens,
                'temperature' => $request->temperature,
            ];

            if (! empty($request->tools)) {
                $params['tools'] = $request->tools;
                $params['tool_choice'] = $request->toolChoice;
            }

            $response = $this->client->chat()->create($params);
            $choice = $response->choices[0];

            $toolCalls = [];
            if (! empty($choice->message->toolCalls)) {
                foreach ($choice->message->toolCalls as $tc) {
                    $toolCalls[] = [
                        'id' => $tc->id,
                        'type' => 'function',
                        'function' => [
                            'name' => $tc->function->name,
                            'arguments' => $tc->function->arguments,
                        ],
                    ];
                }
            }

            return new AiChatResponse(
                content: $choice->message->content ?? '',
                toolCalls: $toolCalls,
                promptTokens: $response->usage->promptTokens,
                completionTokens: $response->usage->completionTokens,
                totalTokens: $response->usage->totalTokens,
                model: $response->model,
                finishReason: $choice->finishReason ?? 'stop',
            );
        } catch (\Exception $e) {
            return new AiChatResponse(
                content: '',
                error: $e->getMessage(),
            );
        }
    }

    public function streamChat(AiChatRequest $request, callable $onChunk): AiChatResponse
    {
        try {
            $params = [
                'model' => $request->model,
                'messages' => $request->messages,
                'max_tokens' => $request->maxTokens,
                'temperature' => $request->temperature,
                'stream' => true,
                'stream_options' => ['include_usage' => true], // Enable usage tracking in streaming
            ];

            // Include tools if provided
            if (! empty($request->tools)) {
                $params['tools'] = $request->tools;
                $params['tool_choice'] = $request->toolChoice ?? 'auto';
            }

            $stream = $this->client->chat()->createStreamed($params);

            $fullContent = '';
            $model = $request->model;
            $finishReason = 'stop';
            $toolCallsAccumulator = []; // Accumulate tool call chunks
            $promptTokens = 0;
            $completionTokens = 0;

            foreach ($stream as $response) {
                // Capture usage from final chunk (when include_usage is enabled)
                if (isset($response->usage)) {
                    $promptTokens = $response->usage->promptTokens ?? 0;
                    $completionTokens = $response->usage->completionTokens ?? 0;
                }

                $choice = $response->choices[0] ?? null;
                if (! $choice) {
                    continue;
                }

                // Handle content delta
                $delta = $choice->delta->content ?? '';
                if ($delta !== '') {
                    $fullContent .= $delta;
                    $onChunk($delta);
                }

                // Handle tool call deltas (they come in chunks)
                if (! empty($choice->delta->toolCalls)) {
                    foreach ($choice->delta->toolCalls as $toolCallDelta) {
                        $index = $toolCallDelta->index;

                        // Initialize accumulator for this tool call
                        if (! isset($toolCallsAccumulator[$index])) {
                            $toolCallsAccumulator[$index] = [
                                'id' => '',
                                'type' => 'function',
                                'function' => [
                                    'name' => '',
                                    'arguments' => '',
                                ],
                            ];
                        }

                        // Accumulate the pieces
                        if (! empty($toolCallDelta->id)) {
                            $toolCallsAccumulator[$index]['id'] = $toolCallDelta->id;
                        }
                        if (! empty($toolCallDelta->function->name)) {
                            $toolCallsAccumulator[$index]['function']['name'] .= $toolCallDelta->function->name;
                        }
                        if (! empty($toolCallDelta->function->arguments)) {
                            $toolCallsAccumulator[$index]['function']['arguments'] .= $toolCallDelta->function->arguments;
                        }
                    }
                }

                // Capture finish reason
                if (! empty($choice->finishReason)) {
                    $finishReason = $choice->finishReason;
                }

                $model = $response->model ?? $model;
            }

            // Convert accumulated tool calls to array
            $toolCalls = array_values($toolCallsAccumulator);

            return new AiChatResponse(
                content: $fullContent,
                toolCalls: $toolCalls,
                promptTokens: $promptTokens,
                completionTokens: $completionTokens,
                totalTokens: $promptTokens + $completionTokens,
                model: $model,
                finishReason: $finishReason,
            );
        } catch (\Exception $e) {
            // Fallback to non-streaming if streaming fails
            $response = $this->chat($request);
            if (! $response->isError()) {
                $onChunk($response->content);
            }

            return $response;
        }
    }

    public function validateApiKey(string $apiKey): bool
    {
        return str_starts_with($apiKey, 'sk-') && strlen($apiKey) > 20;
    }

    public function testConnection(string $apiKey): bool
    {
        try {
            $client = OpenAI::client($apiKey);
            $client->models()->list();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
