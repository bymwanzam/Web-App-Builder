<div class="bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Appetize.io Integration</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure live app preview functionality</p>
    </div>

    <form wire:submit="save" class="p-6 space-y-4">
        <!-- Enable Appetize Integration -->
        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">Enable Appetize.io Integration</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Allow users with Pro/Enterprise plans to preview apps in browser</p>
            </div>
            <x-toggle wire-model="appetize_enabled" />
        </div>

        <!-- API Token -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">API Token</label>
            <div class="flex gap-2">
                <input
                    type="password"
                    wire:model="appetize_api_token"
                    placeholder="Enter your Appetize.io API token"
                    class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                @if(config('app.demo'))
                    <button type="button" disabled title="Disabled in demo mode" class="text-gray-900 bg-white border border-gray-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 inline-flex items-center cursor-not-allowed opacity-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Test Connection
                    </button>
                @else
                    <button
                        type="button"
                        wire:click="testConnection"
                        wire:loading.attr="disabled"
                        class="text-gray-900 bg-white border border-gray-300 focus:outline-hidden hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-4 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700 inline-flex items-center">
                        <svg wire:loading.remove wire:target="testConnection" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <svg wire:loading wire:target="testConnection" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="testConnection">Test Connection</span>
                        <span wire:loading wire:target="testConnection">Testing...</span>
                    </button>
                @endif
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Get your API token from
                <a href="https://appetize.io/account" target="_blank" class="text-blue-600 hover:underline dark:text-blue-500">
                    appetize.io/account
                </a>
            </p>
            @error('appetize_api_token')
                <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </div>

        <!-- Auto Upload Toggle -->
        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">Auto-upload builds to Appetize</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Automatically upload successful builds for instant preview</p>
            </div>
            <x-toggle wire-model="appetize_auto_upload" />
        </div>

        <!-- Preview Retention Days -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Preview Retention (Days)
            </label>
            <input
                type="number"
                wire:model="appetize_retention_days"
                min="1"
                max="30"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Delete old previews after this many days to save costs (1-30 days)
            </p>
            @error('appetize_retention_days')
                <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </div>

        <!-- Session Timeout -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Session Timeout (Seconds)
            </label>
            <select
                wire:model="appetize_session_timeout"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="30">30 seconds</option>
                <option value="60">1 minute</option>
                <option value="120">2 minutes</option>
                <option value="180">3 minutes</option>
                <option value="300">5 minutes</option>
                <option value="600">10 minutes</option>
            </select>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Inactivity timeout before session ends
            </p>
            @error('appetize_session_timeout')
                <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit Button -->
        @if(config('app.demo'))
            <button type="button" disabled title="Disabled in demo mode" class="text-white bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 cursor-not-allowed opacity-50">
                {{ __('Save') }}
            </button>
        @else
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-hidden dark:focus:ring-blue-800">
                {{ __('Save') }}
            </button>
        @endif
    </form>
</div>
