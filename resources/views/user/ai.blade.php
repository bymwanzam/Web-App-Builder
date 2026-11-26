<x-app-layout>
    <x-slot name="title">Assistant</x-slot>

    <div
        x-data="{
            hasConversations: {{ ($hasConversations ?? false) || isset($conversationId) ? 'true' : 'false' }},
            sidebarVisible: {{ ($hasConversations ?? false) || isset($conversationId) ? 'true' : 'false' }}
        }"
        @sidebar-state-changed.window="hasConversations = $event.detail.hasConversations; sidebarVisible = hasConversations"
        @conversation-started.window="sidebarVisible = true"
        @update-url.window="history.pushState({}, '', $event.detail.url)"
        class="flex h-[calc(100dvh-6.5rem)] bg-white dark:bg-gray-800 rounded-lg shadow-xs border border-gray-200 dark:border-gray-700 overflow-hidden"
    >
        <!-- Sidebar - Conditionally shown with animation -->
        <div
            x-show="sidebarVisible"
            x-transition:enter="transition-all ease-out duration-300"
            x-transition:enter-start="w-0 opacity-0 -translate-x-4"
            x-transition:enter-end="w-64 opacity-100 translate-x-0"
            x-transition:leave="transition-all ease-in duration-200"
            x-transition:leave-start="w-64 opacity-100 translate-x-0"
            x-transition:leave-end="w-0 opacity-0 -translate-x-4"
            class="w-64 border-r border-gray-200 dark:border-gray-700 flex-shrink-0 hidden md:flex flex-col overflow-hidden"
            x-cloak
        >
            <livewire:user.ai-chat-sidebar :active-conversation-id="$conversationId ?? null" />
        </div>

        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <livewire:user.ai-chat :conversation-id="$conversationId ?? null" :app-id="$appId ?? null" />
        </div>
    </div>

    @push('modals')
        <livewire:user.delete-conversation-modal />
        <livewire:user.ai-settings-modal />
    @endpush
</x-app-layout>
