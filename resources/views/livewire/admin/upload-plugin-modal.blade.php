<div x-data="{ showModal: @entangle('showModal') }"
     x-show="showModal"
     x-cloak
     @keydown.escape.window="showModal = false"
     class="fixed inset-0 z-50 overflow-y-auto">

    <!-- Modal backdrop -->
    <div class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 transition-opacity"></div>

    <!-- Modal -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <div @click.away="$wire.close()" class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Upload Plugin
                </h3>
                <button wire:click="close" type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form wire:submit="upload" class="p-6 space-y-4">
                <x-file-upload
                    wire-model="pluginFile"
                    accept=".zip"
                    label="Plugin File"
                    help-text="ZIP file (MAX. 10MB)"
                    :max-size="10"
                    preview-type="file"
                />

                @if($uploading)
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm text-blue-800 dark:text-blue-300">Installing plugin...</span>
                        </div>
                    </div>
                @endif

                <!-- Footer -->
                <div class="flex gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" wire:click="close" @disabled($uploading)
                            class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                        Cancel
                    </button>
                    <button type="submit" @disabled($uploading || !$pluginFile)
                            class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        @if($uploading)
                            Installing...
                        @else
                            Upload
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
