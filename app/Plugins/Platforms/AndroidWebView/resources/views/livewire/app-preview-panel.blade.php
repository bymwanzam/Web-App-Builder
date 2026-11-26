<aside class="fixed top-0 right-0 z-30 w-80 h-screen bg-gray-50 border-l border-gray-200 pt-16 px-6 pb-6 overflow-y-auto dark:bg-gray-900 dark:border-gray-700 hidden xl:block">

    <!-- App Preview -->
    @if($selectedBuild)
        <livewire:shared.app-preview
            :build="$selectedBuild"
            :platform="$selectedBuild->platform"
            :key="'preview-'.$selectedBuild->id"
        />
    @else
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center py-12 px-6 text-center">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                No Build Selected
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 max-w-xs">
                Select a completed build from the Builds tab to preview it here
            </p>
        </div>
    @endif

</aside>
