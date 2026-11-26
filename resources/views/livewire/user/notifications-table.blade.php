<div class="relative overflow-x-auto bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
    <!-- Search and Filters -->
    <div class="flex flex-col items-center justify-between p-4 space-y-3 md:flex-row md:space-y-0 md:space-x-4">
        <div class="w-full md:w-1/2">
            <label for="notifications-search" class="sr-only">Search</label>
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input wire:model.live="search" type="text" id="notifications-search" class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Search notifications...">
            </div>
        </div>
        <div class="flex flex-col items-stretch justify-end flex-shrink-0 w-full space-y-2 md:w-auto md:flex-row md:space-y-0 md:items-center md:space-x-3">
            <div class="flex items-center w-full space-x-3 md:w-auto">
                <!-- Filter Dropdown -->
                <button id="filterDropdownButton" data-dropdown-toggle="filterDropdown" class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg md:w-auto focus:outline-hidden hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="w-4 h-4 mr-2 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                    </svg>
                    Filter
                    <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                    </svg>
                </button>
                <div id="filterDropdown" class="z-10 hidden w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
                    <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Status</h6>
                    <ul class="space-y-2 text-sm" aria-labelledby="filterDropdownButton">
                        <li class="flex items-center">
                            <input wire:model.live="statusFilter" id="sent" type="checkbox" value="sent" class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="sent" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Sent</label>
                        </li>
                        <li class="flex items-center">
                            <input wire:model.live="statusFilter" id="pending" type="checkbox" value="pending" class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="pending" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Pending</label>
                        </li>
                        <li class="flex items-center">
                            <input wire:model.live="statusFilter" id="scheduled" type="checkbox" value="scheduled" class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="scheduled" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Scheduled</label>
                        </li>
                        <li class="flex items-center">
                            <input wire:model.live="statusFilter" id="failed" type="checkbox" value="failed" class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="failed" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Failed</label>
                        </li>
                    </ul>
                    @if($userApps->count() > 0)
                        <h6 class="mt-4 mb-3 text-sm font-medium text-gray-900 dark:text-white">App</h6>
                        <ul class="space-y-2 text-sm" aria-labelledby="filterDropdownButton">
                            @foreach($userApps as $app)
                                <li class="flex items-center">
                                    <input wire:model.live="appFilter" id="app-{{ $app->id }}" type="checkbox" value="{{ $app->id }}" class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                    <label for="app-{{ $app->id }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $app->name }}</label>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Title</th>
                <th scope="col" class="px-6 py-3">App</th>
                <th scope="col" class="px-6 py-3">Style</th>
                <th scope="col" class="px-6 py-3">Status</th>
                <th scope="col" class="px-6 py-3">Time</th>
                <th scope="col" class="px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($notifications as $notification)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900 dark:text-white">{{ Str::limit($notification->title, 40) }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($notification->body, 50) }}</div>
                    </td>
                    <td class="px-6 py-4">{{ $notification->app->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        @if($notification->notification_style === 'image')
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                                </svg>
                                Image
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/>
                                </svg>
                                Text
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $this->getStatusClass($notification->status) }}">
                            {{ ucfirst($notification->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($notification->status === 'scheduled' && $notification->scheduled_at)
                            <span title="Scheduled for {{ $notification->scheduled_at->format('M d, Y g:i A') }}">
                                {{ $notification->scheduled_at->diffForHumans() }}
                            </span>
                        @elseif($notification->sent_at)
                            <span title="{{ $notification->sent_at->format('M d, Y g:i A') }}">
                                {{ $notification->sent_at->diffForHumans() }}
                            </span>
                        @else
                            <span title="{{ $notification->created_at->format('M d, Y g:i A') }}">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if(in_array($notification->status, ['scheduled', 'pending']))
                            <button wire:click="$dispatch('delete-notification', { id: {{ $notification->id }} })" class="text-red-600 hover:text-red-800 dark:text-red-400 hover:underline">
                                Cancel
                            </button>
                        @elseif($notification->status === 'failed')
                            <button title="{{ $notification->error_message }}" class="text-gray-500 cursor-help">
                                View Error
                            </button>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        @if($userApps->count() === 0)
                            No apps with push notifications enabled.
                        @else
                            No notifications found.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <x-table-pagination :paginator="$notifications" />
</div>
