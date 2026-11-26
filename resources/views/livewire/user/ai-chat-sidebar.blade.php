<div class="flex flex-col h-full">
    <!-- New Chat Button - Only show when a conversation is active -->
    @if($activeConversationId)
    <div class="p-3 border-b border-gray-200 dark:border-gray-700">
        <button
            wire:click="newConversation"
            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Chat
        </button>
    </div>
    @endif

    <!-- Conversations List -->
    <div class="flex-1 overflow-y-auto p-2">
        @if(empty($conversations))
            <div class="text-center py-8 px-4">
                <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="text-sm text-gray-500 dark:text-gray-400">No conversations yet</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Start a new chat above</p>
            </div>
        @else
            <div class="space-y-1">
                @foreach($conversations as $conv)
                    <div
                        wire:key="conv-{{ $conv['id'] }}"
                        class="group relative"
                    >
                        <button
                            wire:click="selectConversation({{ $conv['id'] }})"
                            class="w-full text-left px-3 py-2 rounded-lg transition-colors {{ $activeConversationId === $conv['id'] ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}"
                        >
                            <p class="text-sm font-medium truncate pr-6">{{ $conv['title'] }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                @if($conv['appName'])
                                    <span class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $conv['appName'] }}</span>
                                    <span class="text-gray-300 dark:text-gray-600">·</span>
                                @endif
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $conv['updatedAt'] }}</span>
                            </div>
                        </button>

                        <!-- Delete button -->
                        <button
                            wire:click="$dispatch('delete-conversation', { id: {{ $conv['id'] }}, isActive: {{ $activeConversationId === $conv['id'] ? 'true' : 'false' }} })"
                            class="absolute right-2 top-1/2 -translate-y-1/2 p-1 rounded opacity-0 group-hover:opacity-100 hover:bg-red-100 dark:hover:bg-red-900/30 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-all"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Settings Button (only show if user can configure their own API keys) -->
    @if(auth()->user()->plan?->allowsUserAiApiKey())
    <div class="p-3 border-t border-gray-200 dark:border-gray-700">
        <button
            wire:click="$dispatch('open-ai-settings-modal')"
            class="w-full flex items-center gap-2 px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            AI Settings
        </button>
    </div>
    @endif
</div>
