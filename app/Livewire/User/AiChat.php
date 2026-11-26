<?php

namespace App\Livewire\User;

use App\Models\AiConversation;
use App\Models\App;
use App\Models\SystemSetting;
use App\Services\AiChatService;
use Livewire\Attributes\On;
use Livewire\Component;

class AiChat extends Component
{
    public ?AiConversation $conversation = null;

    public string $message = '';

    public bool $isLoading = false;

    public array $displayMessages = [];

    public array $availablePlatforms = [];

    public bool $platformSelected = false;

    public array $providerInfo = [];

    public ?int $appId = null;

    public bool $streamingEnabled = false;

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

    public function mount(?int $conversationId = null, ?int $appId = null)
    {
        if (! $this->serviceAvailable) {
            return;
        }

        $this->appId = $appId;
        $this->providerInfo = $this->chatService->getProviderInfo();
        $this->availablePlatforms = $this->chatService->getAvailablePlatforms();
        $this->streamingEnabled = (bool) SystemSetting::get('ai_streaming_enabled', false);

        if ($conversationId) {
            $this->loadConversation($conversationId);
        } elseif ($appId) {
            // Load or create conversation for specific app
            $app = App::where('user_id', auth()->id())->find($appId);
            if ($app) {
                $this->startConversationForApp($app);
            }
        }
    }

    #[On('startNewConversation')]
    public function startNewConversation()
    {
        // Reset to welcome state (don't create conversation until first message)
        $this->conversation = null;
        $this->displayMessages = [];
        $this->platformSelected = false;
        $this->appId = null;

        // Update URL without full page reload (Alpine listener handles this)
        $this->dispatch('updateUrl', url: route('user.ai'));
    }

    #[On('conversationDeleted')]
    public function handleConversationDeleted(int $deletedId, bool $wasActive = false)
    {
        // Active conversation deletions use Livewire.navigate() to avoid hydration issues
        // This listener handles the edge case where a non-active conversation was being viewed
        if ($this->conversation && $this->conversation->id === $deletedId) {
            $this->conversation = null;
            $this->displayMessages = [];
            $this->platformSelected = false;
            $this->appId = null;
            $this->dispatch('updateUrl', url: route('user.ai'));
        }
    }

    public function startConversationForApp(App $app)
    {
        // Find existing conversation for this app or create new one
        $existing = AiConversation::where('user_id', auth()->id())
            ->where('app_id', $app->id)
            ->latest()
            ->first();

        if ($existing) {
            $this->loadConversation($existing->id);
        } else {
            $platformPlugin = $app->plugin;
            $this->conversation = $this->chatService->createConversation(
                $app->id,
                $platformPlugin?->id
            );
            $this->chatService->setAppContext($app);
            $this->platformSelected = true;
            $this->displayMessages = [];

            // Add welcome message for this app
            $this->displayMessages[] = [
                'role' => 'assistant',
                'content' => "I'm ready to help you with **{$app->name}**! You can ask me to:\n\n- Change the design (colors, theme)\n- Configure navigation tabs or drawer menu\n- Set up permissions\n- Create hosted pages (About, Contact, etc.)\n- Build your app\n\nWhat would you like to do?",
            ];
        }

        $this->dispatch('conversationStarted', conversationId: $this->conversation->id);
        // Also dispatch to browser/Alpine for sidebar visibility
        $this->js("window.dispatchEvent(new CustomEvent('conversation-started'))");
    }

    #[On('loadConversation')]
    public function loadConversation(int $conversationId)
    {
        $conversation = AiConversation::where('user_id', auth()->id())
            ->find($conversationId);

        if ($conversation) {
            $this->conversation = $conversation;
            $this->loadMessages();
            $this->platformSelected = true;

            // Set app context if conversation has an app
            if ($conversation->app_id) {
                $app = App::find($conversation->app_id);
                if ($app) {
                    $this->chatService->setAppContext($app);
                }
            }
        }
    }

    private function loadMessages()
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

    public function sendMessage()
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
                message: 'You have reached your monthly AI token limit. Upgrade your plan or use your own API key.'
            );

            return;
        }

        // Create conversation if not exists
        if (! $this->conversation) {
            $this->conversation = $this->chatService->createConversation();
            $this->dispatch('conversationStarted', conversationId: $this->conversation->id);
            // Also dispatch to browser/Alpine for sidebar visibility
            $this->js("window.dispatchEvent(new CustomEvent('conversation-started'))");
        }

        // Ensure app context is set for platform-specific tool calls
        if ($this->conversation->app_id) {
            $app = App::find($this->conversation->app_id);
            if ($app) {
                $this->chatService->setAppContext($app);
            }
        }

        $userMessage = trim($this->message);
        $this->message = '';

        // Add user message to display immediately with animation flag
        $this->displayMessages[] = [
            'role' => 'user',
            'content' => $userMessage,
            'isNew' => true,
        ];

        // Scroll to bottom after user message
        $this->js('setTimeout(() => { const c = document.getElementById("messages-container"); if(c) c.scrollTo({top: c.scrollHeight, behavior: "smooth"}); }, 50)');

        // Use streaming if enabled - now supports tool calls
        $useStreaming = $this->streamingEnabled;

        if ($useStreaming) {
            $this->isLoading = true;

            // Add placeholder message for streaming
            $this->displayMessages[] = [
                'role' => 'assistant',
                'content' => '',
                'isStreaming' => true,
            ];

            // Trigger streaming via JavaScript
            $conversationId = $this->conversation->id;
            $escapedMessage = addslashes($userMessage);
            $this->js("
                setTimeout(() => {
                    if (window.aiStreaming) {
                        window.aiStreaming.startStream({$conversationId}, '{$escapedMessage}');
                    }
                }, 100);
            ");

            return;
        }

        // Non-streaming: regular API call with typewriter effect
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
                    'typewriter' => true, // Enable word-by-word animation
                ];

                // Refresh provider info (token count may have changed)
                $this->providerInfo = $this->chatService->getProviderInfo();
            }

            // Refresh conversation from database
            $this->conversation->refresh();

            // Notify sidebar to refresh
            $this->dispatch('conversationUpdated');
        } catch (\Exception $e) {
            $this->displayMessages[] = [
                'role' => 'assistant',
                'content' => 'Sorry, something went wrong. Please try again.',
            ];
        }

        $this->isLoading = false;

        // Scroll to bottom and refocus input after AI response
        $this->js('setTimeout(() => { const c = document.getElementById("messages-container"); if(c) c.scrollTo({top: c.scrollHeight, behavior: "smooth"}); document.getElementById("ai-chat-input")?.focus(); }, 100)');
    }

    public function refreshAfterStreaming()
    {
        // Reload conversation and messages from database
        if ($this->conversation) {
            $this->conversation->refresh();
            $this->loadMessages();
            $this->providerInfo = $this->chatService->getProviderInfo();
        }

        $this->isLoading = false;

        // Notify sidebar to refresh
        $this->dispatch('conversationUpdated');

        // Scroll to bottom and refocus input
        $this->js('setTimeout(() => { const c = document.getElementById("messages-container"); if(c) c.scrollTo({top: c.scrollHeight, behavior: "smooth"}); document.getElementById("ai-chat-input")?.focus(); }, 100)');
    }

    public function render()
    {
        return view('livewire.user.ai-chat');
    }
}
