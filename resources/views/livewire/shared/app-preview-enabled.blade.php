<div class="space-y-4">
    <!-- Remaining Minutes Counter -->
    @if($remainingMinutes < 999999)
        <div class="relative overflow-hidden rounded-xl border {{ $remainingMinutes < 60 ? 'bg-gradient-to-br from-red-50 to-red-100 border-red-200 dark:from-red-900/30 dark:to-red-900/10 dark:border-red-800' : 'bg-gradient-to-br from-blue-50 to-blue-100 border-blue-200 dark:from-blue-900/30 dark:to-blue-900/10 dark:border-blue-800' }}">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-lg {{ $remainingMinutes < 60 ? 'bg-red-500/10 dark:bg-red-500/20' : 'bg-blue-500/10 dark:bg-blue-500/20' }}">
                            <svg class="w-5 h-5 {{ $remainingMinutes < 60 ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-medium {{ $remainingMinutes < 60 ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400' }} uppercase tracking-wide mb-0.5">
                                Preview Time
                            </div>
                            <div class="text-2xl font-bold {{ $remainingMinutes < 60 ? 'text-red-900 dark:text-red-100' : 'text-blue-900 dark:text-blue-100' }}">
                                {{ $remainingMinutes }}
                                <span class="text-sm font-normal {{ $remainingMinutes < 60 ? 'text-red-700 dark:text-red-300' : 'text-blue-700 dark:text-blue-300' }}">min</span>
                            </div>
                            <div class="text-xs {{ $remainingMinutes < 60 ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400' }}">
                                remaining this month
                            </div>
                        </div>
                    </div>
                    @if($remainingMinutes < 60)
                        <a href="{{ route('user.billing') }}" wire:navigate class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-xs">
                            Upgrade
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="relative overflow-hidden rounded-xl border bg-gradient-to-br from-green-50 to-emerald-100 border-green-200 dark:from-green-900/30 dark:to-emerald-900/10 dark:border-green-800">
            <div class="p-4">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-lg bg-green-500/10 dark:bg-green-500/20">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide mb-0.5">
                            Premium Access
                        </div>
                        <div class="text-lg font-bold text-green-900 dark:text-green-100">
                            Unlimited Preview Time
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Appetize Preview Container -->
    <div class="relative bg-gray-900 rounded-lg overflow-hidden shadow-xl" style="aspect-ratio: 9/19.5;">
        <iframe
            src="{{ $previewUrl }}"
            allow="camera; microphone; geolocation; payment"
            scrolling="no"
            class="w-full h-full border-0"
            wire:init="trackSessionStart">
        </iframe>
    </div>

    <!-- Build Info -->
    <div class="grid grid-cols-2 gap-3 text-sm">
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

    <!-- Help Text -->
    <div class="flex items-start gap-2 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-blue-700 dark:text-blue-300">
            <p class="font-medium mb-1">Interactive Preview</p>
            <p class="text-xs">Tap, swipe, and interact with your app directly in the browser. Test your app instantly without downloading.</p>
        </div>
    </div>
</div>
