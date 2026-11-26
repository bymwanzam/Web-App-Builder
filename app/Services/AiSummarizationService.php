<?php

namespace App\Services;

use App\Contracts\AiProvider;
use App\DataTransferObjects\AiChatRequest;
use App\Models\AiConversation;
use App\Models\SystemSetting;
use App\Models\User;

class AiSummarizationService
{
    private AiProvider $provider;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->provider = AiProviderFactory::forUser($user);
    }

    /**
     * Check if the conversation should be summarized based on message count.
     */
    public function shouldSummarize(AiConversation $conversation): bool
    {
        // Check if summarization is enabled
        if (! SystemSetting::get('ai_summarization_enabled', true)) {
            return false;
        }

        $threshold = (int) SystemSetting::get('ai_summarization_threshold', 20);
        $messages = $conversation->messages ?? [];

        // Count only user and assistant messages (not system or tool messages)
        $messageCount = count(array_filter(
            $messages,
            fn ($m) => in_array($m['role'] ?? '', ['user', 'assistant'])
        ));

        return $messageCount >= $threshold;
    }

    /**
     * Summarize older messages and update the conversation.
     */
    public function summarize(AiConversation $conversation): void
    {
        $keepRecent = (int) SystemSetting::get('ai_summarization_keep_recent', 6);
        $maxLength = (int) SystemSetting::get('ai_summarization_max_length', 500);
        $messages = $conversation->messages ?? [];

        if (count($messages) <= $keepRecent) {
            return;
        }

        // Split messages: older ones to summarize, recent ones to keep
        $messagesToSummarize = array_slice($messages, 0, -$keepRecent);
        $recentMessages = array_slice($messages, -$keepRecent);

        // Filter to only user/assistant messages for summarization
        $conversationMessages = array_filter(
            $messagesToSummarize,
            fn ($m) => in_array($m['role'] ?? '', ['user', 'assistant'])
        );

        if (empty($conversationMessages)) {
            return;
        }

        // Generate summary
        $summary = $this->generateSummary(array_values($conversationMessages), $maxLength);

        if (empty($summary)) {
            return;
        }

        // Combine with existing summary if present
        $existingSummary = $conversation->context_summary;
        if ($existingSummary) {
            $summary = $existingSummary."\n\n---\n\n".$summary;
            // Trim if too long (keep roughly double the max length for combined)
            if (strlen($summary) > $maxLength * 2) {
                $summary = $this->trimSummary($summary, $maxLength * 2);
            }
        }

        // Update conversation
        $conversation->update([
            'messages' => $recentMessages,
            'context_summary' => $summary,
        ]);
    }

    /**
     * Generate a summary of the given messages using the AI provider.
     */
    private function generateSummary(array $messages, int $maxLength): ?string
    {
        $conversationText = $this->formatMessagesForSummary($messages);

        $prompt = <<<PROMPT
Summarize the following conversation between a user and an AI assistant about mobile app configuration.

Focus on:
1. Key decisions made (colors, settings, features enabled/disabled)
2. Important context about the app (name, type, purpose)
3. Any configuration changes that were made
4. Unresolved issues or pending tasks

Keep the summary under {$maxLength} characters. Be concise but preserve critical information needed for context.

Conversation:
{$conversationText}

Summary:
PROMPT;

        try {
            $request = new AiChatRequest(
                messages: [
                    ['role' => 'user', 'content' => $prompt],
                ],
                model: $this->provider->getDefaultModel(),
                tools: [],
                maxTokens: 1000,
                temperature: 0.3, // Lower temperature for more focused summaries
            );

            $response = $this->provider->chat($request);

            if ($response->isError()) {
                return null;
            }

            return trim($response->content);
        } catch (\Exception $e) {
            // Log error but don't fail the conversation
            report($e);

            return null;
        }
    }

    /**
     * Format messages for the summary prompt.
     */
    private function formatMessagesForSummary(array $messages): string
    {
        $formatted = [];

        foreach ($messages as $message) {
            $role = ucfirst($message['role'] ?? 'unknown');
            $content = $message['content'] ?? '';

            // Truncate very long messages
            if (strlen($content) > 500) {
                $content = substr($content, 0, 500).'...';
            }

            $formatted[] = "{$role}: {$content}";
        }

        return implode("\n\n", $formatted);
    }

    /**
     * Trim a summary to approximately the target length.
     */
    private function trimSummary(string $summary, int $targetLength): string
    {
        if (strlen($summary) <= $targetLength) {
            return $summary;
        }

        // Try to cut at a sentence boundary
        $trimmed = substr($summary, 0, $targetLength);
        $lastPeriod = strrpos($trimmed, '.');
        $lastNewline = strrpos($trimmed, "\n");

        $cutPoint = max($lastPeriod, $lastNewline);
        if ($cutPoint && $cutPoint > $targetLength * 0.7) {
            return substr($summary, 0, $cutPoint + 1);
        }

        return $trimmed.'...';
    }
}
