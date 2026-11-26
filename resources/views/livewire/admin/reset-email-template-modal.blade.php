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
                 class="relative z-10 inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">

                <!-- Modal content -->
                <div class="text-center">
                    <!-- Warning icon -->
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 dark:bg-amber-900/20 mb-4">
                        <svg class="h-6 w-6 text-amber-600 dark:text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>

                    <!-- Title -->
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Reset Email Template
                    </h3>

                    <!-- Message -->
                    <div class="mb-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Are you sure you want to reset
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $templateName }}</span>
                            to its default content?
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            Any customizations you've made will be lost.
                        </p>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex items-center justify-center gap-3">
                        <button type="button"
                                wire:click="closeModal"
                                class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-hidden focus:ring-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-600 dark:hover:text-white dark:focus:ring-gray-600">
                            Cancel
                        </button>
                        @if(config('app.demo'))
                            <button type="button" disabled title="Disabled in demo mode"
                                    class="px-5 py-2.5 text-sm font-medium text-white bg-amber-600 rounded-lg cursor-not-allowed opacity-50">
                                Reset
                            </button>
                        @else
                            <button type="button"
                                    wire:click="resetTemplate"
                                    class="px-5 py-2.5 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 focus:ring-4 focus:outline-hidden focus:ring-amber-300 dark:bg-amber-600 dark:hover:bg-amber-700 dark:focus:ring-amber-900">
                                Reset
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
