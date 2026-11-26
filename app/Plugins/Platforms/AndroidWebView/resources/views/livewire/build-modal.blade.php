<div>
    <!-- Modal backdrop -->
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

            <!-- Center modal -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative z-10 inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">

                <!-- Modal header -->
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Create New Build
                    </h3>
                    <button type="button"
                            wire:click="closeModal"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal body -->
                <form wire:submit.prevent="createBuild">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Platform selection -->
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Platform <span class="text-red-500">*</span>
                            </label>
                            <div class="p-4 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-900 rounded-lg border-2 border-green-200 dark:border-green-800">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.6 9.48l1.84-3.18c.16-.31.04-.69-.26-.85a.637.637 0 0 0-.83.22l-1.88 3.24a11.43 11.43 0 0 0-8.94 0L5.65 5.67a.643.643 0 0 0-.87-.2c-.28.18-.37.54-.22.83L6.4 9.48A10.81 10.81 0 0 0 1 18h22a10.81 10.81 0 0 0-5.4-8.52M7 15.25a1.25 1.25 0 1 1 0-2.5 1.25 1.25 0 0 1 0 2.5m10 0a1.25 1.25 0 1 1 0-2.5 1.25 1.25 0 0 1 0 2.5"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="text-lg font-semibold text-gray-900 dark:text-white">Android</span>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Build costs 1 credit</p>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Build type selection -->
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Build Type <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <input type="radio" id="build-type-debug" wire:model.live="buildType" value="debug" class="hidden peer">
                                    <label for="build-type-debug"
                                           wire:click="$set('buildType', 'debug')"
                                           class="inline-flex items-center justify-between w-full p-4 text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:peer-checked:text-blue-500">
                                        <div class="flex flex-col items-center w-full">
                                            <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                            </svg>
                                            <span class="text-sm font-medium">Debug</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">For testing</span>
                                        </div>
                                    </label>
                                </div>
                                <div>
                                    <input type="radio" id="build-type-release" wire:model.live="buildType" value="release" class="hidden peer">
                                    <label for="build-type-release"
                                           wire:click="$set('buildType', 'release')"
                                           class="inline-flex items-center justify-between w-full p-4 text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:peer-checked:text-blue-500">
                                        <div class="flex flex-col items-center w-full">
                                            <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-sm font-medium">Release</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">For production</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Android Output Format (only shown for Android) -->
                        @if($platform === 'android-webview')
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Output Format <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <input type="radio" id="build-format-apk" wire:model.live="buildFormat" value="apk" class="hidden peer">
                                    <label for="build-format-apk"
                                           wire:click="$set('buildFormat', 'apk')"
                                           class="inline-flex items-center justify-between w-full p-4 text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:peer-checked:text-blue-500">
                                        <div class="flex flex-col items-center w-full">
                                            <svg class="w-8 h-8 mb-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                                            </svg>
                                            <span class="text-sm font-medium">APK</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">For testing/sideloading</span>
                                        </div>
                                    </label>
                                </div>
                                <div>
                                    <input type="radio" id="build-format-aab" wire:model.live="buildFormat" value="aab" class="hidden peer">
                                    <label for="build-format-aab"
                                           wire:click="$set('buildFormat', 'aab')"
                                           class="inline-flex items-center justify-between w-full p-4 text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:peer-checked:text-blue-500">
                                        <div class="flex flex-col items-center w-full">
                                            <svg class="w-8 h-8 mb-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                            </svg>
                                            <span class="text-sm font-medium">AAB</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">For Google Play Store</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Builder Selection -->
                        @if($this->canSelectBuilder)
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Builder
                            </label>
                            <select wire:model.live="selectedBuilderId"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                <option value="">Auto-select (recommended)</option>
                                @foreach($this->availableBuilders as $builder)
                                    <option value="{{ $builder->id }}">
                                        {{ $builder->name }} - {{ $builder->current_builds_count }}/{{ $builder->max_queue }} builds ({{ $builder->credit_cost ?? 1 }} credit{{ $builder->credit_cost > 1 ? 's' : '' }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Leave as "Auto-select" to use the least busy builder, or choose a specific builder.
                            </p>
                        </div>
                        @elseif($this->availableBuilders->count() === 1)
                            <!-- Show single builder info -->
                            <div class="col-span-2">
                                <div class="p-3 bg-blue-50 dark:bg-gray-800 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            Builder: {{ $this->availableBuilders->first()->name }} ({{ $this->availableBuilders->first()->credit_cost ?? 1 }} credit{{ $this->availableBuilders->first()->credit_cost > 1 ? 's' : '' }})
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @elseif($this->availableBuilders->count() === 0)
                            <!-- No builders available warning -->
                            <div class="col-span-2">
                                <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800" role="alert">
                                    <div class="flex items-start">
                                        <svg class="flex-shrink-0 inline w-5 h-5 me-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                                        </svg>
                                        <div>
                                            <span class="font-medium">No builders available!</span>
                                            <p class="mt-1 text-xs">Your plan does not have access to any builders. Please contact support or upgrade your plan.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Signing Key Selection (Release Builds Only) -->
                        @if($buildType === 'release')
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Signing Key <span class="text-red-500">*</span>
                            </label>

                            <!-- Android Keystore Selection -->
                            <select wire:model.live="selectedKeystoreId"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">Select a keystore</option>
                                @foreach($this->availableKeystores as $keystore)
                                    <option value="{{ $keystore->id }}">
                                        {{ $keystore->name }} ({{ $keystore->alias ?? 'Auto-generated' }})
                                    </option>
                                @endforeach
                            </select>
                            @if(count($this->availableKeystores) === 0)
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    No keystores available. <a href="#signing" wire:click="closeModal" class="underline hover:text-red-700">Upload one in the Signing section</a>
                                </p>
                            @endif
                        </div>
                        @endif

                        <!-- Signing Warning -->
                        @if($this->showSigningWarning)
                        <div class="col-span-2 p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800" role="alert">
                            <div class="flex items-start">
                                <svg class="flex-shrink-0 inline w-5 h-5 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                                </svg>
                                <div>
                                    <span class="font-medium">Signing key required!</span>
                                    <p class="mt-1 text-xs">{{ $this->signingWarningMessage }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Version Name Input -->
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Version Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   wire:model.blur="versionName"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 font-mono"
                                   placeholder="1.0.0">
                            @error('versionName')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Format: X.Y or X.Y.Z (e.g., 1.0 or 1.0.0). Auto-incremented for each platform.
                            </p>
                        </div>

                        <!-- Build Number Display -->
                        <div class="col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Build Number
                            </label>
                            <div class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 rounded-lg border border-blue-100 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div class="flex flex-col">
                                        <span class="text-xs text-gray-500 dark:text-gray-400 mb-1">Next Build Number</span>
                                        <span class="font-mono text-2xl font-bold text-blue-600 dark:text-blue-400">#{{ $this->nextVersionCode }}</span>
                                    </div>
                                    <svg class="w-8 h-8 text-blue-300 dark:text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="mt-3 pt-3 border-t border-blue-200 dark:border-gray-700">
                                    <p class="text-xs text-gray-600 dark:text-gray-400 flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-blue-500 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        Build number automatically increments with each build
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Info box -->
                        <div class="col-span-2 p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-start">
                                <svg class="flex-shrink-0 inline w-4 h-4 me-3 mt-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                                </svg>
                                <div>
                                    <span class="font-medium">This build will cost {{ $creditCost }} credit{{ $creditCost > 1 ? 's' : '' }}</span>
                                    <p class="mt-1 text-xs">You'll receive an email when the build is ready to download.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="flex items-center justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-2">
                            <button type="button"
                                    wire:click="closeModal"
                                    class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-hidden focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                                Cancel
                            </button>
                            <button type="submit"
                                    @if(!$this->canSubmit) disabled @endif
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 disabled:opacity-50 disabled:cursor-not-allowed">
                                Start Build
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
