<div class="p-6 bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Privacy & Consent</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Manage how we use your data</p>
        </div>
        <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="saveConsents" class="space-y-4">
        {{-- Marketing Communications --}}
        <label class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
            <div class="flex items-center h-5 mt-0.5">
                <input
                    type="checkbox"
                    wire:model="marketing"
                    id="marketing"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                >
            </div>
            <div class="flex-1">
                <span class="font-medium text-gray-900 dark:text-white">Marketing Communications</span>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Receive updates about new features, tips, and promotional offers.
                </p>
            </div>
        </label>

        {{-- Third-Party Data Sharing --}}
        <label class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
            <div class="flex items-center h-5 mt-0.5">
                <input
                    type="checkbox"
                    wire:model="thirdParty"
                    id="thirdParty"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                >
            </div>
            <div class="flex-1">
                <span class="font-medium text-gray-900 dark:text-white">Third-Party Services</span>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Allow sharing data with third-party services (Appetize for app preview, Firebase for push notifications, payment gateways).
                </p>
            </div>
        </label>

        {{-- AI Services --}}
        <label class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
            <div class="flex items-center h-5 mt-0.5">
                <input
                    type="checkbox"
                    wire:model="aiServices"
                    id="aiServices"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                >
            </div>
            <div class="flex-1">
                <span class="font-medium text-gray-900 dark:text-white">AI Services</span>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Allow processing your prompts with AI services (OpenAI, Anthropic) for the AI chat feature.
                </p>
            </div>
        </label>

        <div class="pt-4">
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="saveConsents">Save Preferences</span>
                <span wire:loading wire:target="saveConsents">Saving...</span>
            </button>
        </div>
    </form>

    {{-- Privacy Links --}}
    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
            For more information about how we handle your data, please review:
        </p>
        <div class="flex flex-wrap gap-4 text-sm">
            <a href="{{ route('legal.privacy') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                Privacy Policy
            </a>
            <a href="{{ route('legal.cookies') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                Cookie Policy
            </a>
            <a href="{{ route('legal.terms') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                Terms of Service
            </a>
        </div>
    </div>
</div>
