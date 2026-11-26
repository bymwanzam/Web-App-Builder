<?php

namespace App\Services;

use App\Contracts\AiProvider;
use App\DataTransferObjects\AiChatRequest;
use App\DataTransferObjects\AiChatResponse;
use App\Models\AiConversation;
use App\Models\AiUsage;
use App\Models\App;
use App\Models\Plugin;
use App\Models\SystemSetting;
use App\Models\User;

class AiChatService
{
    private User $user;

    private AiProvider $provider;

    private bool $usingOwnKey;

    private ?App $currentApp = null;

    private ?object $currentPlatform = null;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->provider = AiProviderFactory::forUser($user);
        $this->usingOwnKey = $user->isUsingOwnAiApiKey();
    }

    /**
     * Check if user can chat (has quota remaining).
     */
    public function canChat(): bool
    {
        // Users with their own key bypass limits
        if ($this->usingOwnKey) {
            return true;
        }

        // Check plan permission
        if (! $this->user->canUseAiChat()) {
            return false;
        }

        // Check token quota
        return $this->user->getRemainingAiTokens() > 0;
    }

    /**
     * Check if user is using their own API key.
     */
    public function isUsingOwnKey(): bool
    {
        return $this->usingOwnKey;
    }

    /**
     * Get provider information for the UI.
     */
    public function getProviderInfo(): array
    {
        $plan = $this->user->plan;

        return [
            'provider' => $this->provider->getProviderId(),
            'providerName' => $this->provider->getName(),
            'model' => $this->getDefaultModel(),
            'usingOwnKey' => $this->usingOwnKey,
            'remainingTokens' => $this->usingOwnKey ? null : $this->user->getRemainingAiTokens(),
            'monthlyLimit' => $this->usingOwnKey ? null : ($plan ? $plan->getMonthlyAiTokens() : 0),
            'unlimitedTokens' => $plan ? $plan->hasUnlimitedAiTokens() : false,
        ];
    }

    /**
     * Get available platform plugins for app creation.
     */
    public function getAvailablePlatforms(): array
    {
        $platforms = Plugin::where('type', 'platform')
            ->where('status', 'active')
            ->get();

        return $platforms->map(fn ($plugin) => [
            'id' => $plugin->id,
            'slug' => $plugin->slug,
            'name' => $plugin->name,
        ])->toArray();
    }

    /**
     * Set the current app context for the conversation.
     */
    public function setAppContext(App $app): void
    {
        $this->currentApp = $app;
        $this->currentPlatform = $app->getPlatformInstance();
    }

    /**
     * Send a message and get a response.
     */
    public function chat(AiConversation $conversation, string $message): AiChatResponse
    {
        // Add user message to conversation
        $messages = $conversation->messages ?? [];
        $messages[] = ['role' => 'user', 'content' => $message];

        // Build the full messages array with system prompt
        $fullMessages = $this->buildMessagesWithContext($messages, $conversation);

        // Get tool definitions if we have an app context
        $tools = $this->getToolDefinitions($conversation);

        // Create the request
        $request = new AiChatRequest(
            messages: $fullMessages,
            model: $conversation->model ?? $this->getDefaultModel(),
            tools: $tools,
            toolChoice: ! empty($tools) ? 'auto' : null,
            maxTokens: (int) SystemSetting::get('ai_max_tokens_per_request', 4096),
            temperature: (float) SystemSetting::get('ai_temperature', 0.7),
        );

        // Make the API call
        $response = $this->provider->chat($request);

        // Handle errors
        if ($response->isError()) {
            return $response;
        }

        // Track usage (unless using own key)
        if (! $this->usingOwnKey) {
            $this->trackUsage($conversation, $response);
        }

        // Add assistant response to messages
        $assistantMessage = ['role' => 'assistant', 'content' => $response->content];
        if ($response->hasToolCalls()) {
            $assistantMessage['tool_calls'] = $response->toolCalls;
        }
        $messages[] = $assistantMessage;

        // Handle tool calls if present (with loop for chained tool calls)
        $maxToolIterations = 5; // Prevent infinite loops
        $iteration = 0;

        while ($response->hasToolCalls() && $iteration < $maxToolIterations) {
            $iteration++;

            $messages = $this->handleToolCalls($response->toolCalls, $messages, $conversation);

            // Get follow-up response after tool execution
            $fullMessages = $this->buildMessagesWithContext($messages, $conversation);
            $followUpRequest = new AiChatRequest(
                messages: $fullMessages,
                model: $conversation->model ?? $this->getDefaultModel(),
                tools: $tools,
                toolChoice: 'auto',
                maxTokens: (int) SystemSetting::get('ai_max_tokens_per_request', 4096),
                temperature: (float) SystemSetting::get('ai_temperature', 0.7),
            );

            $response = $this->provider->chat($followUpRequest);

            if ($response->isError()) {
                break;
            }

            if (! $this->usingOwnKey) {
                $this->trackUsage($conversation, $response);
            }

            // Add assistant response to messages
            $assistantMessage = ['role' => 'assistant', 'content' => $response->content];
            if ($response->hasToolCalls()) {
                $assistantMessage['tool_calls'] = $response->toolCalls;
            }
            $messages[] = $assistantMessage;
        }

        // Save updated conversation
        $conversation->update([
            'messages' => $messages,
            'total_tokens' => $conversation->total_tokens + $response->totalTokens,
            'model' => $response->model,
            'provider' => $this->provider->getProviderId(),
        ]);

        // Generate title if this is the first message
        if (count($messages) <= 2 && empty($conversation->title)) {
            $this->generateConversationTitle($conversation, $message);
        }

        // Check if auto-summarization is needed
        $this->checkAndSummarize($conversation);

        return $response;
    }

    /**
     * Stream a chat response with tool call support.
     */
    public function streamChat(AiConversation $conversation, string $message, callable $onChunk): AiChatResponse
    {
        // Add user message to conversation
        $messages = $conversation->messages ?? [];
        $messages[] = ['role' => 'user', 'content' => $message];

        // Build the full messages array with system prompt
        $fullMessages = $this->buildMessagesWithContext($messages, $conversation);

        // Get tool definitions
        $tools = $this->getToolDefinitions($conversation);

        // Create the request WITH tools
        $request = new AiChatRequest(
            messages: $fullMessages,
            model: $conversation->model ?? $this->getDefaultModel(),
            tools: $tools,
            toolChoice: ! empty($tools) ? 'auto' : null,
            maxTokens: (int) SystemSetting::get('ai_max_tokens_per_request', 4096),
            temperature: (float) SystemSetting::get('ai_temperature', 0.7),
        );

        // Make the streaming API call
        $response = $this->provider->streamChat($request, $onChunk);

        // Handle errors
        if ($response->isError()) {
            return $response;
        }

        // Track usage (unless using own key)
        if (! $this->usingOwnKey && $response->totalTokens > 0) {
            $this->trackUsage($conversation, $response);
        }

        // Add assistant response to messages
        $assistantMessage = ['role' => 'assistant', 'content' => $response->content];
        if ($response->hasToolCalls()) {
            $assistantMessage['tool_calls'] = $response->toolCalls;
        }
        $messages[] = $assistantMessage;

        // Handle tool calls if present (with loop for chained tool calls)
        $maxToolIterations = 5; // Prevent infinite loops
        $iteration = 0;

        while ($response->hasToolCalls() && $iteration < $maxToolIterations) {
            $iteration++;

            // Send a special marker for the frontend to show a nice working indicator
            $onChunk('[[TOOL_EXECUTING]]');

            $messages = $this->handleToolCalls($response->toolCalls, $messages, $conversation);

            // Get follow-up response
            $fullMessages = $this->buildMessagesWithContext($messages, $conversation);
            $followUpRequest = new AiChatRequest(
                messages: $fullMessages,
                model: $conversation->model ?? $this->getDefaultModel(),
                tools: $tools,
                toolChoice: 'auto',
                maxTokens: (int) SystemSetting::get('ai_max_tokens_per_request', 4096),
                temperature: (float) SystemSetting::get('ai_temperature', 0.7),
            );

            // Stream the follow-up response
            $response = $this->provider->streamChat($followUpRequest, $onChunk);

            if ($response->isError()) {
                break;
            }

            if (! $this->usingOwnKey && $response->totalTokens > 0) {
                $this->trackUsage($conversation, $response);
            }

            // Add assistant response to messages
            $assistantMessage = ['role' => 'assistant', 'content' => $response->content];
            if ($response->hasToolCalls()) {
                $assistantMessage['tool_calls'] = $response->toolCalls;
            }
            $messages[] = $assistantMessage;
        }

        // Save updated conversation
        $conversation->update([
            'messages' => $messages,
            'total_tokens' => $conversation->total_tokens + ($response->totalTokens ?? 0),
            'model' => $response->model,
            'provider' => $this->provider->getProviderId(),
        ]);

        // Generate title if this is the first message
        if (count($messages) <= 2 && empty($conversation->title)) {
            $this->generateConversationTitle($conversation, $message);
        }

        // Check if auto-summarization is needed
        $this->checkAndSummarize($conversation);

        return $response;
    }

    /**
     * Build messages array with system prompt.
     */
    private function buildMessagesWithContext(array $messages, AiConversation $conversation): array
    {
        $systemPrompt = $this->buildSystemPrompt($conversation);

        // Include context summary if available
        if (! empty($conversation->context_summary)) {
            $systemPrompt .= "\n\n## Previous Conversation Summary:\n".$conversation->context_summary;
        }

        return array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $messages
        );
    }

    /**
     * Build the system prompt with platform context.
     */
    private function buildSystemPrompt(AiConversation $conversation): string
    {
        // Use a more restrictive prompt for widget conversations
        if ($conversation->conversation_type === 'widget') {
            return $this->buildWidgetSystemPrompt($conversation);
        }

        $prompt = 'You are an AI assistant for an app builder platform. ';
        $prompt .= "Your ONLY purpose is to help users create, configure, and build apps.\n\n";

        $prompt .= "IMPORTANT RESTRICTIONS:\n";
        $prompt .= "- You MUST ONLY answer questions related to app building and configuration.\n";
        $prompt .= '- If a user asks about unrelated topics (general knowledge, trivia, coding unrelated to apps, etc.), ';
        $prompt .= "politely decline and redirect them to app-building topics.\n";
        $prompt .= "- Example response for off-topic questions: \"I'm here to help you build apps. ";
        $prompt .= "Is there anything I can help you with regarding your app project?\"\n\n";

        $prompt .= "What you CAN help with:\n";
        $prompt .= "- Creating new apps\n";
        $prompt .= "- Configuring app settings (design, colors, themes)\n";
        $prompt .= "- Setting up navigation\n";
        $prompt .= "- Platform-specific configurations\n";
        $prompt .= "- App permissions\n";
        $prompt .= "- Building and exporting apps\n";
        $prompt .= "- Push notifications setup\n";
        $prompt .= "- App icons and splash screens\n";
        $prompt .= "- Hosted pages (About, Contact, etc.)\n\n";

        // Add platform context if available
        if ($conversation->platform_plugin_id) {
            $plugin = Plugin::find($conversation->platform_plugin_id);
            if ($plugin) {
                $pluginConfig = $this->loadPluginConfig($plugin);
                if (isset($pluginConfig['ai_system_context'])) {
                    $prompt .= "Platform: {$plugin->name}\n";
                    $prompt .= $pluginConfig['ai_system_context']."\n\n";
                }
            }
        }

        // Add current app context if available
        if ($conversation->app_id && $this->currentApp) {
            if ($this->currentPlatform && method_exists($this->currentPlatform, 'getAiAppContext')) {
                $prompt .= $this->currentPlatform->getAiAppContext($this->currentApp)."\n\n";
            }
        }

        $prompt .= "## Modifying App Settings:\n";
        $prompt .= "To modify an app's settings (URL, design, navigation, etc.), you MUST:\n";
        $prompt .= "1. First call `switchToApp` with the app's ID or name to set the context\n";
        $prompt .= "2. Then call the platform-specific function (e.g., `setGeneralSettings`, `setDesignSettings`)\n\n";
        $prompt .= "Example: To change 'MyApp' URL to 'https://example.com':\n";
        $prompt .= "- Step 1: switchToApp(appName: 'MyApp')\n";
        $prompt .= "- Step 2: setGeneralSettings(url: 'https://example.com')\n\n";

        $prompt .= "Guidelines:\n";
        $prompt .= "- Be helpful and concise\n";
        $prompt .= "- When making changes, explain what you did\n";
        $prompt .= "- If something fails, explain why and suggest alternatives\n";
        $prompt .= "- Always confirm important actions before executing them\n";
        $prompt .= "- Stay focused on app-building tasks only\n";

        return $prompt;
    }

    /**
     * Build a restrictive system prompt for widget conversations.
     * Widget conversations are ONLY for the specific app being edited.
     */
    private function buildWidgetSystemPrompt(AiConversation $conversation): string
    {
        $appName = $this->currentApp?->name ?? 'this app';

        $prompt = "You are an AI assistant for configuring **{$appName}**.\n\n";

        $prompt .= "CRITICAL RESTRICTIONS:\n";
        $prompt .= "- You can ONLY help with THIS SPECIFIC APP ({$appName}).\n";
        $prompt .= "- You CANNOT access, list, or discuss any other apps the user may have.\n";
        $prompt .= "- You CANNOT create new apps from this widget.\n";
        $prompt .= "- If the user asks about other apps or wants to create new apps, tell them to use the main AI Assistant page.\n";
        $prompt .= "- If asked to list apps, say: \"I can only help with {$appName}. To manage other apps, please use the main AI Assistant.\"\n\n";

        $prompt .= "What you CAN help with for {$appName}:\n";
        $prompt .= "- Viewing and changing app settings (design, colors, theme)\n";
        $prompt .= "- Configuring navigation (tabs, drawer menu)\n";
        $prompt .= "- Setting up permissions\n";
        $prompt .= "- Configuring splash screen and icons\n";
        $prompt .= "- WebView settings\n";
        $prompt .= "- Custom code injection\n";
        $prompt .= "- Push notifications setup\n";
        $prompt .= "- Building the app\n";
        $prompt .= "- Hosted pages (About, Contact, etc.)\n\n";

        // Add platform context
        if ($conversation->platform_plugin_id) {
            $plugin = Plugin::find($conversation->platform_plugin_id);
            if ($plugin) {
                $pluginConfig = $this->loadPluginConfig($plugin);
                if (isset($pluginConfig['ai_system_context'])) {
                    $prompt .= "Platform: {$plugin->name}\n";
                    $prompt .= $pluginConfig['ai_system_context']."\n\n";
                }
            }
        }

        // Add current app context
        if ($this->currentApp && $this->currentPlatform && method_exists($this->currentPlatform, 'getAiAppContext')) {
            $prompt .= $this->currentPlatform->getAiAppContext($this->currentApp)."\n\n";
        }

        $prompt .= "Guidelines:\n";
        $prompt .= "- Be helpful and concise\n";
        $prompt .= "- Focus ONLY on {$appName}\n";
        $prompt .= "- When making changes, explain what you did\n";
        $prompt .= "- If something fails, explain why and suggest alternatives\n";

        return $prompt;
    }

    /**
     * Get tool definitions from plugin config.
     */
    private function getToolDefinitions(AiConversation $conversation): array
    {
        $tools = [];
        $isWidget = $conversation->conversation_type === 'widget';

        // Widget conversations only get app-specific tools, not global app management
        if (! $isWidget) {
            // Add global functions for app management (main AI page only)
            $tools[] = [
                'type' => 'function',
                'function' => [
                    'name' => 'listApps',
                    'description' => 'List all apps owned by the user with their basic information',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => new \stdClass,
                    ],
                ],
            ];

            $tools[] = [
                'type' => 'function',
                'function' => [
                    'name' => 'searchApp',
                    'description' => 'Search for apps by name pattern',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'query' => [
                                'type' => 'string',
                                'description' => 'Search query to match against app names (case-insensitive, min 2 chars)',
                            ],
                        ],
                        'required' => ['query'],
                    ],
                ],
            ];

            $tools[] = [
                'type' => 'function',
                'function' => [
                    'name' => 'switchToApp',
                    'description' => 'Switch the conversation context to a different app by ID or name',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'appId' => [
                                'type' => 'integer',
                                'description' => 'The app ID to switch to',
                            ],
                            'appName' => [
                                'type' => 'string',
                                'description' => 'The app name to switch to (used if appId not provided)',
                            ],
                        ],
                    ],
                ],
            ];

            $tools[] = [
                'type' => 'function',
                'function' => [
                    'name' => 'deleteApp',
                    'description' => 'Delete an app permanently. Use with caution.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'appId' => [
                                'type' => 'integer',
                                'description' => 'The app ID to delete (uses current app if not provided)',
                            ],
                            'confirmDelete' => [
                                'type' => 'boolean',
                                'description' => 'Must be true to confirm deletion',
                            ],
                        ],
                        'required' => ['confirmDelete'],
                    ],
                ],
            ];
        }

        // These tools are available for both main and widget (for current app)
        $tools[] = [
            'type' => 'function',
            'function' => [
                'name' => 'renameApp',
                'description' => 'Rename an existing app',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'appId' => [
                            'type' => 'integer',
                            'description' => 'The app ID to rename (uses current app if not provided)',
                        ],
                        'newName' => [
                            'type' => 'string',
                            'description' => 'The new name for the app',
                        ],
                    ],
                    'required' => ['newName'],
                ],
            ],
        ];

        $tools[] = [
            'type' => 'function',
            'function' => [
                'name' => 'getAppConfig',
                'description' => 'Get the current configuration and settings of an app including design, navigation, permissions, splashscreen, webview settings, and all other platform-specific settings. Use this when the user asks about an app\'s current settings or configuration.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'appId' => [
                            'type' => 'integer',
                            'description' => 'The app ID to get config for (uses current app context if not provided)',
                        ],
                    ],
                ],
            ],
        ];

        // Add global create app function if no app is selected (main AI page only)
        if (! $conversation->app_id && ! $isWidget) {
            // Get available platform slugs
            $platformSlugs = Plugin::where('type', 'platform')
                ->where('status', 'active')
                ->pluck('slug')
                ->toArray();

            $tools[] = [
                'type' => 'function',
                'function' => [
                    'name' => 'createApp',
                    'description' => 'Create a new mobile app with the specified name and platform',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => [
                                'type' => 'string',
                                'description' => 'The name of the app',
                            ],
                            'platform' => [
                                'type' => 'string',
                                'enum' => $platformSlugs,
                                'description' => 'The platform to use. Must be one of: '.implode(', ', $platformSlugs),
                            ],
                            'url' => [
                                'type' => 'string',
                                'description' => 'The website URL for WebView apps',
                            ],
                        ],
                        'required' => ['name', 'url'],
                    ],
                ],
            ];
        }

        // Add platform-specific functions if we have an app
        if ($conversation->platform_plugin_id) {
            $plugin = Plugin::find($conversation->platform_plugin_id);
            if ($plugin) {
                $pluginConfig = $this->loadPluginConfig($plugin);
                if (isset($pluginConfig['ai_functions']) && is_array($pluginConfig['ai_functions'])) {
                    foreach ($pluginConfig['ai_functions'] as $func) {
                        // Build parameters with proper structure
                        $parameters = $func['parameters'] ?? ['type' => 'object', 'properties' => new \stdClass];

                        // Ensure properties is always an object, not an array
                        // This prevents PHP from converting {} to [] which breaks OpenAI's schema validation
                        if (isset($parameters['properties']) && is_array($parameters['properties']) && empty($parameters['properties'])) {
                            $parameters['properties'] = new \stdClass;
                        }

                        $tools[] = [
                            'type' => 'function',
                            'function' => [
                                'name' => $func['name'],
                                'description' => $func['description'] ?? '',
                                'parameters' => $parameters,
                            ],
                        ];
                    }
                }
            }
        }

        return $tools;
    }

    /**
     * Handle tool calls from the AI response.
     */
    private function handleToolCalls(array $toolCalls, array $messages, AiConversation $conversation): array
    {
        foreach ($toolCalls as $toolCall) {
            $functionName = $toolCall['function']['name'];
            $arguments = json_decode($toolCall['function']['arguments'], true) ?? [];

            $result = $this->executeFunction($functionName, $arguments, $conversation);

            // Add tool result to messages
            $messages[] = [
                'role' => 'tool',
                'tool_call_id' => $toolCall['id'],
                'content' => json_encode($result),
            ];
        }

        return $messages;
    }

    /**
     * Execute a function call.
     */
    private function executeFunction(string $name, array $args, AiConversation $conversation): array
    {
        // Handle global functions
        return match ($name) {
            'createApp' => $this->handleCreateApp($args, $conversation),
            'listApps' => $this->handleListApps(),
            'searchApp' => $this->handleSearchApp($args),
            'switchToApp' => $this->handleSwitchToApp($args, $conversation),
            'renameApp' => $this->handleRenameApp($args, $conversation),
            'deleteApp' => $this->handleDeleteApp($args, $conversation),
            'getAppConfig' => $this->handleGetAppConfig($args, $conversation),
            default => $this->executePlatformFunction($name, $args),
        };
    }

    /**
     * Execute a platform-specific function.
     */
    private function executePlatformFunction(string $name, array $args): array
    {
        // Delegate to platform plugin
        if ($this->currentApp && $this->currentPlatform) {
            if (method_exists($this->currentPlatform, 'executeAiFunction')) {
                return $this->currentPlatform->executeAiFunction($name, $args, $this->currentApp);
            }
        }

        return ['success' => false, 'error' => "Unknown function: {$name}"];
    }

    /**
     * Handle the createApp function.
     */
    private function handleCreateApp(array $args, AiConversation $conversation): array
    {
        $name = $args['name'] ?? null;
        $platformSlug = $args['platform'] ?? 'android-webview';
        $url = $args['url'] ?? null;

        if (! $name) {
            return ['success' => false, 'error' => 'App name is required'];
        }

        // Normalize platform slug (handle common variations)
        $platformSlug = strtolower(trim($platformSlug));
        $platformSlug = str_replace(['_', ' '], '-', $platformSlug);

        // Handle common variations without hyphens
        $slugMappings = [
            'androidwebview' => 'android-webview',
            'android' => 'android-webview',
        ];
        $platformSlug = $slugMappings[$platformSlug] ?? $platformSlug;

        // Find the platform plugin
        $plugin = Plugin::where('slug', $platformSlug)->where('type', 'platform')->first();
        if (! $plugin) {
            return ['success' => false, 'error' => "Platform '{$platformSlug}' not found"];
        }

        // Create the app
        $app = App::create([
            'user_id' => $this->user->id,
            'platform_plugin_id' => $plugin->id,
            'name' => $name,
            'version_name' => '1.0.0',
            'version_code' => 1,
        ]);

        // Get platform instance and create config
        $platformInstance = $app->getPlatformInstance();
        if ($platformInstance) {
            $formData = ['url' => $url ?? 'https://example.com'];
            $platformInstance->createConfig($app, $formData);
        }

        // Update conversation with app context
        $conversation->update([
            'app_id' => $app->id,
            'platform_plugin_id' => $plugin->id,
        ]);

        // Set current app context
        $this->setAppContext($app);

        return [
            'success' => true,
            'message' => "App '{$name}' created successfully!",
            'app' => [
                'id' => $app->id,
                'name' => $app->name,
                'platform' => $platformSlug,
            ],
        ];
    }

    /**
     * Handle the listApps function.
     */
    private function handleListApps(): array
    {
        $apps = App::where('user_id', $this->user->id)
            ->with(['platformPlugin', 'androidWebViewConfig'])
            ->orderBy('updated_at', 'desc')
            ->get();

        if ($apps->isEmpty()) {
            return [
                'success' => true,
                'message' => 'No apps found. You can create a new app using the createApp function.',
                'apps' => [],
                'total' => 0,
            ];
        }

        return [
            'success' => true,
            'apps' => $apps->map(fn ($app) => [
                'id' => $app->id,
                'name' => $app->name,
                'platform' => $app->platformPlugin?->slug ?? 'unknown',
                'url' => $app->androidWebViewConfig?->url ?? null,
                'createdAt' => $app->created_at->diffForHumans(),
            ])->toArray(),
            'total' => $apps->count(),
        ];
    }

    /**
     * Handle the searchApp function.
     */
    private function handleSearchApp(array $args): array
    {
        $query = $args['query'] ?? null;

        if (! $query || strlen($query) < 2) {
            return ['success' => false, 'error' => 'Search query must be at least 2 characters'];
        }

        $apps = App::where('user_id', $this->user->id)
            ->where('name', 'like', "%{$query}%")
            ->with(['platformPlugin', 'androidWebViewConfig'])
            ->limit(10)
            ->get();

        if ($apps->isEmpty()) {
            return [
                'success' => true,
                'message' => "No apps found matching '{$query}'",
                'query' => $query,
                'results' => [],
                'count' => 0,
            ];
        }

        return [
            'success' => true,
            'query' => $query,
            'results' => $apps->map(fn ($app) => [
                'id' => $app->id,
                'name' => $app->name,
                'platform' => $app->platformPlugin?->slug ?? 'unknown',
                'url' => $app->androidWebViewConfig?->url ?? null,
            ])->toArray(),
            'count' => $apps->count(),
        ];
    }

    /**
     * Handle the switchToApp function.
     */
    private function handleSwitchToApp(array $args, AiConversation $conversation): array
    {
        $appId = $args['appId'] ?? null;
        $appName = $args['appName'] ?? null;

        if (! $appId && ! $appName) {
            return ['success' => false, 'error' => 'Either appId or appName is required'];
        }

        $query = App::where('user_id', $this->user->id);

        if ($appId) {
            $app = $query->find($appId);
        } else {
            $app = $query->where('name', 'like', "%{$appName}%")->first();
        }

        if (! $app) {
            return ['success' => false, 'error' => 'App not found'];
        }

        // Update conversation context
        $conversation->update([
            'app_id' => $app->id,
            'platform_plugin_id' => $app->platform_plugin_id,
        ]);

        // Set current app context
        $this->setAppContext($app);

        return [
            'success' => true,
            'message' => "Switched to app '{$app->name}'",
            'app' => [
                'id' => $app->id,
                'name' => $app->name,
                'platform' => $app->platformPlugin?->slug ?? 'unknown',
            ],
        ];
    }

    /**
     * Handle the renameApp function.
     */
    private function handleRenameApp(array $args, AiConversation $conversation): array
    {
        $appId = $args['appId'] ?? $conversation->app_id;
        $newName = $args['newName'] ?? null;

        if (! $newName) {
            return ['success' => false, 'error' => 'New name is required'];
        }

        if (strlen($newName) < 2 || strlen($newName) > 100) {
            return ['success' => false, 'error' => 'App name must be between 2 and 100 characters'];
        }

        if (! $appId) {
            return ['success' => false, 'error' => 'No app selected. Please specify an appId or switch to an app first.'];
        }

        $app = App::where('user_id', $this->user->id)->find($appId);

        if (! $app) {
            return ['success' => false, 'error' => 'App not found'];
        }

        $oldName = $app->name;
        $app->update(['name' => $newName]);

        return [
            'success' => true,
            'message' => "App renamed from '{$oldName}' to '{$newName}'",
            'app' => [
                'id' => $app->id,
                'name' => $app->name,
                'oldName' => $oldName,
            ],
        ];
    }

    /**
     * Handle the deleteApp function.
     */
    private function handleDeleteApp(array $args, AiConversation $conversation): array
    {
        $appId = $args['appId'] ?? $conversation->app_id;
        $confirmDelete = $args['confirmDelete'] ?? false;

        if (! $confirmDelete) {
            return ['success' => false, 'error' => 'You must set confirmDelete to true to delete an app. This action cannot be undone.'];
        }

        if (! $appId) {
            return ['success' => false, 'error' => 'No app selected. Please specify an appId or switch to an app first.'];
        }

        $app = App::where('user_id', $this->user->id)->find($appId);

        if (! $app) {
            return ['success' => false, 'error' => 'App not found'];
        }

        $appName = $app->name;

        // Delete the app (this should cascade delete related configs and builds)
        $app->delete();

        // Clear the conversation's app context if we deleted the current app
        if ($conversation->app_id === $appId) {
            $conversation->update([
                'app_id' => null,
                'platform_plugin_id' => null,
            ]);
            $this->currentApp = null;
            $this->currentPlatform = null;
        }

        return [
            'success' => true,
            'message' => "App '{$appName}' has been permanently deleted.",
        ];
    }

    /**
     * Handle the getAppConfig function - delegates to platform plugin.
     */
    private function handleGetAppConfig(array $args, AiConversation $conversation): array
    {
        $appId = $args['appId'] ?? $conversation->app_id;

        if (! $appId) {
            return ['success' => false, 'error' => 'No app specified. Please provide an appId or switch to an app first.'];
        }

        $app = App::where('user_id', $this->user->id)->find($appId);

        if (! $app) {
            return ['success' => false, 'error' => 'App not found'];
        }

        // Set app context for subsequent tool calls in the same request
        $this->setAppContext($app);

        // Update conversation context if not already set
        if (! $conversation->app_id) {
            $conversation->update([
                'app_id' => $app->id,
                'platform_plugin_id' => $app->platform_plugin_id,
            ]);
        }

        // Get the platform instance and delegate to it
        if ($this->currentPlatform && method_exists($this->currentPlatform, 'executeAiFunction')) {
            return $this->currentPlatform->executeAiFunction('getAppConfig', $args, $app);
        }

        // Fallback if platform doesn't support AI functions
        return [
            'success' => true,
            'app' => [
                'id' => $app->id,
                'name' => $app->name,
                'platform' => $app->platformPlugin?->name ?? 'Unknown',
                'created_at' => $app->created_at->format('Y-m-d H:i'),
            ],
            'message' => 'Detailed configuration not available for this platform.',
        ];
    }

    /**
     * Track token usage.
     */
    private function trackUsage(AiConversation $conversation, AiChatResponse $response): void
    {
        // Calculate estimated cost based on provider pricing
        $models = $this->provider->getAvailableModels();
        $modelInfo = $models[$response->model] ?? null;

        $estimatedCost = 0;
        if ($modelInfo) {
            $inputCost = ($response->promptTokens / 1000) * ($modelInfo['input_cost'] ?? 0);
            $outputCost = ($response->completionTokens / 1000) * ($modelInfo['output_cost'] ?? 0);
            $estimatedCost = $inputCost + $outputCost;
        }

        AiUsage::create([
            'user_id' => $this->user->id,
            'ai_conversation_id' => $conversation->id,
            'provider' => $this->provider->getProviderId(),
            'model' => $response->model,
            'prompt_tokens' => $response->promptTokens,
            'completion_tokens' => $response->completionTokens,
            'total_tokens' => $response->totalTokens,
            'estimated_cost' => $estimatedCost,
        ]);
    }

    /**
     * Generate a title for the conversation based on the first message.
     */
    private function generateConversationTitle(AiConversation $conversation, string $firstMessage): void
    {
        // Simple title generation: take first 50 chars of the message
        $title = mb_substr($firstMessage, 0, 50);
        if (mb_strlen($firstMessage) > 50) {
            $title .= '...';
        }

        $conversation->update(['title' => $title]);
    }

    /**
     * Get the default model for the current provider.
     */
    private function getDefaultModel(): string
    {
        $systemModel = SystemSetting::get('ai_default_model');
        if ($systemModel) {
            return $systemModel;
        }

        return $this->provider->getDefaultModel();
    }

    /**
     * Load plugin configuration from plugin.json.
     */
    private function loadPluginConfig(Plugin $plugin): array
    {
        $pluginPath = $this->getPluginPath($plugin);
        $configPath = $pluginPath.'/plugin.json';

        if (file_exists($configPath)) {
            $content = file_get_contents($configPath);

            return json_decode($content, true) ?? [];
        }

        return [];
    }

    /**
     * Get the plugin directory path.
     */
    private function getPluginPath(Plugin $plugin): string
    {
        $typeDir = match ($plugin->type) {
            'platform' => 'Platforms',
            'payment_gateway' => 'PaymentGateways',
            'bridge' => 'Bridges',
            default => ucfirst($plugin->type),
        };

        // Convert slug to directory name (e.g., 'android-webview' -> 'AndroidWebView')
        $dirName = str_replace(' ', '', ucwords(str_replace('-', ' ', $plugin->slug)));

        return app_path("Plugins/{$typeDir}/{$dirName}");
    }

    /**
     * Create a new conversation.
     */
    public function createConversation(?int $appId = null, ?int $platformPluginId = null): AiConversation
    {
        return AiConversation::create([
            'user_id' => $this->user->id,
            'app_id' => $appId,
            'platform_plugin_id' => $platformPluginId,
            'messages' => [],
            'provider' => $this->provider->getProviderId(),
            'model' => $this->getDefaultModel(),
        ]);
    }

    /**
     * Delete a conversation.
     */
    public function deleteConversation(AiConversation $conversation): bool
    {
        if ($conversation->user_id !== $this->user->id) {
            return false;
        }

        return $conversation->delete();
    }

    /**
     * Get user's conversations.
     */
    public function getConversations(int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return AiConversation::where('user_id', $this->user->id)
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Create a widget conversation for an app.
     * Widget conversations are hidden from the main AI page and have one per app.
     */
    public function createWidgetConversation(App $app): AiConversation
    {
        return AiConversation::create([
            'user_id' => $this->user->id,
            'app_id' => $app->id,
            'platform_plugin_id' => $app->platform_plugin_id,
            'conversation_type' => 'widget',
            'title' => 'App Assistant',
            'messages' => [],
            'provider' => $this->provider->getProviderId(),
            'model' => $this->getDefaultModel(),
        ]);
    }

    /**
     * Get or create the widget conversation for an app.
     */
    public function getOrCreateWidgetConversation(App $app): AiConversation
    {
        $conversation = AiConversation::where('user_id', $this->user->id)
            ->where('app_id', $app->id)
            ->where('conversation_type', 'widget')
            ->first();

        if (! $conversation) {
            $conversation = $this->createWidgetConversation($app);
        }

        return $conversation;
    }

    /**
     * Check if the conversation needs auto-summarization and perform it.
     */
    private function checkAndSummarize(AiConversation $conversation): void
    {
        try {
            $summarizer = new AiSummarizationService($this->user);

            if ($summarizer->shouldSummarize($conversation)) {
                $summarizer->summarize($conversation);
                // Refresh the conversation to get updated messages
                $conversation->refresh();
            }
        } catch (\Exception $e) {
            // Log error but don't fail the conversation
            report($e);
        }
    }
}
