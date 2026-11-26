<?php

namespace App\DataTransferObjects;

class AiChatRequest
{
    public function __construct(
        public array $messages,
        public string $model,
        public array $tools = [],
        public string $toolChoice = 'auto',
        public int $maxTokens = 4096,
        public float $temperature = 0.7,
        public ?string $systemPrompt = null,
    ) {}
}
