<?php

namespace App\Livewire\User;

use App\Models\AiConversation;
use App\Models\App;
use App\Models\SystemSetting;
use App\Services\AiChatService;
use Livewire\Attributes\On;
use Livewire\Component;

class AiChatWidget extends Component
{
    public App $app;

    public ?AiConversation $conversation = null;

    public string $message = '';

    public bool $isLoading = false;

    public bool $isExpanded = false;

    public array $displayMessages = [];

    public array $providerInfo = [];

    public bool $streamingEnabled = false;

    public string $position = 'bottom-right';

    public bool $serviceAvailable = true;

    public string $serviceError = '';

    protected ?AiChatService $chatService = null;

    public function boot()
    {
        try {
            $this->chatService = new AiChatService(auth()->user());
            $this->serviceAvailable = true;
        } catch (\Exception $e) {
            $this->serviceAvailable = false;
            $this->serviceError = $e->getMessage();
        }
    }

    public function mount(App $app)
    {
        $this->app = $app;
        $this->position = SystemSetting::get('ai_widget_position', 'bottom-right');

        if (! $this->serviceAvailable) {
            return;
        }

        $this->providerInfo = $this->chatService->getProviderInfo();
        $this->streamingEnabled = (bool) SystemSetting::get('ai_streaming_enabled', false);

        $this->loadOrCreateConversation();
    }

    private function loadOrCreateConversation(): void
    {
        // Get or create widget conversation for this app
        $this->conversation = $this->chatService->getOrCreateWidgetConversation($this->app);
        $this->chatService->setAppContext($this->app);
        $this->loadMessages();

        // Add welcome message if conversation is empty
        if (empty($this->displayMessages)) {
            $this->addWelcomeMessage();
        }
    }

    private function addWelcomeMessage(): void
    {
        $this->displayMessages[] = [
            'role' => 'assistant',
            'content' => "Hi! I can help you configure **{$this->app->name}**. Ask me to change colors, add navigation items, configure permissions, or anything else!",
        ];
    }

    private function loadMessages(): void
    {
        if (! $this->conversation) {
            $this->displayMessages = [];

            return;
        }

        $messages = $this->conversation->messages ?? [];
        $this->displayMessages = [];

        foreach ($messages as $msg) {
            // Filter out system messages and tool results for display
            if ($msg['role'] === 'system' || $msg['role'] === 'tool') {
                continue;
            }

            // Filter out assistant messages with empty content (tool call placeholders)
            if ($msg['role'] === 'assistant' && empty(trim($msg['content'] ?? ''))) {
                continue;
            }

            $this->displayMessages[] = [
                'role' => $msg['role'],
                'content' => $msg['content'] ?? '',
            ];
        }
    }

    public function toggleExpand(): void
    {
        $this->isExpanded = ! $this->isExpanded;
    }

    public function sendMessage(): void
    {
        if (! $this->serviceAvailable || ! $this->chatService) {
            return;
        }

        if (empty(trim($this->message))) {
            return;
        }

        if (! $this->chatService->canChat()) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'You have reached your monthly AI token limit.'
            );

            return;
        }

        // Ensure conversation exists
        if (! $this->conversation) {
            $this->loadOrCreateConversation();
        }

        // Ensure app context is set
        $this->chatService->setAppContext($this->app);

        $userMessage = trim($this->message);
        $this->message = '';

        // Add user message to display
        $this->displayMessages[] = [
            'role' => 'user',
            'content' => $userMessage,
            'isNew' => true,
        ];

        // Scroll to bottom
        $this->js('setTimeout(() => { const c = document.getElementById("widget-messages"); if(c) c.scrollTo({top: c.scrollHeight, behavior: "smooth"}); }, 50)');

        if ($this->streamingEnabled) {
            $this->isLoading = true;

            // Add placeholder for streaming
            $this->displayMessages[] = [
                'role' => 'assistant',
                'content' => '',
                'isStreaming' => true,
            ];

            // Trigger streaming via JavaScript - properly escape the message
            $conversationId = $this->conversation->id;
            $escapedMessage = json_encode($userMessage);
            $this->js("
                setTimeout(() => {
                    if (window.widgetStreaming) {
                        window.widgetStreaming.startStream({$conversationId}, {$escapedMessage});
                    }
                }, 100);
            ");

            return;
        }

        // Non-streaming API call
        $this->isLoading = true;

        try {
            $response = $this->chatService->chat($this->conversation, $userMessage);

            if ($response->isError()) {
                $this->displayMessages[] = [
                    'role' => 'assistant',
                    'content' => 'Sorry, I encountered an error: '.$response->error,
                ];
            } else {
                $this->displayMessages[] = [
                    'role' => 'assistant',
                    'content' => $response->content,
                    'typewriter' => true,
                ];

                $this->providerInfo = $this->chatService->getProviderInfo();
            }

            $this->conversation->refresh();

            // Notify settings components to refresh (AI may have changed config)
            $this->dispatch('app-config-updated', appId: $this->app->id);
        } catch (\Exception $e) {
            $this->displayMessages[] = [
                'role' => 'assistant',
                'content' => 'Sorry, something went wrong. Please try again.',
            ];
        }

        $this->isLoading = false;

        // Scroll to bottom
        $this->js('setTimeout(() => { const c = document.getElementById("widget-messages"); if(c) c.scrollTo({top: c.scrollHeight, behavior: "smooth"}); }, 100)');
    }

    #[On('widgetStreamingComplete')]
    public function refreshAfterStreaming(): void
    {
        if ($this->conversation) {
            $this->conversation->refresh();
            $this->loadMessages();
            $this->providerInfo = $this->chatService->getProviderInfo();
        }

        $this->isLoading = false;

        // Notify settings components to refresh (AI may have changed config)
        $this->dispatch('app-config-updated', appId: $this->app->id);
    }

    public function clearConversation(): void
    {
        if ($this->conversation) {
            $this->conversation->delete();
        }

        // Create fresh conversation
        $this->conversation = $this->chatService->createWidgetConversation($this->app);
        $this->chatService->setAppContext($this->app);
        $this->displayMessages = [];
        $this->addWelcomeMessage();

        $this->dispatch('show-toast',
            type: 'success',
            message: 'Conversation cleared'
        );
    }

    public function getPositionClasses(): string
    {
        return match ($this->position) {
            'bottom-left' => 'bottom-6 left-6',
            'top-right' => 'top-20 right-6',
            'top-left' => 'top-20 left-6',
            default => 'bottom-6 right-6',
        };
    }

    public function render()
    {
        return view('livewire.user.ai-chat-widget');
    }
}
