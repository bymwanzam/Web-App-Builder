<div x-data="{ show: @entangle('showModal') }">
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
                        Upload Signing Key
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
                <form wire:submit="save">
                    <div class="space-y-4 mb-6">
                        <!-- Platform Info -->
                        <div class="p-3 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-900 rounded-lg border border-green-200 dark:border-green-800">
                            <div class="flex items-center space-x-2">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.6 9.48l1.84-3.18c.16-.31.04-.69-.26-.85a.637.637 0 0 0-.83.22l-1.88 3.24a11.43 11.43 0 0 0-8.94 0L5.65 5.67a.643.643 0 0 0-.87-.2c-.28.18-.37.54-.22.83L6.4 9.48A10.81 10.81 0 0 0 1 18h22a10.81 10.81 0 0 0-5.4-8.52M7 15.25a1.25 1.25 0 1 1 0-2.5 1.25 1.25 0 0 1 0 2.5m10 0a1.25 1.25 0 1 1 0-2.5 1.25 1.25 0 0 1 0 2.5"/>
                                </svg>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">Android Keystore Upload</span>
                            </div>
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="keystore_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Keystore Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="keystore_name" wire:model="keystore_name"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                   placeholder="My Release Keystore">
                            @error('keystore_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Keystore File -->
                        <div>
                            <x-file-upload
                                wire-model="keystore_file"
                                accept=".jks,.keystore,.ks"
                                label="Keystore File *"
                                help-text=".jks, .keystore, or .ks file (Max 5MB)"
                                :max-size="5"
                                preview-type="file"
                            />
                        </div>

                        <!-- Upload custom key toggle -->
                        <div class="p-3 bg-blue-50 dark:bg-gray-900 rounded-lg border border-blue-200 dark:border-gray-700">
                            <x-toggle
                                wire-model="upload_custom_key"
                                :live="true"
                                id="upload_custom_key"
                                label="Upload custom key"
                            />
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Uncheck this if using auto-generated debug keystore
                            </p>
                        </div>

                        @if($upload_custom_key)
                            <!-- Alias -->
                            <div>
                                <label for="keystore_alias" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Alias <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="keystore_alias" wire:model="keystore_alias"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                       placeholder="my-key-alias">
                                @error('keystore_alias')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="keystore_password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <input type="password" id="keystore_password" wire:model="keystore_password"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                       placeholder="••••••••">
                                @error('keystore_password')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Used for both keystore and key access
                                </p>
                            </div>
                        @endif
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
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Upload
                            </button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </template>
</div>
