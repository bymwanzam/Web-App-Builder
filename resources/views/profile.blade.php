<x-app-layout>
    <x-slot name="title">Settings</x-slot>

    <!-- Page Header -->
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="square" stroke-linejoin="round" stroke-width="2" d="M10 19H5a1 1 0 0 1-1-1v-1a3 3 0 0 1 3-3h2m10 1a3 3 0 0 1-3 3m3-3a3 3 0 0 0-3-3m3 3h1m-4 3a3 3 0 0 1-3-3m3 3v1m-3-4a3 3 0 0 1 3-3m-3 3h-1m4-3v-1m-2.121 1.879-.707-.707m5.656 5.656-.707-.707m-4.242 0-.707.707m5.656-5.656-.707.707M12 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Settings</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 hidden sm:block">Manage your account settings and preferences</p>
        </div>
    </div>

    <!-- Settings Cards -->
    <div class="space-y-6">

        <!-- Profile Settings -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Profile Settings</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update your account information</p>
            </div>
            <div class="p-6">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>

        <!-- Security Settings -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Security</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your password and authentication</p>
            </div>
            <div class="p-6">
                <livewire:profile.update-password-form />
            </div>
        </div>

        <!-- Privacy & Data Settings -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Privacy & Data</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your consent preferences and data</p>
            </div>
            <div class="p-6 space-y-6">
                <livewire:user.consent-settings />
                <hr class="border-gray-200 dark:border-gray-700" />
                <livewire:user.data-export-card />
            </div>
        </div>

        @if(auth()->id() !== 1)
            <!-- Danger Zone -->
            <div class="bg-white border border-red-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-red-800">
                <div class="p-6 border-b border-red-200 dark:border-red-800">
                    <h3 class="text-lg font-semibold text-red-600 dark:text-red-400">Danger Zone</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Irreversible actions</p>
                </div>
                <div class="p-6">
                    <livewire:user.delete-account-modal />
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
