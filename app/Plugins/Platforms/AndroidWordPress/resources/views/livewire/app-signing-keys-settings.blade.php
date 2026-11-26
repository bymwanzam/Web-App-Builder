<!-- Signing Card -->
<div class="settings-card">
    <div>
        <!-- Android Keystores -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-md font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.6 9.48l1.84-3.18c.16-.31.04-.69-.26-.85-.29-.15-.64-.01-.84.23l-1.88 3.24a11.43 11.43 0 0 0-8.94 0L5.65 5.67c-.2-.25-.56-.39-.85-.23-.3.16-.42.54-.26.85l1.84 3.18C2.5 12.18 0 16.23 0 20.75h24c0-4.52-2.5-8.57-6.4-11.27M7 17.25c-.69 0-1.25-.56-1.25-1.25s.56-1.25 1.25-1.25 1.25.56 1.25 1.25-.56 1.25-1.25 1.25m10 0c-.69 0-1.25-.56-1.25-1.25s.56-1.25 1.25-1.25 1.25.56 1.25 1.25-.56 1.25-1.25 1.25"/>
                    </svg>
                    Android Keystores
                </h4>
                <div class="flex gap-2">
                    <button type="button"
                            wire:click="$dispatch('openKeystoreGeneratorModal')"
                            class="inline-flex items-center rounded-lg bg-green-700 px-3 py-2 text-xs font-medium text-white hover:bg-green-800 focus:outline-hidden focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                        Generate New
                    </button>
                    <button type="button"
                            wire:click="$dispatch('openSigningKeyUploadModal')"
                            class="inline-flex items-center rounded-lg bg-blue-700 px-3 py-2 text-xs font-medium text-white hover:bg-blue-800 focus:outline-hidden focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Upload Existing
                    </button>
                </div>
            </div>

            @forelse($keystores as $keystore)
                <div class="mb-3 flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h5 class="font-medium text-gray-900 dark:text-white">{{ $keystore->name }}</h5>
                            @if($app->androidWordPressConfig?->keystore_id === $keystore->id)
                                <span class="inline-flex items-center rounded-md bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                    Default
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Alias: {{ $keystore->alias ?? 'Auto-generated' }} • Created {{ $keystore->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($app->androidWordPressConfig?->keystore_id !== $keystore->id)
                            <button
                                type="button"
                                wire:click="setAsDefault('android', {{ $keystore->id }})"
                                class="rounded-lg bg-gray-200 px-3 py-2 text-xs font-medium text-gray-900 hover:bg-gray-300 focus:outline-hidden dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
                            >
                                Set as Default
                            </button>
                        @endif
                        <button
                            type="button"
                            wire:click="$dispatch('edit-keystore', { id: {{ $keystore->id }} })"
                            class="rounded-lg bg-blue-700 px-3 py-2 text-xs font-medium text-white hover:bg-blue-800 focus:outline-hidden"
                        >
                            Edit
                        </button>
                        <button
                            type="button"
                            wire:click="$dispatch('delete-keystore', { id: {{ $keystore->id }}, name: '{{ $keystore->name }}' })"
                            class="rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800 focus:outline-hidden"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">No Android keystores yet. Generate a new key or upload an existing one.</p>
            @endforelse
        </div>
    </div>

    <!-- Keystore Generator Modal (shared component) -->
    @livewire('plugins.shared.keystore-generator-modal')
</div>
