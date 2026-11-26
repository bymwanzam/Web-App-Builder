<div>
<form wire:submit.prevent="save">

    <!-- User Agent Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">User Agent</h3>
                <p class="settings-card-description">Customize the browser identification string sent by the WebView</p>
            </div>
        </div>

        <div>
            <label for="user-agent" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Custom User Agent
            </label>
            <textarea id="user-agent" rows="4" wire:model.live="user_agent"
                      class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('user_agent') border-red-500 @enderror"
                      placeholder="Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 (KHTML, like Gecko) MyApp/1.0"></textarea>
            <div class="flex items-center justify-between">
                @error('user_agent')
                    <small class="helper-text text-red-600 dark:text-red-400">{{ $message }}</small>
                @else
                    <small class="helper-text">Leave empty to use the default Android WebView user agent</small>
                @enderror
                <small class="character-counter {{ strlen($user_agent ?? '') > 800 ? 'warning' : '' }} {{ strlen($user_agent ?? '') >= 1000 ? 'error' : '' }}" x-data x-init="$watch('$wire.user_agent', value => $el.textContent = (value?.length || 0) + '/1000')">{{ strlen($user_agent ?? '') }}/1000</small>
            </div>
        </div>
    </div>

    <!-- WebView Features Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">WebView Features</h3>
                <p class="settings-card-description">Configure WebView behavior and capabilities</p>
            </div>
        </div>

        <div class="space-y-3">
            <!-- JavaScript Toggle -->
            <label class="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 dark:bg-gray-700/50 dark:hover:bg-gray-700 transition-colors">
                <div class="flex-1 pr-4">
                    <div class="font-medium text-gray-900 dark:text-white">Enable JavaScript</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Allow JavaScript execution in the WebView (recommended for modern websites)</div>
                </div>
                <input type="checkbox"
                       wire:model.live="enable_javascript"
                       class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
            </label>

            <!-- DOM Storage -->
            <label class="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 dark:bg-gray-700/50 dark:hover:bg-gray-700 transition-colors">
                <div class="flex-1 pr-4">
                    <div class="font-medium text-gray-900 dark:text-white">Enable DOM Storage</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Allow localStorage and sessionStorage for web content (required for modern web apps)</div>
                </div>
                <input type="checkbox"
                       wire:model.live="enable_dom_storage"
                       class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
            </label>

            <!-- Zoom Controls -->
            <label class="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 dark:bg-gray-700/50 dark:hover:bg-gray-700 transition-colors">
                <div class="flex-1 pr-4">
                    <div class="font-medium text-gray-900 dark:text-white">Enable Zoom Controls</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Allow users to pinch-to-zoom and zoom in/out of web content</div>
                </div>
                <input type="checkbox"
                       wire:model.live="enable_zoom"
                       class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
            </label>

            <!-- Cache -->
            <label class="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 dark:bg-gray-700/50 dark:hover:bg-gray-700 transition-colors">
                <div class="flex-1 pr-4">
                    <div class="font-medium text-gray-900 dark:text-white">Enable Cache</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Cache web content for faster loading and offline support</div>
                </div>
                <input type="checkbox"
                       wire:model.live="enable_cache"
                       class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
            </label>
        </div>
    </div>

    <!-- Save Button -->
    <div class="flex items-center justify-between pt-4">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-3 text-center dark:bg-blue-600 dark:hover:bg-blue-700 transition-all">
            {{ __('Save') }}
        </button>
    </div>

</form>
</div>
