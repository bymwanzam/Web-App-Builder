<?php

namespace App\Services\AiProviders;

use App\Contracts\AiProvider;
use App\DataTransferObjects\AiChatRequest;
use App\DataTransferObjects\AiChatResponse;
use ClaudePhp\ClaudePhp;

class AnthropicProvider implements AiProvider
{
    private ClaudePhp $client;

    public function __construct(string $apiKey)
    {
        $this->client = new ClaudePhp(apiKey: $apiKey);
    }

    public function getProviderId(): string
    {
        return 'anthropic';
    }

    public function getName(): string
    {
        return 'Anthropic (Claude)';
    }

    public function getAvailableModels(): array
    {
        return [
            'claude-opus-4-8' => [
                'name' => 'Claude Opus 4.8',
                'context_window' => 1000000,
                'input_cost' => 0.005,
                'output_cost' => 0.025,
            ],
            'claude-sonnet-4-6' => [
                'name' => 'Claude Sonnet 4.6',
                'context_window' => 1000000,
                'input_cost' => 0.003,
                'output_cost' => 0.015,
            ],
            'claude-haiku-4-5' => [
                'name' => 'Claude Haiku 4.5',
                'context_window' => 200000,
                'input_cost' => 0.001,
                'output_cost' => 0.005,
            ],
        ];
    }

    public function getDefaultModel(): string
    {
        return 'claude-sonnet-4-6';
    }

    public function chat(AiChatRequest $request): AiChatResponse
    {
        try {
            $messages = $this->convertMessages($request->messages);
            $systemPrompt = $this->extractSystemPrompt($request->messages);

            $params = [
                'model' => $request->model,
                'max_tokens' => $request->maxTokens,
                'messages' => $messages,
            ];

            if ($systemPrompt) {
                $params['system'] = $systemPrompt;
            }

            if (! empty($request->tools)) {
                $params['tools'] = $this->convertTools($request->tools);
            }

            $response = $this->client->messages()->create($params);

            $content = '';
            $toolCalls = [];

            foreach ($response->content as $block) {
                if ($block->type === 'text') {
                    $content .= $block->text;
                } elseif ($block->type === 'tool_use') {
                    $toolCalls[] = [
                        'id' => $block->id,
                        'type' => 'function',
                        'function' => [
                            'name' => $block->name,
                            'arguments' => json_encode($block->input),
                        ],
                    ];
                }
            }

            return new AiChatResponse(
                content: $content,
                toolCalls: $toolCalls,
                promptTokens: $response->usage->input_tokens ?? 0,
                completionTokens: $response->usage->output_tokens ?? 0,
                totalTokens: ($response->usage->input_tokens ?? 0) + ($response->usage->output_tokens ?? 0),
                model: $response->model,
                finishReason: $response->stop_reason ?? 'end_turn',
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
            $messages = $this->convertMessages($request->messages);
            $systemPrompt = $this->extractSystemPrompt($request->messages);

            $params = [
                'model' => $request->model,
                'max_tokens' => $request->maxTokens,
                'messages' => $messages,
                'stream' => true,
            ];

            if ($systemPrompt) {
                $params['system'] = $systemPrompt;
            }

            // Include tools if provided
            if (! empty($request->tools)) {
                $params['tools'] = $this->convertTools($request->tools);
            }

            $stream = $this->client->messages()->createStreamed($params);

            $fullContent = '';
            $model = $request->model;
            $finishReason = 'end_turn';
            $toolCalls = [];
            $currentToolCall = null;
            $currentToolIndex = -1;
            $inputTokens = 0;
            $outputTokens = 0;

            foreach ($stream as $event) {
                $eventType = $event->type ?? null;

                // Handle text content deltas
                if ($eventType === 'content_block_delta') {
                    $deltaType = $event->delta->type ?? null;

                    if ($deltaType === 'text_delta') {
                        $delta = $event->delta->text ?? '';
                        if ($delta !== '') {
                            $fullContent .= $delta;
                            $onChunk($delta);
                        }
                    } elseif ($deltaType === 'input_json_delta') {
                        // Accumulate tool call arguments
                        if ($currentToolCall !== null) {
                            $currentToolCall['function']['arguments'] .= $event->delta->partial_json ?? '';
                        }
                    }
                }

                // Handle content block start (for tool_use blocks)
                if ($eventType === 'content_block_start') {
                    $contentBlock = $event->content_block ?? null;
                    if ($contentBlock && ($contentBlock->type ?? null) === 'tool_use') {
                        $currentToolIndex++;
                        $currentToolCall = [
                            'id' => $contentBlock->id,
                            'type' => 'function',
                            'function' => [
                                'name' => $contentBlock->name,
                                'arguments' => '',
                            ],
                        ];
                    }
                }

                // Handle content block stop (finalize tool call)
                if ($eventType === 'content_block_stop' && $currentToolCall !== null) {
                    $toolCalls[] = $currentToolCall;
                    $currentToolCall = null;
                }

                // Capture message metadata and usage from message_start
                if ($eventType === 'message_start') {
                    if (isset($event->message->model)) {
                        $model = $event->message->model;
                    }
                    if (isset($event->message->usage->input_tokens)) {
                        $inputTokens = $event->message->usage->input_tokens;
                    }
                }

                // Capture final usage and stop reason from message_delta
                if ($eventType === 'message_delta') {
                    $finishReason = $event->delta->stop_reason ?? $finishReason;
                    if (isset($event->usage->output_tokens)) {
                        $outputTokens = $event->usage->output_tokens;
                    }
                }
            }

            return new AiChatResponse(
                content: $fullContent,
                toolCalls: $toolCalls,
                promptTokens: $inputTokens,
                completionTokens: $outputTokens,
                totalTokens: $inputTokens + $outputTokens,
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
        return str_starts_with($apiKey, 'sk-ant-') && strlen($apiKey) > 20;
    }

    public function testConnection(string $apiKey): bool
    {
        try {
            $testClient = new ClaudePhp(apiKey: $apiKey);
            $testClient->messages()->create([
                'model' => 'claude-haiku-4-5',
                'max_tokens' => 10,
                'messages' => [['role' => 'user', 'content' => 'Hi']],
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function convertMessages(array $messages): array
    {
        $converted = [];
        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                continue; // System goes in separate field
            }

            // Handle tool results
            if ($msg['role'] === 'tool') {
                $converted[] = [
                    'role' => 'user',
                    'content' => [[
                        'type' => 'tool_result',
                        'tool_use_id' => $msg['tool_call_id'],
                        'content' => $msg['content'],
                    ]],
                ];

                continue;
            }

            // Handle assistant messages with tool calls
            if ($msg['role'] === 'assistant' && ! empty($msg['tool_calls'])) {
                $contentBlocks = [];
                if (! empty($msg['content'])) {
                    $contentBlocks[] = ['type' => 'text', 'text' => $msg['content']];
                }
                foreach ($msg['tool_calls'] as $tc) {
                    $contentBlocks[] = [
                        'type' => 'tool_use',
                        'id' => $tc['id'],
                        'name' => $tc['function']['name'],
                        'input' => json_decode($tc['function']['arguments'], true) ?? [],
                    ];
                }
                $converted[] = [
                    'role' => 'assistant',
                    'content' => $contentBlocks,
                ];

                continue;
            }

            $converted[] = [
                'role' => $msg['role'] === 'assistant' ? 'assistant' : 'user',
                'content' => $msg['content'],
            ];
        }

        return $converted;
    }

    private function extractSystemPrompt(array $messages): ?string
    {
        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                return $msg['content'];
            }
        }

        return null;
    }

    private function convertTools(array $tools): array
    {
        $converted = [];
        foreach ($tools as $tool) {
            if ($tool['type'] === 'function') {
                $converted[] = [
                    'name' => $tool['function']['name'],
                    'description' => $tool['function']['description'] ?? '',
                    'input_schema' => $tool['function']['parameters'] ?? ['type' => 'object', 'properties' => []],
                ];
            }
        }

        return $converted;
    }
}
