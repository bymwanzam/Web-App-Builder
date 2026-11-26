<form wire:submit.prevent="save">

    <!-- Core Features Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Core Features</h3>
                <p class="settings-card-description">Enable or disable app functionality</p>
            </div>
        </div>

        <div class="space-y-4">
            <!-- Enable Search -->
            <div>
                <x-toggle
                    wire-model="enable_search"
                    :live="true"
                    label="Enable search"
                />
                <small class="helper-text">Allow users to search for posts in your app</small>
            </div>

            <!-- Enable Comments -->
            <div>
                <x-toggle
                    wire-model="enable_comments"
                    :live="true"
                    label="Enable comments"
                />
                <small class="helper-text">Allow users to view and post comments on articles</small>
            </div>

            <!-- Enable Bookmarks -->
            <div>
                <x-toggle
                    wire-model="enable_bookmarks"
                    :live="true"
                    label="Enable bookmarks"
                />
                <small class="helper-text">Allow users to save posts for later reading</small>
            </div>

            <!-- Enable Share -->
            <div>
                <x-toggle
                    wire-model="enable_share"
                    :live="true"
                    label="Enable sharing"
                />
                <small class="helper-text">Allow users to share posts via other apps</small>
            </div>
        </div>
    </div>

    <!-- Advanced Features Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Advanced Features</h3>
                <p class="settings-card-description">Additional functionality for enhanced user experience</p>
            </div>
        </div>

        <div class="space-y-4">
            <!-- Enable Offline Reading -->
            <div>
                <x-toggle
                    wire-model="enable_offline_reading"
                    :live="true"
                    label="Enable offline reading"
                />
                <small class="helper-text">Cache posts for reading without internet connection</small>
            </div>

            <!-- Enable User Auth -->
            <div>
                <x-toggle
                    wire-model="enable_user_auth"
                    :live="true"
                    label="Enable user authentication"
                />
                <small class="helper-text">Allow users to sign in with their WordPress.com account</small>
            </div>

            <!-- Enable Push Notifications -->
            <div>
                <x-toggle
                    wire-model="enable_push_notifications"
                    :live="true"
                    label="Enable push notifications"
                />
                <small class="helper-text">Send notifications when new posts are published</small>
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
