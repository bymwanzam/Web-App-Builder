<div x-data="{ show: @entangle('showModal'), showAdvanced: false }">
    <template x-if="show">
        <div class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title"
             role="dialog"
             aria-modal="true">

            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500/75 dark:bg-gray-900/75"
                     @click="$wire.closeModal()"
                     aria-hidden="true"></div>

                <!-- Center modal -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div class="relative z-10 inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">

                    <!-- Modal header -->
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Generate Signing Key
                        </h3>
                        <button type="button"
                                wire:click="closeModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Error display -->
                    @if($error)
                        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-red-900/50 dark:text-red-200 border border-red-200 dark:border-red-800">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $error }}
                            </div>
                        </div>
                    @endif

                    <!-- Modal body -->
                    <form wire:submit="generate">
                        <div class="space-y-4 mb-6 max-h-[60vh] overflow-y-auto pr-2">
                            <!-- Platform Info -->
                            <div class="p-3 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-900 rounded-lg border border-green-200 dark:border-green-800">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.6 9.48l1.84-3.18c.16-.31.04-.69-.26-.85a.637.637 0 0 0-.83.22l-1.88 3.24a11.43 11.43 0 0 0-8.94 0L5.65 5.67a.643.643 0 0 0-.87-.2c-.28.18-.37.54-.22.83L6.4 9.48A10.81 10.81 0 0 0 1 18h22a10.81 10.81 0 0 0-5.4-8.52M7 15.25a1.25 1.25 0 1 1 0-2.5 1.25 1.25 0 0 1 0 2.5m10 0a1.25 1.25 0 1 1 0-2.5 1.25 1.25 0 0 1 0 2.5"/>
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Generate Android Keystore</span>
                                </div>
                                <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                    A new JKS keystore will be generated securely on the build server
                                </p>
                            </div>

                            <!-- Basic Info Section -->
                            <div class="space-y-4">
                                <!-- Keystore Name -->
                                <div>
                                    <label for="keystore_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Keystore Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="keystore_name" wire:model.live.debounce.300ms="keystore_name"
                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                           placeholder="My App Release Key">
                                    @error('keystore_name')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Key Alias -->
                                <div>
                                    <label for="alias" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Key Alias <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="alias" wire:model="alias"
                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                           placeholder="my-app-key">
                                    @error('alias')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Must start with a letter. Letters, numbers, hyphens, and underscores allowed.
                                    </p>
                                </div>
                            </div>

                            <!-- Password Section -->
                            <div class="space-y-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Password Settings
                                </h4>

                                <!-- Store Password -->
                                <div>
                                    <label for="store_password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Keystore Password <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password" id="store_password" wire:model="store_password"
                                           class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                           placeholder="Min. 6 characters">
                                    @error('store_password')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Same password toggle -->
                                <div class="flex items-center">
                                    <input type="checkbox" id="use_same_password" wire:model.live="use_same_password"
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="use_same_password" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Use same password for key and keystore
                                    </label>
                                </div>

                                @if(!$use_same_password)
                                    <!-- Key Password -->
                                    <div>
                                        <label for="key_password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                            Key Password <span class="text-red-500">*</span>
                                        </label>
                                        <input type="password" id="key_password" wire:model="key_password"
                                               class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                               placeholder="Min. 6 characters">
                                        @error('key_password')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                            </div>

                            <!-- Validity Period -->
                            <div>
                                <label for="validity_years" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Validity Period
                                </label>
                                <select id="validity_years" wire:model="validity_years"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="10">10 years</option>
                                    <option value="15">15 years</option>
                                    <option value="20">20 years</option>
                                    <option value="25">25 years (Recommended)</option>
                                    <option value="30">30 years</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Google Play requires at least 25 years validity for new apps
                                </p>
                            </div>

                            <!-- Certificate Details (Advanced) -->
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                <button type="button"
                                        @click="showAdvanced = !showAdvanced"
                                        class="w-full flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/>
                                        </svg>
                                        Certificate Details (Optional)
                                    </span>
                                    <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': showAdvanced }" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>

                                <div x-show="showAdvanced" x-collapse class="p-4 space-y-4 bg-white dark:bg-gray-800">
                                    <!-- Common Name -->
                                    <div>
                                        <label for="cn" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                            Common Name (CN) <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="cn" wire:model="cn"
                                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                               placeholder="Your app or organization name">
                                        @error('cn')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Organization -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="o" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                                Organization (O)
                                            </label>
                                            <input type="text" id="o" wire:model="o"
                                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                   placeholder="Company Name">
                                        </div>
                                        <div>
                                            <label for="ou" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                                Organizational Unit (OU)
                                            </label>
                                            <input type="text" id="ou" wire:model="ou"
                                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                   placeholder="Mobile Apps">
                                        </div>
                                    </div>

                                    <!-- Location -->
                                    <div class="grid grid-cols-3 gap-4">
                                        <div>
                                            <label for="l" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                                City (L)
                                            </label>
                                            <input type="text" id="l" wire:model="l"
                                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                   placeholder="San Francisco">
                                        </div>
                                        <div>
                                            <label for="st" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                                State (ST)
                                            </label>
                                            <input type="text" id="st" wire:model="st"
                                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                   placeholder="CA">
                                        </div>
                                        <div>
                                            <label for="c" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                                Country (C)
                                            </label>
                                            <input type="text" id="c" wire:model="c"
                                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                   placeholder="US"
                                                   maxlength="2">
                                            @error('c')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Info box -->
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="text-xs text-blue-700 dark:text-blue-300">
                                        <p class="font-medium">Important: Save your password securely!</p>
                                        <p class="mt-1">The keystore will be encrypted and stored. You'll need the password for future builds. There is no way to recover a lost password.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-2">
                                <button type="button"
                                        wire:click="closeModal"
                                        :disabled="$wire.isGenerating"
                                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-hidden focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Cancel
                                </button>
                                <button type="submit"
                                        :disabled="$wire.isGenerating"
                                        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-hidden focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800 disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center">
                                    <template x-if="$wire.isGenerating">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </template>
                                    <span x-text="$wire.isGenerating ? 'Generating...' : 'Generate Keystore'"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
