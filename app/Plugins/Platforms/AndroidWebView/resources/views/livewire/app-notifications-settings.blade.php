<form wire:submit="save">

    <!-- Notifications Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Notifications</h3>
                <p class="settings-card-description">Configure Firebase Cloud Messaging (FCM) for notifications</p>
            </div>
        </div>

        <div class="space-y-6">

            <!-- Enable/Disable -->
            <div>
                <x-toggle
                    wire-model="enabled"
                    id="enabled"
                    label="Enable Notifications"
                />
            </div>

            <!-- Firebase Service Account -->
            <div>
                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    Firebase Service Account
                </label>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                    Used by the server to send notifications via FCM API
                </p>
                <x-file-upload
                    wire-model="firebase_credentials_file_upload"
                    accept=".json"
                    label=""
                    help-text="JSON file (MAX. 2MB)"
                    :current-file="$firebase_credentials_file"
                    :max-size="2"
                    preview-type="file"
                />
                @if($firebase_credentials_file)
                    <button
                        type="button"
                        wire:click="deleteFirebaseCredentials"
                        wire:confirm="Are you sure you want to delete the Firebase credentials file?"
                        class="mt-2 rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800 focus:outline-hidden focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
                    >
                        Delete Firebase Credentials
                    </button>
                @endif
            </div>

            <!-- Google Services -->
            <div>
                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    Google Services
                </label>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                    Required for Android app configuration
                </p>
                <x-file-upload
                    wire-model="google_services_file_upload"
                    accept=".json"
                    label=""
                    help-text="JSON file (MAX. 1MB)"
                    :current-file="$google_services_file"
                    :max-size="1"
                    preview-type="file"
                />
                @if($google_services_file)
                    <button
                        type="button"
                        wire:click="deleteGoogleServices"
                        wire:confirm="Are you sure you want to delete the Google Services file?"
                        class="mt-2 rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800 focus:outline-hidden focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
                    >
                        Delete Google Services File
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="flex items-center justify-between pt-4">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-3 text-center dark:bg-blue-600 dark:hover:bg-blue-700 transition-all">
            {{ __('Save') }}
        </button>
    </div>

</form>
