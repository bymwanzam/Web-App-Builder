<?php

namespace App\DataTransferObjects;

class AiChatResponse
{
    public function __construct(
        public string $content,
        public array $toolCalls = [],
        public int $promptTokens = 0,
        public int $completionTokens = 0,
        public int $totalTokens = 0,
        public string $model = '',
        public string $finishReason = 'stop',
        public ?string $error = null,
    ) {}

    public function hasToolCalls(): bool
    {
        return ! empty($this->toolCalls);
    }

    public function isError(): bool
    {
        return $this->error !== null;
    }
}
