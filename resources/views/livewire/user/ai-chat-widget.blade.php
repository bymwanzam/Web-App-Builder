<div>
@if($serviceAvailable)
<div class="fixed {{ $this->getPositionClasses() }} z-50" x-data="{
    expanded: @entangle('isExpanded'),
    showClearConfirm: false,
    soundMuted: localStorage.getItem('widget-chat-muted') === 'true',
    chatSound: null,
    init() {
        this.chatSound = new Audio('/sounds/chat-notification.ogg');
        this.chatSound.volume = 0.5;
    },
    toggleSound() {
        this.soundMuted = !this.soundMuted;
        localStorage.setItem('widget-chat-muted', this.soundMuted);
    },
    playSound() {
        if (!this.soundMuted && this.chatSound) {
            this.chatSound.currentTime = 0;
            this.chatSound.play().catch(() => {});
        }
    },
    scrollToBottom() {
        setTimeout(() => {
            const container = document.getElementById('widget-messages');
            if (container) container.scrollTop = container.scrollHeight;
        }, 50);
    }
}" x-effect="if (expanded) scrollToBottom()" @widget-response-complete.window="playSound()">
    {{-- Expanded Chat Panel --}}
    <div
        x-show="expanded"
        x-cloak
        x-transition:enter="transition ease-out duration-300 origin-bottom-right"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200 origin-bottom-right"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute bottom-0 right-0 w-96 h-[32rem] sm:w-96 sm:h-[32rem] max-sm:fixed max-sm:inset-0 max-sm:w-full max-sm:h-full max-sm:rounded-none bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 flex flex-col overflow-hidden"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.05 3.00002v5C7.33127 8.93351 5.05005 11.2392 5.05005 14.2c0 3.7555 3.13401 6.8 6.99995 6.8 3.866 0 7-3.0445 7-6.8 0-2.9608-2.2812-5.26649-5-6.19998v-5m-4 0h4m-4 0H8.05005m5.99995 0h2M5.09798 15H19.0021"/>
                </svg>
                <span class="font-medium text-sm">Assistant</span>
            </div>
            <div class="flex items-center gap-1">
                {{-- Sound Toggle --}}
                <button
                    @click="toggleSound()"
                    class="p-1.5 hover:bg-white/20 rounded-lg transition-colors"
                    :title="soundMuted ? 'Unmute notifications' : 'Mute notifications'"
                >
                    <svg x-show="!soundMuted" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                    </svg>
                    <svg x-show="soundMuted" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
                    </svg>
                </button>
                {{-- Clear Button --}}
                <button
                    @click="showClearConfirm = true"
                    class="p-1.5 hover:bg-white/20 rounded-lg transition-colors"
                    title="Clear conversation"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
                {{-- Close Button --}}
                <button
                    @click="expanded = false"
                    class="p-1.5 hover:bg-white/20 rounded-lg transition-colors"
                    title="Close"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Clear Confirmation Overlay --}}
        <div
            x-show="showClearConfirm"
            x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 z-10 bg-black/50 flex items-center justify-center p-4"
            @click.self="showClearConfirm = false"
        >
            <div
                x-show="showClearConfirm"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-4 max-w-xs w-full"
                @click.stop
            >
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white text-sm">Clear conversation?</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">This cannot be undone.</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button
                        @click="showClearConfirm = false"
                        class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        @click="showClearConfirm = false; $wire.clearConversation()"
                        class="flex-1 px-3 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors"
                    >
                        Clear
                    </button>
                </div>
            </div>
        </div>

        {{-- Messages Area --}}
        <div id="widget-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900">
            @foreach($displayMessages as $index => $msg)
                @if($msg['role'] === 'user')
                    {{-- User Message --}}
                    <div class="flex justify-end {{ isset($msg['isNew']) ? 'animate-slide-in-right' : '' }}">
                        <div class="max-w-[85%] bg-blue-600 text-white rounded-2xl rounded-br-sm px-4 py-2 text-sm">
                            {{ $msg['content'] }}
                        </div>
                    </div>
                @elseif($msg['role'] === 'assistant')
                    {{-- Assistant Message --}}
                    <div class="flex justify-start {{ isset($msg['isNew']) ? 'animate-slide-in-left' : '' }}">
                        <div class="max-w-[85%] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl rounded-bl-sm px-4 py-2 text-sm text-gray-800 dark:text-gray-200">
                            @if(isset($msg['isStreaming']))
                                <div id="widget-streaming-content" class="prose prose-sm dark:prose-invert max-w-none min-h-[1.5em]">
                                    <span class="widget-typing-indicator">
                                        <span></span><span></span><span></span>
                                    </span>
                                </div>
                            @elseif(isset($msg['typewriter']))
                                <div x-data="widgetTypewriter(@js($msg['content']))" class="prose prose-sm dark:prose-invert max-w-none">
                                    <span x-html="displayedHtml"></span>
                                    <span x-show="!finished" class="widget-cursor"></span>
                                </div>
                            @else
                                <div class="prose prose-sm dark:prose-invert max-w-none">{!! \Illuminate\Support\Str::markdown($msg['content'], ['renderer' => ['soft_break' => "<br>\n"]]) !!}</div>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- Loading Indicator --}}
            @if($isLoading && !$streamingEnabled)
                <div class="flex justify-start animate-fade-in-up">
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl rounded-bl-sm px-4 py-3">
                        <span class="widget-typing-indicator">
                            <span></span><span></span><span></span>
                        </span>
                    </div>
                </div>
            @endif
        </div>

        {{-- Input Area --}}
        <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <form wire:submit="sendMessage" class="flex gap-2">
                <input
                    type="text"
                    wire:model="message"
                    id="widget-chat-input"
                    placeholder="Ask me anything..."
                    class="flex-1 text-sm bg-gray-100 dark:bg-gray-700 border-0 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 dark:text-white placeholder-gray-400 disabled:opacity-50"
                    @keydown.escape="expanded = false"
                    @if($isLoading) disabled @endif
                >
                <button
                    type="submit"
                    class="p-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    @if($isLoading) disabled @endif
                    wire:loading.attr="disabled"
                >
                    <svg wire:loading.remove wire:target="sendMessage" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                    </svg>
                    <svg wire:loading wire:target="sendMessage" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- Floating Action Button (when collapsed) --}}
    <button
        x-show="!expanded"
        x-cloak
        @click="expanded = true"
        x-transition:enter="transition ease-out duration-200 delay-100"
        x-transition:enter-start="opacity-0 scale-75"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-75"
        class="absolute bottom-0 right-0 w-14 h-14 bg-gradient-to-br from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-full shadow-lg hover:shadow-xl transition-shadow flex items-center justify-center group"
    >
        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.05 3.00002v5C7.33127 8.93351 5.05005 11.2392 5.05005 14.2c0 3.7555 3.13401 6.8 6.99995 6.8 3.866 0 7-3.0445 7-6.8 0-2.9608-2.2812-5.26649-5-6.19998v-5m-4 0h4m-4 0H8.05005m5.99995 0h2M5.09798 15H19.0021"/>
        </svg>
    </button>
</div>

{{-- Widget Scripts --}}
@script
<script>
    // Typewriter effect for widget (non-streaming responses)
    Alpine.data('widgetTypewriter', (text) => ({
        text: text,
        displayedHtml: '',
        finished: false,
        intervalId: null,
        init() {
            this.typeWrite();
        },
        typeWrite() {
            const words = this.text.split(/(\s+)/);
            let index = 0;
            this.finished = false;
            this.intervalId = setInterval(() => {
                if (index < words.length) {
                    const partialText = words.slice(0, index + 1).join('');
                    this.displayedHtml = this.simpleMarkdown(partialText);
                    index++;
                    if (index % 5 === 0) {
                        this.scrollToBottom();
                    }
                } else {
                    clearInterval(this.intervalId);
                    this.finished = true;
                    this.displayedHtml = this.simpleMarkdown(this.text);
                    this.scrollToBottom();
                    // Play notification sound
                    window.dispatchEvent(new CustomEvent('widget-response-complete'));
                    setTimeout(() => {
                        document.getElementById('widget-chat-input')?.focus();
                    }, 100);
                }
            }, 30); // Match main chat speed (30ms per word)
        },
        scrollToBottom() {
            const container = document.getElementById('widget-messages');
            if (container) {
                container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
            }
        },
        simpleMarkdown(text) {
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`(.*?)`/g, '<code class="bg-gray-200 dark:bg-gray-600 px-1 rounded text-xs">$1</code>')
                .replace(/\n/g, '<br>');
        },
        destroy() {
            if (this.intervalId) {
                clearInterval(this.intervalId);
            }
        }
    }));

    // Widget streaming handler
    (function() {
        function scrollToBottom() {
            const container = document.getElementById('widget-messages');
            if (container) {
                container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
            }
        }

        function getWorkingIndicatorHtml() {
            return '<span class="widget-typing-indicator working"><span></span><span></span><span></span></span>' +
                '<span class="text-xs text-gray-500 dark:text-gray-400 ml-2">Working...</span>';
        }

        function simpleMarkdown(text) {
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`(.*?)`/g, '<code class="bg-gray-200 dark:bg-gray-600 px-1 rounded text-xs">$1</code>')
                .replace(/\n/g, '<br>');
        }

        window.widgetStreaming = {
            isStreaming: false,
            streamingContent: '',
            isExecutingTool: false,
            abortController: null,

            startStream: function(conversationId, message) {
                const self = this;
                if (self.isStreaming || !conversationId) return;

                self.isStreaming = true;
                self.streamingContent = '';
                self.isExecutingTool = false;
                self.abortController = new AbortController();

                const contentEl = document.getElementById('widget-streaming-content');
                if (!contentEl) {
                    self.finishStreaming();
                    return;
                }

                fetch('/user/ai/' + conversationId + '/stream', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'text/event-stream',
                    },
                    body: JSON.stringify({ message: message }),
                    signal: self.abortController.signal
                })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Stream request failed: ' + response.status);
                    }
                    const reader = response.body.getReader();
                    const decoder = new TextDecoder();

                    function read() {
                        reader.read().then(function(result) {
                            if (result.done) {
                                self.finishStreaming();
                                return;
                            }

                            const chunk = decoder.decode(result.value, { stream: true });
                            const lines = chunk.split('\n');

                            lines.forEach(function(line) {
                                if (line.indexOf('data: ') === 0) {
                                    try {
                                        const data = JSON.parse(line.slice(6));

                                        if (data.done) {
                                            self.finishStreaming();
                                            return;
                                        }

                                        if (data.error) {
                                            contentEl.innerHTML = '<span class="text-red-500 text-xs">Error: ' + data.error + '</span>';
                                            self.finishStreaming();
                                            return;
                                        }

                                        if (data.content) {
                                            if (data.content === '[[TOOL_EXECUTING]]' || data.content.includes('[[TOOL_EXECUTING]]')) {
                                                self.isExecutingTool = true;
                                                contentEl.innerHTML = getWorkingIndicatorHtml();
                                                scrollToBottom();
                                                return;
                                            }

                                            if (self.isExecutingTool) {
                                                self.isExecutingTool = false;
                                                self.streamingContent = '';
                                            }

                                            self.streamingContent += data.content;
                                            contentEl.innerHTML = simpleMarkdown(self.streamingContent) +
                                                '<span class="widget-cursor"></span>';
                                            scrollToBottom();
                                        }
                                    } catch (e) {
                                        // Ignore JSON parse errors for partial chunks
                                    }
                                }
                            });

                            read();
                        }).catch(function(error) {
                            if (error.name !== 'AbortError') {
                                console.error('Read error:', error);
                            }
                            self.finishStreaming();
                        });
                    }

                    read();
                })
                .catch(function(error) {
                    if (error.name !== 'AbortError') {
                        console.error('Streaming error:', error);
                        if (contentEl) {
                            contentEl.innerHTML = '<span class="text-red-500 text-xs">Connection error. Please try again.</span>';
                        }
                    }
                    self.finishStreaming();
                });
            },

            finishStreaming: function() {
                this.isStreaming = false;
                this.abortController = null;
                // Play notification sound
                window.dispatchEvent(new CustomEvent('widget-response-complete'));
                Livewire.dispatch('widgetStreamingComplete');
                setTimeout(function() {
                    document.getElementById('widget-chat-input')?.focus();
                }, 100);
            },

            abort: function() {
                if (this.abortController) {
                    this.abortController.abort();
                }
            }
        };
    })();
</script>
@endscript

<style>
    /* Typing indicator - 3 bouncing dots */
    .widget-typing-indicator {
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
    .widget-typing-indicator span {
        width: 6px;
        height: 6px;
        background-color: #3b82f6;
        border-radius: 50%;
        animation: widget-bounce 1.4s ease-in-out infinite;
    }
    .widget-typing-indicator span:nth-child(1) { animation-delay: 0s; }
    .widget-typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .widget-typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

    .widget-typing-indicator.working span {
        background-color: #8b5cf6;
    }

    @keyframes widget-bounce {
        0%, 60%, 100% { transform: translateY(0); }
        30% { transform: translateY(-4px); }
    }

    /* Streaming cursor */
    .widget-cursor {
        display: inline-block;
        width: 2px;
        height: 1em;
        background-color: #3b82f6;
        margin-left: 2px;
        vertical-align: text-bottom;
        animation: widget-blink 0.8s ease-in-out infinite;
    }

    @keyframes widget-blink {
        0%, 50% { opacity: 1; }
        51%, 100% { opacity: 0; }
    }

    /* Message animations */
    @keyframes slide-in-right {
        from { opacity: 0; transform: translateX(1rem); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes slide-in-left {
        from { opacity: 0; transform: translateX(-1rem); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes fade-in-up {
        from { opacity: 0; transform: translateY(0.5rem); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-slide-in-right { animation: slide-in-right 0.3s ease-out; }
    .animate-slide-in-left { animation: slide-in-left 0.3s ease-out; }
    .animate-fade-in-up { animation: fade-in-up 0.3s ease-out; }
</style>
@endif
</div>
