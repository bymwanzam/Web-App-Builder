<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg">
    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="flex -mb-px" aria-label="Tabs">
            <button wire:click="setTab('jobs')"
                    class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm {{ $activeTab === 'jobs' ? 'border-blue-500 text-blue-600 dark:text-blue-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Jobs
            </button>
            <button wire:click="setTab('logs')"
                    class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm {{ $activeTab === 'logs' ? 'border-blue-500 text-blue-600 dark:text-blue-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Execution Logs
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="p-6">
        <!-- Jobs Tab -->
        <div x-show="$wire.activeTab === 'jobs'" x-cloak>
            <livewire:admin.cronjobs-table />
        </div>

        <!-- Execution Logs Tab -->
        <div x-show="$wire.activeTab === 'logs'" x-cloak>
            <livewire:admin.cron-logs-table />
        </div>
    </div>
</div>
