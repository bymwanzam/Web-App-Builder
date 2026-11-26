<div>
    <div class="relative overflow-x-auto bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
        <!-- Filters -->
        <div class="flex flex-col items-center justify-between p-4 space-y-3 md:flex-row md:space-y-0 md:space-x-4">
            <div class="w-full md:w-1/3">
                <label for="audit-search" class="sr-only">Search</label>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" id="audit-search" class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Search by user, action, IP...">
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Date Range -->
                <div class="flex items-center gap-2">
                    <input wire:model.live="dateFrom" type="date" class="block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <span class="text-gray-500 dark:text-gray-400">to</span>
                    <input wire:model.live="dateTo" type="date" class="block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                @if($search || !empty($actionFilter) || $dateFrom || $dateTo)
                    <button wire:click="clearFilters" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        Clear filters
                    </button>
                @endif
            </div>
        </div>

        <!-- Action Type Filter -->
        @if($actionTypes->isNotEmpty())
            <div class="px-4 pb-4 flex flex-wrap gap-2">
                @foreach($actionTypes as $action)
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model.live="actionFilter" value="{{ $action }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ ucwords(str_replace('_', ' ', $action)) }}</span>
                    </label>
                @endforeach
            </div>
        @endif

        <!-- Table -->
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Date/Time</th>
                    <th scope="col" class="px-6 py-3">Action</th>
                    <th scope="col" class="px-6 py-3">User</th>
                    <th scope="col" class="px-6 py-3">Actor</th>
                    <th scope="col" class="px-6 py-3">IP Address</th>
                    <th scope="col" class="px-6 py-3">Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span title="{{ $log->created_at->format('M d, Y g:i:s A') }}">
                                {{ $log->created_at->diffForHumans() }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if(str_contains($log->action, 'deleted') || str_contains($log->action, 'failed'))
                                    bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                @elseif(str_contains($log->action, 'cancelled'))
                                    bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                @elseif(str_contains($log->action, 'admin'))
                                    bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300
                                @else
                                    bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                @endif
                            ">
                                {{ ucwords(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($log->user)
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-600 dark:text-gray-300">
                                            {{ strtoupper(substr($log->user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $log->user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->user->email }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400">System</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($log->actor)
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $log->actor->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Admin</div>
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs font-mono">
                            {{ $log->ip_address ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($log->old_values || $log->new_values || $log->metadata)
                                <button
                                    type="button"
                                    x-data="{ open: false }"
                                    @click="open = !open"
                                    class="text-blue-600 dark:text-blue-400 hover:underline text-xs"
                                >
                                    View Details
                                </button>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            No audit logs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="p-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>
