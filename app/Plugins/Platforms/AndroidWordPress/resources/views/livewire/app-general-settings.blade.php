<form wire:submit.prevent="save">

    <!-- Basic Information Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Basic Information</h3>
                <p class="settings-card-description">Essential details about your app</p>
            </div>
        </div>

        <div class="space-y-4">
            <!-- App Name -->
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    App name
                </label>
                <input type="text" id="name" wire:model.live="name"
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('name') border-red-500 @enderror"
                       placeholder="My WordPress App">
                <div class="flex items-center justify-between">
                    @error('name')
                        <small class="helper-text text-red-600 dark:text-red-400">{{ $message }}</small>
                    @else
                        <small class="helper-text">Displayed on device home screen</small>
                    @enderror
                    <small class="character-counter {{ strlen($name ?? '') > 204 ? 'warning' : '' }} {{ strlen($name ?? '') >= 255 ? 'error' : '' }}" x-data x-init="$watch('$wire.name', value => $el.textContent = (value?.length || 0) + '/255')">{{ strlen($name ?? '') }}/255</small>
                </div>
            </div>

            <!-- WordPress Site URL -->
            <div>
                <label for="site_url" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    WordPress Site URL
                </label>
                <input type="url" id="site_url" wire:model.live="site_url"
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('site_url') border-red-500 @enderror"
                       placeholder="https://myblog.wordpress.com">
                <div class="flex items-center justify-between">
                    @error('site_url')
                        <small class="helper-text text-red-600 dark:text-red-400">{{ $message }}</small>
                    @else
                        <small class="helper-text">Your WordPress.com site URL</small>
                    @enderror
                    <small class="character-counter {{ strlen($site_url ?? '') > 204 ? 'warning' : '' }} {{ strlen($site_url ?? '') >= 255 ? 'error' : '' }}" x-data x-init="$watch('$wire.site_url', value => $el.textContent = (value?.length || 0) + '/255')">{{ strlen($site_url ?? '') }}/255</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Configuration</h3>
                <p class="settings-card-description">Technical settings and identifiers</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- APP ID -->
            <div class="relative">
                <label for="app-id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    APP ID
                    <span class="tooltip-icon" title="Unique package identifier used by app stores">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                </label>
                <div class="relative" x-data="{
                    copied: false,
                    copyToClipboard() {
                        const input = this.$refs.appIdInput;
                        input.select();
                        input.setSelectionRange(0, 99999);
                        document.execCommand('copy');
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    }
                }">
                    <input type="text" id="app-id" x-ref="appIdInput" wire:model.live="app_id" readonly
                           class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-lg block w-full p-2.5 pr-16 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-400 cursor-not-allowed"
                           placeholder="com.example.wordpress">
                    <button type="button" @click="copyToClipboard()" class="copy-button">
                        <span x-show="!copied">Copy</span>
                        <span x-show="copied" x-cloak>Copied!</span>
                    </button>
                </div>
                @error('app_id')
                    <small class="helper-text text-red-600 dark:text-red-400">{{ $message }}</small>
                @else
                    <small class="helper-text">App ID cannot be changed once created</small>
                @enderror
            </div>

            <!-- Screen Orientation -->
            <div>
                <label for="screen-orientation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Screen orientation</label>
                <select id="screen-orientation" wire:model.live="screen_orientation"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="system">System</option>
                    <option value="portrait">Portrait</option>
                    <option value="landscape">Landscape</option>
                </select>
                @error('screen_orientation')
                    <small class="helper-text text-red-600 dark:text-red-400">{{ $message }}</small>
                @else
                    <small class="helper-text">Controls how your app rotates on device</small>
                @enderror
            </div>

            <!-- App Language -->
            <div>
                <label for="app-language" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">App language (ISO code)</label>
                <select id="app-language" wire:model.live="app_language"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="en">EN</option>
                </select>
                @error('app_language')
                    <small class="helper-text text-red-600 dark:text-red-400">{{ $message }}</small>
                @else
                    <small class="helper-text">Primary language for your app content</small>
                @enderror
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
