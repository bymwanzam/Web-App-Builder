<div>
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Job Name</th>
                    <th scope="col" class="px-6 py-3">Schedule</th>
                    <th scope="col" class="px-6 py-3">Last Run</th>
                    <th scope="col" class="px-6 py-3">Next Run</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->jobsWithStats as $job)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            {{ $job['name'] }}
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $job['cron'] }}</p>
                        </td>
                        <td class="px-6 py-4">
                            {{ $job['schedule'] }}
                        </td>
                        <td class="px-6 py-4">
                            @if($job['last_run'])
                                {{ $job['last_run']->diffForHumans() }}
                            @else
                                <span class="text-gray-400">Never</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($job['next_run'])
                                {{ $job['next_run']->diffForHumans() }}
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($job['status'] === 'success')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    Active
                                </span>
                            @elseif($job['status'] === 'failed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                    Failed
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <button wire:click="triggerJob('{{ $job['class'] }}')"
                                    wire:loading.attr="disabled"
                                    wire:target="triggerJob"
                                    type="button"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-purple-700 rounded-lg hover:bg-purple-800 focus:ring-4 focus:outline-hidden focus:ring-purple-300 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-800 disabled:opacity-50 disabled:cursor-not-allowed">
                                <!-- Play icon - hide when loading -->
                                <svg wire:loading.remove wire:target="triggerJob" class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <!-- Loading spinner - show when loading -->
                                <svg wire:loading wire:target="triggerJob" class="w-3 h-3 mr-1.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <!-- Button text - changes when loading -->
                                <span wire:loading.remove wire:target="triggerJob">Execute</span>
                                <span wire:loading wire:target="triggerJob">Executing...</span>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
