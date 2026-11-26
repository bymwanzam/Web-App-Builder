<div>
    <div x-data="{ show: @entangle('showModal') }"
         x-show="show"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500/75 dark:bg-gray-900/75"
                 @click="$wire.closeModal()"
                 aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative z-10 inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">

                <!-- Modal header -->
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-blue-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="modal-title">
                                AI Assistant Settings
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Configure your AI assistant preferences</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="closeModal"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal body -->
                @if(!$canUseOwnKey)
                    <!-- Upgrade Prompt -->
                    <div class="mb-4">
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Upgrade Required</p>
                                    <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
                                        Your current plan doesn't allow using your own API keys. Upgrade to Pro or Enterprise to use your own OpenAI, Anthropic or NVIDIA API keys and bypass token limits.
                                    </p>
                                    <a href="{{ route('user.billing') }}" wire:navigate class="inline-flex items-center mt-3 text-sm font-medium text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300">
                                        View upgrade options
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Close button for upgrade prompt -->
                    <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button"
                                wire:click="closeModal"
                                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-hidden focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                            Close
                        </button>
                    </div>
                @else
                    <form wire:submit="save">
                        <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-1">
                            <!-- Provider Preference -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Preferred Provider</label>
                                <select
                                    wire:model.live="preferred_provider"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                                    <option value="system">Use System Default</option>
                                    @foreach($availableProviders as $id => $provider)
                                        <option value="{{ $id }}">{{ $provider['name'] }} (Your Key)</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Choose "System Default" to use the platform's API key, or select a provider to use your own.
                                </p>
                            </div>

                            @if($preferred_provider === 'system')
                                <!-- System Default Info -->
                                <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        You're using the platform's AI service. Your usage is counted against your plan's monthly token limit.
                                    </p>
                                </div>
                            @else
                                <!-- Model Selection -->
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Preferred Model</label>
                                    <select
                                        wire:model="preferred_model"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                        <option value="">Use provider default</option>
                                        @foreach($availableModels as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                @if($preferred_provider === 'openai')
                                    <!-- OpenAI API Key -->
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">OpenAI API Key</label>
                                        <div class="flex gap-2">
                                            <input
                                                type="password"
                                                wire:model="openai_api_key"
                                                placeholder="sk-..."
                                                class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                            >
                                            <button
                                                type="button"
                                                wire:click="testOpenAiKey"
                                                wire:loading.attr="disabled"
                                                wire:target="testOpenAiKey"
                                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600"
                                            >
                                                <span wire:loading.remove wire:target="testOpenAiKey">Test</span>
                                                <span wire:loading wire:target="testOpenAiKey">...</span>
                                            </button>
                                            @if($openai_api_key)
                                                <button
                                                    type="button"
                                                    wire:click="clearOpenAiKey"
                                                    class="px-3 py-2 text-sm font-medium text-red-600 bg-white border border-gray-300 rounded-lg hover:bg-red-50 dark:bg-gray-700 dark:text-red-400 dark:border-gray-600 dark:hover:bg-red-900/20"
                                                >
                                                    Clear
                                                </button>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Get your key from <a href="https://platform.openai.com/api-keys" target="_blank" class="text-blue-600 hover:underline dark:text-blue-400">platform.openai.com</a>
                                        </p>
                                    </div>
                                @endif

                                @if($preferred_provider === 'anthropic')
                                    <!-- Anthropic API Key -->
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Anthropic API Key</label>
                                        <div class="flex gap-2">
                                            <input
                                                type="password"
                                                wire:model="anthropic_api_key"
                                                placeholder="sk-ant-..."
                                                class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                            >
                                            <button
                                                type="button"
                                                wire:click="testAnthropicKey"
                                                wire:loading.attr="disabled"
                                                wire:target="testAnthropicKey"
                                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600"
                                            >
                                                <span wire:loading.remove wire:target="testAnthropicKey">Test</span>
                                                <span wire:loading wire:target="testAnthropicKey">...</span>
                                            </button>
                                            @if($anthropic_api_key)
                                                <button
                                                    type="button"
                                                    wire:click="clearAnthropicKey"
                                                    class="px-3 py-2 text-sm font-medium text-red-600 bg-white border border-gray-300 rounded-lg hover:bg-red-50 dark:bg-gray-700 dark:text-red-400 dark:border-gray-600 dark:hover:bg-red-900/20"
                                                >
                                                    Clear
                                                </button>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Get your key from <a href="https://console.anthropic.com/settings/keys" target="_blank" class="text-blue-600 hover:underline dark:text-blue-400">console.anthropic.com</a>
                                        </p>
                                    </div>
                                @endif

                                @if($preferred_provider === 'nvidia')
                                    <!-- NVIDIA API Key -->
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NVIDIA API Key</label>
                                        <div class="flex gap-2">
                                            <input
                                                type="password"
                                                wire:model="nvidia_api_key"
                                                placeholder="nvapi-..."
                                                class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                            >
                                            <button
                                                type="button"
                                                wire:click="testNvidiaKey"
                                                wire:loading.attr="disabled"
                                                wire:target="testNvidiaKey"
                                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600"
                                            >
                                                <span wire:loading.remove wire:target="testNvidiaKey">Test</span>
                                                <span wire:loading wire:target="testNvidiaKey">...</span>
                                            </button>
                                            @if($nvidia_api_key)
                                                <button
                                                    type="button"
                                                    wire:click="clearNvidiaKey"
                                                    class="px-3 py-2 text-sm font-medium text-red-600 bg-white border border-gray-300 rounded-lg hover:bg-red-50 dark:bg-gray-700 dark:text-red-400 dark:border-gray-600 dark:hover:bg-red-900/20"
                                                >
                                                    Clear
                                                </button>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Get a free key from <a href="https://build.nvidia.com/" target="_blank" class="text-blue-600 hover:underline dark:text-blue-400">build.nvidia.com</a> (open any model, then "Get API Key")
                                        </p>
                                    </div>
                                @endif

                                <!-- Benefits Info -->
                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                    <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Benefits of using your own API key:</p>
                                    <ul class="mt-2 text-sm text-blue-700 dark:text-blue-300 list-disc list-inside space-y-1">
                                        <li>Unlimited AI tokens (no monthly quota)</li>
                                        <li>Direct billing from your provider account</li>
                                        <li>Access to the latest models</li>
                                        <li>Your API usage is private</li>
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end gap-2 pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="button"
                                    wire:click="closeModal"
                                    class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-hidden focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Save
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
