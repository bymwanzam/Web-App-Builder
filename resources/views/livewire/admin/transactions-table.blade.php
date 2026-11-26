<div>
    <div class="relative overflow-x-auto bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
    <div class="flex flex-col items-center justify-between p-4 space-y-3 md:flex-row md:space-y-0 md:space-x-4">
        <div class="w-full md:w-1/2">
            <form class="flex items-center">
                <label for="transactions-search" class="sr-only">Search</label>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           id="transactions-search"
                           class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                           placeholder="Search">
                </div>
            </form>
        </div>
        <div class="flex flex-col items-stretch justify-end flex-shrink-0 w-full space-y-2 md:w-auto md:flex-row md:space-y-0 md:items-center md:space-x-3">
            <div class="flex items-center w-full space-x-3 md:w-auto">
                <button id="filterDropdownButton" data-dropdown-toggle="filterDropdown" type="button" class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg md:w-auto focus:outline-hidden hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="w-4 h-4 mr-2 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                    </svg>
                    Filter
                    <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                    </svg>
                </button>
                <div id="filterDropdown" class="z-10 hidden w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
                    <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Status</h6>
                    <ul class="space-y-2 text-sm" aria-labelledby="filterDropdownButton">
                        <li class="flex items-center">
                            <input id="trx-status-completed" type="checkbox" wire:model.live="statusFilter" value="completed" class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="trx-status-completed" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Completed</label>
                        </li>
                        <li class="flex items-center">
                            <input id="trx-status-failed" type="checkbox" wire:model.live="statusFilter" value="failed" class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="trx-status-failed" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Failed</label>
                        </li>
                        <li class="flex items-center">
                            <input id="trx-status-pending" type="checkbox" wire:model.live="statusFilter" value="pending" class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="trx-status-pending" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Pending</label>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">ID</th>
                <th scope="col" class="px-6 py-3">User</th>
                <th scope="col" class="px-6 py-3">Amount</th>
                <th scope="col" class="px-6 py-3">Payment Method</th>
                <th scope="col" class="px-6 py-3">Status</th>
                <th scope="col" class="px-6 py-3">Date</th>
                <th scope="col" class="px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-6 py-4 font-mono text-xs">#{{ $transaction->transaction_id }}</td>
                    <td class="px-6 py-4">{{ $transaction->user->email }}</td>
                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">${{ number_format($transaction->amount, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $transaction->payment_method }}</td>
                    <td class="px-6 py-4">
                        @if($transaction->status === 'completed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Completed</span>
                        @elseif($transaction->status === 'failed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Failed</span>
                        @elseif($transaction->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Pending</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">{{ ucfirst($transaction->status) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $transaction->transaction_date->format('M j, Y') }}</td>
                    <td class="px-6 py-4">
                        @if($transaction->status === 'pending' && $transaction->payment_method === 'Bank Transfer')
                            <button
                                wire:click="confirmApprove({{ $transaction->id }})"
                                type="button"
                                class="text-green-600 hover:text-green-800 dark:text-green-400 hover:underline mr-3"
                            >
                                Approve
                            </button>
                            <button
                                wire:click="confirmDecline({{ $transaction->id }})"
                                type="button"
                                class="text-red-600 hover:text-red-800 dark:text-red-400 hover:underline"
                            >
                                Decline
                            </button>
                        @else
                            <span class="text-xs text-gray-400 dark:text-gray-500">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="bg-white dark:bg-gray-800">
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        No transactions found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <x-table-pagination :paginator="$transactions" />
    </div>

    <!-- Approve Confirmation Modal -->
    <template x-teleport="body">
    <div x-data="{ show: @entangle('confirmApproveId').live }"
         x-show="show !== null"
         x-cloak
         @keydown.escape.window="show = null"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div @click.away="$wire.confirmApproveId = null" class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Confirm Approval</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Are you sure you want to approve this bank transfer payment? This will activate the user's subscription.
            </p>
            <div class="flex gap-3 justify-end">
                <button wire:click="$set('confirmApproveId', null)" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-hidden focus:ring-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                    Cancel
                </button>
                <button wire:click="approveTransaction($wire.confirmApproveId)" type="button" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:outline-hidden focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                    Approve Payment
                </button>
            </div>
        </div>
    </div>
    </template>

    <!-- Decline Confirmation Modal -->
    <template x-teleport="body">
    <div x-data="{ show: @entangle('confirmDeclineId').live }"
         x-show="show !== null"
         x-cloak
         @keydown.escape.window="show = null"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div @click.away="$wire.confirmDeclineId = null" class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Confirm Decline</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Are you sure you want to decline this bank transfer payment? This will cancel the subscription and notify the user.
            </p>
            <div class="flex gap-3 justify-end">
                <button wire:click="$set('confirmDeclineId', null)" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-hidden focus:ring-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                    Cancel
                </button>
                <button wire:click="declineTransaction($wire.confirmDeclineId)" type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-hidden focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                    Decline Payment
                </button>
            </div>
        </div>
    </div>
    </template>
</div>
