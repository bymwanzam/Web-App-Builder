<div
    class="flex flex-col h-full"
    x-data="{
        soundMuted: localStorage.getItem('ai-chat-muted') === 'true',
        chatSound: null,
        initSound() {
            this.chatSound = new Audio('/sounds/chat-notification.ogg');
            this.chatSound.volume = 0.5;
        },
        toggleSound() {
            this.soundMuted = !this.soundMuted;
            localStorage.setItem('ai-chat-muted', this.soundMuted);
        },
        playSound() {
            if (!this.soundMuted && this.chatSound) {
                this.chatSound.currentTime = 0;
                this.chatSound.play().catch(() => {});
            }
        }
    }"
    x-init="initSound()"
    @ai-response-complete.window="playSound()"
>
    {{-- Service Unavailable Error --}}
    @if(!$serviceAvailable)
        <div class="flex flex-col items-center justify-center h-full text-center px-4 py-8">
            <div class="w-16 h-16 mx-auto mb-4 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">AI Assistant Unavailable</h2>
            <p class="text-gray-600 dark:text-gray-400 max-w-md mb-4">{{ $serviceError }}</p>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.settings') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Go to Settings
                </a>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-500">Please contact the administrator to configure AI settings.</p>
            @endif
        </div>
    @else

    <!-- Header with token info - Only show when there are messages -->
    @if(!empty($displayMessages))
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-blue-500 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.05 3.00002v5C7.33127 8.93351 5.05005 11.2392 5.05005 14.2c0 3.7555 3.13401 6.8 6.99995 6.8 3.866 0 7-3.0445 7-6.8 0-2.9608-2.2812-5.26649-5-6.19998v-5m-4 0h4m-4 0H8.05005m5.99995 0h2M5.09798 15H19.0021"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Assistant</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $providerInfo['providerName'] ?? 'AI' }}
                    @if($providerInfo['usingOwnKey'] ?? false)
                        <span class="text-green-600 dark:text-green-400">(Your API key)</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Token usage indicator -->
            @if(!($providerInfo['usingOwnKey'] ?? false) && !($providerInfo['unlimitedTokens'] ?? false))
                <div class="text-right">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Tokens remaining</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ number_format($providerInfo['remainingTokens'] ?? 0) }}
                        <span class="text-gray-400">/</span>
                        {{ number_format($providerInfo['monthlyLimit'] ?? 0) }}
                    </p>
                </div>
            @elseif($providerInfo['unlimitedTokens'] ?? false)
                <div class="text-right">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        Unlimited
                    </span>
                </div>
            @endif

            <!-- Sound toggle -->
            <button
                @click="toggleSound()"
                class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                :title="soundMuted ? 'Unmute notifications' : 'Mute notifications'"
            >
                <svg x-show="!soundMuted" x-cloak class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                </svg>
                <svg x-show="soundMuted" x-cloak class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
                </svg>
            </button>
        </div>
    </div>
    @endif

    <!-- Messages Area -->
    <div
        class="flex-1 overflow-y-auto p-4 space-y-4"
        id="messages-container"
        x-data="{
            scrollToBottom() {
                this.$el.scrollTo({ top: this.$el.scrollHeight, behavior: 'smooth' });
            }
        }"
        x-init="$nextTick(() => $el.scrollTop = $el.scrollHeight)"
        @scroll-to-bottom.window="$nextTick(() => scrollToBottom())"
        @conversation-updated.window="setTimeout(() => scrollToBottom(), 100)"
    >
        @if(empty($displayMessages))
            <!-- Enhanced Welcome State -->
            <div
                class="flex flex-col items-center justify-center h-full text-center px-4 py-8"
                x-data="{ ready: false }"
                x-init="setTimeout(() => ready = true, 100)"
            >
                <!-- Welcome Content (no redundant card wrapper) -->
                <div
                    class="relative w-full max-w-2xl mx-auto"
                    x-show="ready"
                    x-transition:enter="transition-all ease-out duration-500"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                >
                    {{-- Settings button for welcome screen (only when sidebar is hidden) --}}
                    @if(auth()->user()->plan?->allowsUserAiApiKey())
                    <button
                        wire:click="$dispatch('open-ai-settings-modal')"
                        class="absolute top-0 right-0 p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                        title="AI Settings"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                    @endif

                    <!-- AI Icon with Gradient -->
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-purple-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/25 animate-float">
                        <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.05 3.00002v5C7.33127 8.93351 5.05005 11.2392 5.05005 14.2c0 3.7555 3.13401 6.8 6.99995 6.8 3.866 0 7-3.0445 7-6.8 0-2.9608-2.2812-5.26649-5-6.19998v-5m-4 0h4m-4 0H8.05005m5.99995 0h2M5.09798 15H19.0021"/>
                        </svg>
                    </div>

                    <!-- Welcome Text -->
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">
                        How can I help you today?
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto mb-8">
                        I can help you create and configure mobile apps. Tell me what you'd like to build!
                    </p>

                    <!-- Prominent Input Area (Welcome State) -->
                    <form wire:submit="sendMessage" class="mb-8">
                        <div class="relative">
                            <input
                                type="text"
                                wire:model="message"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50"
                                placeholder="Describe your app idea..."
                                class="w-full px-6 py-4 text-base md:text-lg bg-gray-50 dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:focus:ring-purple-400 dark:focus:border-purple-400 transition-all duration-200 pr-14 dark:text-white dark:placeholder-gray-400"
                            >
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="absolute right-2 top-1/2 -translate-y-1/2 w-12 h-12 bg-gradient-to-br from-purple-500 to-blue-600 text-white rounded-xl flex items-center justify-center hover:shadow-lg hover:shadow-purple-500/25 transition-all duration-200 hover:scale-105 focus:ring-4 focus:ring-purple-300 dark:focus:ring-purple-800 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <!-- Normal send icon -->
                                <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                                </svg>
                                <!-- Loading spinner -->
                                <svg wire:loading class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>

                    <!-- Quick Action Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <button
                            wire:click="$set('message', 'I want to create a new app for my website')"
                            class="group p-4 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/20 border border-gray-200 dark:border-gray-600 hover:border-purple-300 dark:hover:border-purple-700 transition-all duration-200 text-left hover:scale-[1.02]"
                        >
                            <div class="w-8 h-8 mb-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center group-hover:bg-purple-200 dark:group-hover:bg-purple-800/30 transition-colors">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Create App</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Start a new project</p>
                        </button>

                        <button
                            wire:click="$set('message', 'Help me configure my app\'s navigation')"
                            class="group p-4 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 border border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-700 transition-all duration-200 text-left hover:scale-[1.02]"
                        >
                            <div class="w-8 h-8 mb-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-800/30 transition-colors">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Navigation</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tabs & menus</p>
                        </button>

                        <button
                            wire:click="$set('message', 'Help me customize my app\'s design')"
                            class="group p-4 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-pink-50 dark:hover:bg-pink-900/20 border border-gray-200 dark:border-gray-600 hover:border-pink-300 dark:hover:border-pink-700 transition-all duration-200 text-left hover:scale-[1.02]"
                        >
                            <div class="w-8 h-8 mb-2 bg-pink-100 dark:bg-pink-900/30 rounded-lg flex items-center justify-center group-hover:bg-pink-200 dark:group-hover:bg-pink-800/30 transition-colors">
                                <svg class="w-4 h-4 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Design</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Colors & styling</p>
                        </button>

                        <button
                            wire:click="$set('message', 'I want to build my app')"
                            class="group p-4 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-green-50 dark:hover:bg-green-900/20 border border-gray-200 dark:border-gray-600 hover:border-green-300 dark:hover:border-green-700 transition-all duration-200 text-left hover:scale-[1.02]"
                        >
                            <div class="w-8 h-8 mb-2 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-800/30 transition-colors">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Build</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Export your app</p>
                        </button>
                    </div>
                </div>
            </div>
        @else
            <!-- Messages with animations -->
            @foreach($displayMessages as $index => $msg)
                <div
                    wire:key="msg-{{ $index }}"
                    @if($msg['isStreaming'] ?? false) id="streaming-message" @endif
                    class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }} {{ $msg['isNew'] ?? false ? ($msg['role'] === 'user' ? 'animate-slide-in-right' : 'animate-slide-in-left') : '' }}"
                >
                    @if($msg['role'] === 'assistant')
                        <!-- AI Avatar -->
                        <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-purple-500 to-blue-500 rounded-lg flex items-center justify-center mr-3 mt-1 {{ ($msg['isStreaming'] ?? false) ? 'animate-pulse' : '' }}">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.05 3.00002v5C7.33127 8.93351 5.05005 11.2392 5.05005 14.2c0 3.7555 3.13401 6.8 6.99995 6.8 3.866 0 7-3.0445 7-6.8 0-2.9608-2.2812-5.26649-5-6.19998v-5m-4 0h4m-4 0H8.05005m5.99995 0h2M5.09798 15H19.0021"/>
                            </svg>
                        </div>
                    @endif

                    <div class="max-w-[75%] {{ $msg['role'] === 'user' ? 'bg-gradient-to-br from-purple-500 to-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' }} rounded-2xl px-4 py-3 shadow-xs">
                        @if($msg['role'] === 'assistant')
                            @if($msg['isStreaming'] ?? false)
                                {{-- Streaming placeholder --}}
                                <div id="streaming-content" class="prose prose-sm dark:prose-invert max-w-none prose-p:my-2 prose-ul:my-2 prose-ol:my-2 prose-li:my-0.5 prose-headings:my-2">
                                    <span class="inline-flex gap-1">
                                        <span class="w-2 h-2 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                                        <span class="w-2 h-2 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                                        <span class="w-2 h-2 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                                    </span>
                                </div>
                            @elseif($msg['typewriter'] ?? false)
                                {{-- Typewriter effect for non-streaming --}}
                                <div
                                    x-data="typewriter(@js($msg['content']))"
                                    x-init="start()"
                                    class="prose prose-sm dark:prose-invert max-w-none prose-p:my-2 prose-ul:my-2 prose-ol:my-2 prose-li:my-0.5 prose-headings:my-2"
                                >
                                    <span x-html="displayedHtml"></span>
                                    <span x-show="!finished" class="inline-block w-0.5 h-4 bg-purple-500 animate-pulse ml-0.5 align-middle"></span>
                                </div>
                            @else
                                <div class="prose prose-sm dark:prose-invert max-w-none prose-p:my-2 prose-ul:my-2 prose-ol:my-2 prose-li:my-0.5 prose-headings:my-2">
                                    {!! \Illuminate\Support\Str::markdown($msg['content'], ['renderer' => ['soft_break' => "<br>\n"]]) !!}
                                </div>
                            @endif
                        @else
                            <p class="text-sm whitespace-pre-wrap">{{ $msg['content'] }}</p>
                        @endif
                    </div>

                    @if($msg['role'] === 'user')
                        <!-- User Avatar -->
                        <div class="flex-shrink-0 w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-lg flex items-center justify-center ml-3 mt-1">
                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    @endif
                </div>
            @endforeach

            <!-- Enhanced Loading Indicator (only for non-streaming) -->
            @if($isLoading && !$streamingEnabled)
                <div class="flex justify-start animate-fade-in-up">
                    <!-- AI Avatar -->
                    <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-purple-500 to-blue-500 rounded-lg flex items-center justify-center mr-3 mt-1 animate-pulse">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.05 3.00002v5C7.33127 8.93351 5.05005 11.2392 5.05005 14.2c0 3.7555 3.13401 6.8 6.99995 6.8 3.866 0 7-3.0445 7-6.8 0-2.9608-2.2812-5.26649-5-6.19998v-5m-4 0h4m-4 0H8.05005m5.99995 0h2M5.09798 15H19.0021"/>
                        </svg>
                    </div>

                    <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl px-4 py-3 shadow-xs">
                        <div class="flex items-center gap-3">
                            <!-- Gradient Bouncing Dots -->
                            <div class="flex gap-1.5">
                                <div class="w-2.5 h-2.5 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                                <div class="w-2.5 h-2.5 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                                <div class="w-2.5 h-2.5 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Thinking...</span>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    <!-- Input Area (When messages exist) -->
    @if(!empty($displayMessages))
    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
        <form wire:submit="sendMessage" class="flex gap-3 items-center">
            <div class="flex-1 relative">
                <input
                    type="text"
                    id="ai-chat-input"
                    wire:model="message"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50"
                    placeholder="Type your message..."
                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-full focus:ring-purple-500 focus:border-purple-500 px-5 py-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-all duration-200"
                >
            </div>
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="inline-flex items-center justify-center w-11 h-11 bg-gradient-to-br from-purple-500 to-blue-600 text-white rounded-full focus:ring-4 focus:ring-purple-300 dark:focus:ring-purple-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 hover:shadow-lg hover:shadow-purple-500/25 hover:scale-105"
            >
                <!-- Normal send icon -->
                <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                </svg>
                <!-- Loading spinner -->
                <svg wire:loading class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>
    </div>
    @endif

{{-- Typewriter effect for non-streaming responses --}}
@script
<script>
    Alpine.data('typewriter', (fullContent) => ({
        words: [],
        currentIndex: 0,
        displayedText: '',
        displayedHtml: '',
        finished: false,
        intervalId: null,

        start() {
            // Split content into words while preserving whitespace
            this.words = fullContent.split(/(\s+)/);
            this.currentIndex = 0;
            this.displayedText = '';
            this.finished = false;

            // Speed: ~30ms per word for natural reading pace
            const speed = 30;

            this.intervalId = setInterval(() => {
                if (this.currentIndex < this.words.length) {
                    this.displayedText += this.words[this.currentIndex];
                    this.currentIndex++;

                    // Convert markdown to HTML for display
                    this.displayedHtml = this.simpleMarkdown(this.displayedText);

                    // Scroll to bottom periodically
                    if (this.currentIndex % 10 === 0) {
                        this.scrollToBottom();
                    }
                } else {
                    this.finish();
                }
            }, speed);
        },

        finish() {
            if (this.intervalId) {
                clearInterval(this.intervalId);
                this.intervalId = null;
            }
            this.finished = true;
            this.displayedHtml = this.simpleMarkdown(fullContent);
            this.scrollToBottom();
            // Play notification sound
            window.dispatchEvent(new CustomEvent('ai-response-complete'));
        },

        scrollToBottom() {
            const container = document.getElementById('messages-container');
            if (container) {
                container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
            }
        },

        simpleMarkdown(text) {
            // Basic markdown conversion for display during typing
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`(.*?)`/g, '<code class="bg-gray-200 dark:bg-gray-600 px-1 rounded">$1</code>')
                .replace(/\n/g, '<br>');
        }
    }));
</script>
@endscript

@if($streamingEnabled)
@script
<script>
    (function() {
        function scrollToBottom() {
            var container = document.getElementById('messages-container');
            if (container) {
                container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
            }
        }

        function getWorkingIndicatorHtml() {
            return '<div class="flex items-center gap-3 py-2">' +
                '<div class="relative flex items-center justify-center w-8 h-8">' +
                    '<div class="absolute w-8 h-8 border-2 border-purple-500/30 rounded-full"></div>' +
                    '<div class="absolute w-8 h-8 border-2 border-transparent border-t-purple-500 rounded-full animate-spin"></div>' +
                    '<svg class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>' +
                    '</svg>' +
                '</div>' +
                '<div>' +
                    '<div class="text-sm font-medium text-gray-900 dark:text-white">Executing action</div>' +
                    '<div class="text-xs text-gray-500 dark:text-gray-400">Please wait...</div>' +
                '</div>' +
            '</div>';
        }

        window.aiStreaming = {
            isStreaming: false,
            streamingContent: '',
            isExecutingTool: false,

            startStream: function(conversationId, message) {
                var self = this;
                if (self.isStreaming || !conversationId) return;

                self.isStreaming = true;
                self.streamingContent = '';
                self.isExecutingTool = false;

                var contentEl = document.getElementById('streaming-content');

                fetch('/user/ai/' + conversationId + '/stream', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'text/event-stream',
                    },
                    body: JSON.stringify({ message: message }),
                })
                .then(function(response) {
                    if (!response.ok) throw new Error('Stream failed');
                    var reader = response.body.getReader();
                    var decoder = new TextDecoder();

                    function read() {
                        reader.read().then(function(result) {
                            if (result.done) {
                                self.finishStreaming();
                                return;
                            }
                            var chunk = decoder.decode(result.value);
                            var lines = chunk.split('\n');
                            lines.forEach(function(line) {
                                if (line.indexOf('data: ') === 0) {
                                    try {
                                        var data = JSON.parse(line.slice(6));
                                        if (data.content) {
                                            // Check for tool execution marker
                                            if (data.content === '[[TOOL_EXECUTING]]') {
                                                self.isExecutingTool = true;
                                                if (contentEl) {
                                                    contentEl.innerHTML = getWorkingIndicatorHtml();
                                                }
                                                scrollToBottom();
                                                return;
                                            }

                                            // If we were executing a tool, clear the indicator first
                                            if (self.isExecutingTool) {
                                                self.isExecutingTool = false;
                                                self.streamingContent = '';
                                            }

                                            self.streamingContent += data.content;
                                            // Update the streaming content element
                                            if (contentEl) {
                                                contentEl.innerHTML = self.streamingContent.replace(/\n/g, '<br>');
                                            }
                                            scrollToBottom();
                                        }
                                        if (data.done) {
                                            self.finishStreaming();
                                            return;
                                        }
                                        if (data.error) {
                                            console.error('Stream error:', data.error);
                                            if (contentEl) {
                                                contentEl.innerHTML = 'Error: ' + data.error;
                                            }
                                            self.finishStreaming();
                                            return;
                                        }
                                    } catch (e) {}
                                }
                            });
                            read();
                        });
                    }
                    read();
                })
                .catch(function(error) {
                    console.error('Streaming error:', error);
                    if (contentEl) {
                        contentEl.innerHTML = 'Sorry, streaming failed. Please try again.';
                    }
                    self.finishStreaming();
                });
            },

            finishStreaming: function() {
                this.isStreaming = false;
                this.isExecutingTool = false;
                // Play notification sound
                window.dispatchEvent(new CustomEvent('ai-response-complete'));
                // Refresh the component to get the final state from server
                $wire.call('refreshAfterStreaming');
            }
        };
    })();
</script>
@endscript
@endif
@endif {{-- End serviceAvailable check --}}
</div>
