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

                @if($build)
                    <!-- Modal header -->
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Download Build
                            </h3>
                            <div class="flex items-center gap-2 mt-2">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ ucfirst($build->platform) }} v{{ $build->version_name }} ({{ $build->version_code }})
                                </p>
                                @if($build->build_type === 'release')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800">
                                        Release
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                        Debug
                                    </span>
                                @endif
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $build->formatted_size }}</span>
                            </div>
                        </div>
                        <button type="button"
                                wire:click="closeModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal body -->
                    <div class="space-y-6">
                        @if($build->platform === 'android-webview' && $build->build_format === 'aab')
                            <!-- AAB Warning - No QR Code -->
                            <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                                <div class="flex gap-3">
                                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-semibold text-amber-900 dark:text-amber-300 mb-1">Android App Bundle (AAB)</h4>
                                        <p class="text-xs text-amber-800 dark:text-amber-400">
                                            This is an AAB file which cannot be directly installed on devices. AAB files are designed for upload to Google Play Console, where Google will generate optimized APKs for users.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- QR Code Section (APK only) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex flex-col items-center justify-center p-6 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                    @if($this->qrCodeSvg)
                                        <div class="p-4 bg-white rounded-lg">
                                            {!! $this->qrCodeSvg !!}
                                        </div>
                                        <p class="mt-3 text-xs text-center text-gray-500 dark:text-gray-400">
                                            Scan with your Android device
                                        </p>
                                    @else
                                        <div class="flex flex-col items-center justify-center py-8">
                                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">QR code unavailable</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Download Link Section -->
                                <div class="flex flex-col justify-center space-y-4">
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Download Options</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                            Scan the QR code with your mobile device or use the direct download link below.
                                        </p>
                                    </div>

                                    <a href="{{ $build->download_url }}"
                                       class="inline-flex items-center justify-center gap-2 w-full px-5 py-3 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-hidden focus:ring-blue-300 rounded-lg dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Download {{ strtoupper($build->build_format ?? 'APK') }}
                                    </a>

                                    @if($build->platform === 'android-webview' && $build->build_format === 'apk')
                                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                            <p class="text-xs text-blue-800 dark:text-blue-300">
                                                <strong>Installation:</strong> You may need to enable "Install from Unknown Sources" in your Android settings.
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- AAB Download Section (outside grid) -->
                        @if($build->platform === 'android-webview' && $build->build_format === 'aab')
                            <div class="flex flex-col space-y-4">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Download AAB File</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                        Upload this file to Google Play Console to publish your app.
                                    </p>
                                </div>

                                <a href="{{ $build->download_url }}"
                                   class="inline-flex items-center justify-center gap-2 w-full px-5 py-3 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-hidden focus:ring-blue-300 rounded-lg dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download AAB File
                                </a>

                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <p class="text-xs text-blue-800 dark:text-blue-300">
                                        <strong>Next Steps:</strong> Upload this AAB file to Google Play Console under Release → Production/Testing. Google will automatically generate optimized APKs for your users.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Modal footer -->
                    <div class="flex items-center justify-end pt-4 mt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button"
                                wire:click="closeModal"
                                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-hidden focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                            Close
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
