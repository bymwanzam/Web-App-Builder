<div class="space-y-6">
    <!-- Upgrade Prompt -->
    <div class="p-4 bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                    Upgrade for Live Previews
                </h3>
                <p class="text-xs text-gray-600 dark:text-gray-300 mb-3">
                    Test your app instantly in the browser without downloading.
                </p>
                <a href="{{ route('user.billing') }}"
                   wire:navigate
                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-hidden focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    View Plans
                </a>
            </div>
        </div>
    </div>

    <!-- Manual Testing Instructions -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Manual Testing Steps</h3>
            <span class="text-xs text-gray-500 dark:text-gray-400">3 steps</span>
        </div>

        @foreach($manualTestSteps as $step)
            <div class="flex gap-4">
                <!-- Step Number -->
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold">
                        {{ $step['number'] }}
                    </div>
                </div>

                <!-- Step Content -->
                <div class="flex-1 pb-4 {{ !$loop->last ? 'border-b border-gray-200 dark:border-gray-700' : '' }}">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                        {{ $step['title'] }}
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                        {{ $step['description'] }}
                    </p>

                    @if($step['action'] === 'download')
                        <!-- QR Code & Download -->
                        <div class="space-y-4">
                            @if($this->qrCodeSvg)
                                <div class="flex justify-center p-3 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                    <div class="w-32 h-32 bg-white rounded">
                                        {!! $this->qrCodeSvg !!}
                                    </div>
                                </div>
                                <p class="text-xs text-center text-gray-500 dark:text-gray-400">Scan QR code to download</p>
                            @endif

                            <div class="space-y-2">
                                <a href="{{ $step['download_url'] }}"
                                   class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:outline-hidden focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download {{ strtoupper($build->build_format) }}
                                </a>
                                <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                    {{ $this->formatFileSize($build->artifact_size) }}
                                </p>
                            </div>
                        </div>
                    @elseif($step['action'] === 'external_link')
                        <a href="{{ $step['url'] }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 focus:ring-4 focus:outline-hidden focus:ring-blue-300 dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-900/50 dark:focus:ring-blue-800">
                            Open Appetize.io
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Build Info -->
    <div class="grid grid-cols-2 gap-3 text-sm pt-4 border-t border-gray-200 dark:border-gray-700">
        <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <div class="text-gray-500 dark:text-gray-400 text-xs mb-1">Platform</div>
            <div class="font-medium text-gray-900 dark:text-white">{{ $platform }}</div>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <div class="text-gray-500 dark:text-gray-400 text-xs mb-1">Version</div>
            <div class="font-medium text-gray-900 dark:text-white">{{ $build->version_name }}</div>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <div class="text-gray-500 dark:text-gray-400 text-xs mb-1">Size</div>
            <div class="font-medium text-gray-900 dark:text-white">{{ $this->formatFileSize($build->artifact_size) }}</div>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <div class="text-gray-500 dark:text-gray-400 text-xs mb-1">Format</div>
            <div class="font-medium text-gray-900 dark:text-white uppercase">{{ $build->build_format }}</div>
        </div>
    </div>
</div>
