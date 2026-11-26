<div class="bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">AI Chat Assistant</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure AI-powered app creation assistant</p>
    </div>

    <form wire:submit="save" class="p-6 space-y-4">
        <!-- Enable AI Chat -->
        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">Enable AI Chat Assistant</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Allow users to create and configure apps via AI conversation</p>
            </div>
            <x-toggle wire-model="ai_chat_enabled" />
        </div>

        <!-- Enable Streaming Responses -->
        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">Enable Streaming Responses</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Stream AI responses word-by-word for a more interactive experience</p>
                <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                    <svg class="inline w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    May not work on all shared hosting environments
                </p>
            </div>
            <x-toggle wire-model="ai_streaming_enabled" />
        </div>

        <!-- Provider Selection -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">AI Provider</label>
            <select
                wire:model.live="ai_provider"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                @foreach($availableProviders as $id => $provider)
                    <option value="{{ $id }}">{{ $provider['name'] }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Choose OpenAI (GPT-4.1), Anthropic (Claude) or NVIDIA (free trial models)
            </p>
            @error('ai_provider')
                <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </div>

        <!-- API Key -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">API Key</label>
            <div class="flex gap-2">
                <input
                    type="password"
                    wire:model="ai_provider_api_key"
                    placeholder="{{ $availableProviders[$ai_provider]['key_placeholder'] ?? 'Enter your API key' }}"
                    class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                @if(config('app.demo'))
                    <button type="button" disabled title="Disabled in demo mode" class="text-gray-900 bg-white border border-gray-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 inline-flex items-center cursor-not-allowed opacity-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Test Connection
                    </button>
                @else
                    <button
                        type="button"
                        wire:click="testConnection"
                        wire:loading.attr="disabled"
                        class="text-gray-900 bg-white border border-gray-300 focus:outline-hidden hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-4 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700 inline-flex items-center">
                        <svg wire:loading.remove wire:target="testConnection" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <svg wire:loading wire:target="testConnection" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="testConnection">Test Connection</span>
                        <span wire:loading wire:target="testConnection">Testing...</span>
                    </button>
                @endif
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                @if($ai_provider === 'openai')
                    Get your API key from <a href="https://platform.openai.com/api-keys" target="_blank" class="text-blue-600 hover:underline dark:text-blue-500">platform.openai.com</a>
                @elseif($ai_provider === 'nvidia')
                    Get a free key from <a href="https://build.nvidia.com/" target="_blank" class="text-blue-600 hover:underline dark:text-blue-500">build.nvidia.com</a> (open any model, then "Get API Key")
                @else
                    Get your API key from <a href="https://console.anthropic.com/settings/keys" target="_blank" class="text-blue-600 hover:underline dark:text-blue-500">console.anthropic.com</a>
                @endif
            </p>
            @error('ai_provider_api_key')
                <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </div>

        <!-- Default Model -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Default Model</label>
            <select
                wire:model="ai_default_model"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                @foreach($availableModels as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Recommended: Use a fast, cost-effective model for best balance
            </p>
            @error('ai_default_model')
                <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </div>

        <!-- Advanced Settings -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Advanced Settings</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Max Tokens -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Max Tokens per Request
                    </label>
                    <input
                        type="number"
                        wire:model="ai_max_tokens_per_request"
                        min="256"
                        max="16384"
                        step="256"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Maximum tokens in AI response (256-16384)
                    </p>
                    @error('ai_max_tokens_per_request')
                        <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Temperature -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Temperature
                    </label>
                    <input
                        type="number"
                        wire:model="ai_temperature"
                        min="0"
                        max="2"
                        step="0.1"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Response creativity (0 = focused, 2 = creative)
                    </p>
                    @error('ai_temperature')
                        <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Auto-Summarization Settings -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Auto-Summarization</h4>

            <!-- Enable Summarization -->
            <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Enable Auto-Summarization</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Automatically summarize older messages to reduce token usage and maintain context</p>
                </div>
                <x-toggle wire-model="ai_summarization_enabled" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4" x-show="$wire.ai_summarization_enabled" x-cloak>
                <!-- Message Threshold -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Message Threshold
                    </label>
                    <input
                        type="number"
                        wire:model="ai_summarization_threshold"
                        min="10"
                        max="100"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Summarize after this many messages
                    </p>
                    @error('ai_summarization_threshold')
                        <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Keep Recent Messages -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Keep Recent Messages
                    </label>
                    <input
                        type="number"
                        wire:model="ai_summarization_keep_recent"
                        min="2"
                        max="20"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Number of recent messages to keep
                    </p>
                    @error('ai_summarization_keep_recent')
                        <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Max Summary Length -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Max Summary Length
                    </label>
                    <input
                        type="number"
                        wire:model="ai_summarization_max_length"
                        min="100"
                        max="2000"
                        step="50"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Maximum characters for summary
                    </p>
                    @error('ai_summarization_max_length')
                        <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Chat Widget Settings -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Chat Widget</h4>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Widget Position</label>
                <select
                    wire:model="ai_widget_position"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    @foreach($widgetPositions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Position of the floating AI chat widget in the app editor
                </p>
                @error('ai_widget_position')
                    <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Submit Button -->
        @if(config('app.demo'))
            <button type="button" disabled title="Disabled in demo mode" class="text-white bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 cursor-not-allowed opacity-50">
                {{ __('Save') }}
            </button>
        @else
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-hidden dark:focus:ring-blue-800">
                {{ __('Save') }}
            </button>
        @endif
    </form>
</div>
