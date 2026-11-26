<div
    x-data="{
        show: @entangle('show'),
        showPreferences: @entangle('showPreferences'),
        setCookieConsent(preferences) {
            document.cookie = `cookie_consent=${JSON.stringify(preferences)}; path=/; max-age=31536000; SameSite=Lax`;
            this.show = false;
        }
    }"
    x-show="show"
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    @cookie-consent-saved.window="setCookieConsent($event.detail.preferences)"
    class="fixed bottom-0 left-0 right-0 z-50 p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg"
>
    <div class="max-w-7xl mx-auto">
        {{-- Simple consent view --}}
        <div x-show="!showPreferences" x-transition>
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-600 dark:text-gray-300 text-center md:text-left">
                    <p>
                        We use cookies to enhance your experience. By continuing, you agree to our
                        <a href="{{ route('legal.cookies') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Cookie Policy</a>.
                    </p>
                </div>
                <div class="flex flex-wrap justify-center gap-2">
                    <button
                        wire:click="acceptEssential"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Essential Only
                    </button>
                    <button
                        @click="showPreferences = true"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Manage Preferences
                    </button>
                    <button
                        wire:click="acceptAll"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Accept All
                    </button>
                </div>
            </div>
        </div>

        {{-- Detailed preferences view --}}
        <div x-show="showPreferences" x-transition class="max-w-2xl mx-auto">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Cookie Preferences</h3>

            <div class="space-y-4 mb-6">
                {{-- Essential Cookies --}}
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div>
                        <span class="font-medium text-gray-900 dark:text-white">Essential Cookies</span>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Required for the website to function properly.</p>
                    </div>
                    <div class="relative">
                        <input type="checkbox" checked disabled class="sr-only peer">
                        <div class="w-11 h-6 bg-blue-600 rounded-full"></div>
                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition peer-checked:translate-x-full"></div>
                    </div>
                </div>

                {{-- Analytics Cookies --}}
                <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer">
                    <div>
                        <span class="font-medium text-gray-900 dark:text-white">Analytics Cookies</span>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Help us understand how visitors use our site.</p>
                    </div>
                    <div class="relative">
                        <input type="checkbox" wire:model="analytics" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-checked:bg-blue-600 rounded-full transition-colors"></div>
                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform peer-checked:translate-x-5"></div>
                    </div>
                </label>

                {{-- Marketing Cookies --}}
                <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer">
                    <div>
                        <span class="font-medium text-gray-900 dark:text-white">Marketing Cookies</span>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Used to deliver relevant advertisements.</p>
                    </div>
                    <div class="relative">
                        <input type="checkbox" wire:model="marketing" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-checked:bg-blue-600 rounded-full transition-colors"></div>
                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform peer-checked:translate-x-5"></div>
                    </div>
                </label>
            </div>

            <div class="flex justify-end gap-2">
                <button
                    @click="showPreferences = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    Back
                </button>
                <button
                    wire:click="savePreferences"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    Save Preferences
                </button>
            </div>
        </div>
    </div>
</div>
