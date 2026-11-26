<form wire:submit.prevent="save">

    <!-- Push Notifications Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Push Notifications</h3>
                <p class="settings-card-description">Configure Firebase Cloud Messaging for push notifications</p>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Enable Push Notifications Toggle -->
            <div>
                <x-toggle
                    wire-model="enable_push_notifications"
                    :live="true"
                    label="Enable push notifications"
                />
                <small class="helper-text">Send notifications when new posts are published</small>
            </div>

            @if($enable_push_notifications)
                <!-- Firebase Configuration Section -->
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Firebase Configuration Required</p>
                            <p class="text-sm text-yellow-700 dark:text-yellow-400 mt-1">
                                Upload both configuration files below to enable push notifications in your app.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Google Services JSON -->
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        google-services.json
                    </label>

                    @if($google_services_json)
                        <div class="flex items-center gap-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-800 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-green-900 dark:text-green-300">File uploaded</p>
                                <p class="text-xs text-green-700 dark:text-green-400">google-services.json is configured</p>
                            </div>
                            <button
                                type="button"
                                wire:click="deleteGoogleServices"
                                wire:confirm="Are you sure you want to delete this file?"
                                class="rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800 focus:outline-hidden focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
                            >
                                Delete
                            </button>
                        </div>
                    @else
                        <x-file-upload
                            wire-model="google_services_file"
                            accept=".json"
                            label="Upload google-services.json"
                            help-text="Download from Firebase Console (MAX. 1MB)"
                            :max-size="1"
                            preview-type="file"
                        />
                    @endif

                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Download from Firebase Console > Project Settings > Your apps > Android app
                    </p>
                </div>

                <!-- Firebase Service Account -->
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Firebase Service Account (for sending notifications)
                    </label>

                    @if($firebase_service_account)
                        <div class="flex items-center gap-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-800 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-green-900 dark:text-green-300">Credentials configured</p>
                                <p class="text-xs text-green-700 dark:text-green-400">Service account is securely stored</p>
                            </div>
                            <button
                                type="button"
                                wire:click="deleteFirebaseServiceAccount"
                                wire:confirm="Are you sure you want to delete these credentials?"
                                class="rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800 focus:outline-hidden focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
                            >
                                Delete
                            </button>
                        </div>
                    @else
                        <x-file-upload
                            wire-model="firebase_service_account_file"
                            accept=".json"
                            label="Upload Service Account JSON"
                            help-text="Download from Firebase Console (MAX. 2MB)"
                            :max-size="2"
                            preview-type="file"
                        />
                    @endif

                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Download from Firebase Console > Project Settings > Service Accounts > Generate new private key
                    </p>
                </div>

                <!-- How to Setup -->
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <p class="text-sm font-medium text-blue-900 dark:text-blue-300 mb-2">How to set up Firebase:</p>
                    <ol class="text-sm text-blue-800 dark:text-blue-400 space-y-1 list-decimal list-inside">
                        <li>Go to <a href="https://console.firebase.google.com" target="_blank" class="underline hover:text-blue-600">Firebase Console</a></li>
                        <li>Create a project or select an existing one</li>
                        <li>Add an Android app with your package name: <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">{{ $app->androidWordPressConfig?->package_name }}</code></li>
                        <li>Download <strong>google-services.json</strong> and upload it above</li>
                        <li>Go to Project Settings > Service Accounts</li>
                        <li>Generate a new private key and upload it above</li>
                    </ol>
                </div>
            @endif
        </div>
    </div>

    <!-- Save Button -->
    <div class="flex items-center justify-between pt-4">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-3 text-center dark:bg-blue-600 dark:hover:bg-blue-700 transition-all">
            {{ __('Save') }}
        </button>
    </div>

</form>
