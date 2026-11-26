{{-- Platform OAuth Configuration Form --}}
{{-- This form is loaded by PluginConfigModal for admin platform settings --}}

<div class="space-y-6">
    {{-- Section Header --}}
    <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            WordPress.com OAuth Settings
        </h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Configure the shared OAuth credentials for all WordPress apps.
            <a href="https://developer.wordpress.com/apps/"
               target="_blank"
               class="text-blue-600 hover:underline">
                Create OAuth app at WordPress.com &rarr;
            </a>
        </p>
    </div>

    {{-- OAuth Client ID --}}
    <div>
        <label for="oauth_client_id"
               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            OAuth Client ID
            <span class="text-red-500">*</span>
        </label>
        <input type="text"
               id="oauth_client_id"
               wire:model="config.oauth_client_id"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                      focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                      dark:bg-gray-700 dark:border-gray-600 dark:text-white
                      dark:focus:ring-blue-500 dark:focus:border-blue-500"
               placeholder="12345">
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Found in your WordPress.com application settings
        </p>
    </div>

    {{-- OAuth Client Secret --}}
    <div>
        <label for="oauth_client_secret"
               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            OAuth Client Secret
            <span class="text-red-500">*</span>
        </label>
        <input type="password"
               id="oauth_client_secret"
               wire:model="config.oauth_client_secret"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                      focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                      dark:bg-gray-700 dark:border-gray-600 dark:text-white
                      dark:focus:ring-blue-500 dark:focus:border-blue-500"
               placeholder="••••••••••••••••">
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Keep this secret secure - injected into apps at build time
        </p>
    </div>

    {{-- OAuth Redirect Scheme --}}
    <div>
        <label for="oauth_redirect_scheme"
               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            OAuth Redirect Scheme
        </label>
        <div class="flex">
            <input type="text"
                   id="oauth_redirect_scheme"
                   wire:model="config.oauth_redirect_scheme"
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg
                          focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                          dark:bg-gray-700 dark:border-gray-600 dark:text-white
                          dark:focus:ring-blue-500 dark:focus:border-blue-500"
                   placeholder="appy-wp">
            <span class="inline-flex items-center px-3 text-sm text-gray-500 bg-gray-100
                         border border-l-0 border-gray-300 rounded-r-lg
                         dark:bg-gray-600 dark:text-gray-300 dark:border-gray-600">
                ://callback
            </span>
        </div>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Custom URL scheme for OAuth callback (default: appy-wp)
        </p>
    </div>

    {{-- Info Box --}}
    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
        <div class="flex">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0"
                 fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0
                         11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0
                         001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                      clip-rule="evenodd"/>
            </svg>
            <div class="text-sm text-blue-700 dark:text-blue-300">
                <p class="font-medium">Redirect URI for WordPress.com App:</p>
                <code class="block mt-1 p-2 bg-blue-100 dark:bg-blue-900/40 rounded text-xs break-all">
                    {{ $config['oauth_redirect_scheme'] ?? 'appy-wp' }}://callback
                </code>
                <p class="mt-2 text-xs">
                    Enter this exact URI in your WordPress.com application settings.
                </p>
            </div>
        </div>
    </div>

    {{-- Warning for unconfigured state --}}
    @if(empty($config['oauth_client_id']) || empty($config['oauth_client_secret']))
        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
            <div class="flex">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2 flex-shrink-0"
                     fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75
                             1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11
                             13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                          clip-rule="evenodd"/>
                </svg>
                <div class="text-sm text-yellow-700 dark:text-yellow-300">
                    <p class="font-medium">OAuth Not Configured</p>
                    <p class="mt-1">
                        Apps can still be built, but user authentication features will be disabled.
                        Users will only be able to read public content.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
