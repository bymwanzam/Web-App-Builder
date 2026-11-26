<form wire:submit="saveIcon">

    <!-- App Icon Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18.5A2.493 2.493 0 0 1 7.51 20H7.5a2.468 2.468 0 0 1-2.4-3.154 2.98 2.98 0 0 1-.85-5.274 2.468 2.468 0 0 1 .92-3.182 2.477 2.477 0 0 1 1.876-3.344 2.5 2.5 0 0 1 3.41-1.856A2.5 2.5 0 0 1 12 5.5m0 13v-13m0 13a2.493 2.493 0 0 0 4.49 1.5h.01a2.468 2.468 0 0 0 2.403-3.154 2.98 2.98 0 0 0 .847-5.274 2.468 2.468 0 0 0-.921-3.182 2.477 2.477 0 0 0-1.875-3.344A2.5 2.5 0 0 0 14.5 3 2.5 2.5 0 0 0 12 5.5m-8 5a2.5 2.5 0 0 1 3.48-2.3m-.28 8.551a3 3 0 0 1-2.953-5.185M20 10.5a2.5 2.5 0 0 0-3.481-2.3m.28 8.551a3 3 0 0 0 2.954-5.185"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">App Icon</h3>
                <p class="settings-card-description">Upload your app icon as a ZIP file from icon.kitchen</p>
            </div>
        </div>

        <div class="space-y-6">

            <!-- Current Icon Display -->
            @if($app->androidWebViewConfig?->icon_extracted_path || $app->androidWebViewConfig?->app_icon)
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Current Icon
                    </label>
                    <div class="flex items-center gap-4">
                        @if($app->androidWebViewConfig?->app_icon)
                            @php
                            $iconMtime = @filemtime(storage_path('app/public/' . $app->androidWebViewConfig->app_icon)) ?: time();
                            @endphp
                            <div class="w-20 h-20 rounded-lg overflow-hidden">
                                <img src="{{ Storage::url($app->androidWebViewConfig->app_icon) }}?v={{ $iconMtime }}" alt="App icon" class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18.5A2.493 2.493 0 0 1 7.51 20H7.5a2.468 2.468 0 0 1-2.4-3.154 2.98 2.98 0 0 1-.85-5.274 2.468 2.468 0 0 1 .92-3.182 2.477 2.477 0 0 1 1.876-3.344 2.5 2.5 0 0 1 3.41-1.856A2.5 2.5 0 0 1 12 5.5m0 13v-13m0 13a2.493 2.493 0 0 0 4.49 1.5h.01a2.468 2.468 0 0 0 2.403-3.154 2.98 2.98 0 0 0 .847-5.274 2.468 2.468 0 0 0-.921-3.182 2.477 2.477 0 0 0-1.875-3.344A2.5 2.5 0 0 0 14.5 3 2.5 2.5 0 0 0 12 5.5m-8 5a2.5 2.5 0 0 1 3.48-2.3m-.28 8.551a3 3 0 0 1-2.953-5.185M20 10.5a2.5 2.5 0 0 0-3.481-2.3m.28 8.551a3 3 0 0 0 2.954-5.185"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1">
                            @if($app->androidWebViewConfig?->icon_extracted_path)
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Icon ZIP uploaded</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Extracted icons are available for Android and Web platforms
                                </p>
                            @else
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Default Icon</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Upload a custom icon to replace the default
                                </p>
                            @endif
                        </div>
                        @if($app->androidWebViewConfig?->uploaded_icon_zip)
                            <button
                                type="button"
                                wire:click="$dispatch('delete-icon')"
                                class="rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800 focus:outline-hidden focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
                            >
                                Delete
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Icon Upload -->
            <div>
                <x-file-upload
                    wire-model="iconZipFile"
                    accept=".zip"
                    label="Upload ZIP File"
                    help-text="ZIP file from icon.kitchen (MAX. 10MB)"
                    :current-file="$app->androidWebViewConfig?->uploaded_icon_zip"
                    :max-size="10"
                    preview-type="file"
                />

                <div class="mt-3 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <p class="text-sm font-medium text-blue-900 dark:text-blue-300 mb-2">How to create your icon:</p>
                    <ol class="text-sm text-blue-800 dark:text-blue-400 space-y-1 list-decimal list-inside">
                        <li>Visit <a href="https://icon.kitchen" target="_blank" class="underline hover:text-blue-600">icon.kitchen</a></li>
                        <li>Design your app icon</li>
                        <li>Download the ZIP file containing all platform icons</li>
                        <li>Upload the ZIP file here</li>
                    </ol>
                </div>

                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    The ZIP file should contain icons for Android and Web platforms (Max 10MB)
                </p>

                <button
                    type="submit"
                    class="mt-4 rounded-lg bg-blue-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-blue-800 focus:outline-hidden focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                >
                    Upload Icon
                </button>
            </div>
        </div>
    </div>

</form>
