<div>
    {{-- Trigger Button --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-900 dark:text-white">Delete Account</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Permanently delete your account and all data</p>
        </div>
        @if($pendingDeletion)
            <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-yellow-800 bg-yellow-100 rounded-full dark:bg-yellow-900 dark:text-yellow-300">
                Deletion Pending
            </span>
        @endif
        <button
            wire:click="openModal"
            type="button"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
        >
            @if($pendingDeletion)
                View Status
            @else
                Delete Account
            @endif
        </button>
    </div>

    {{-- Modal --}}
    <div
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        x-cloak
        @keydown.escape.window="show = false"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto"
    >
        <!-- Backdrop -->
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-opacity-80"
            @click="show = false"
        ></div>

        <!-- Modal -->
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-md p-4"
        >
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                <!-- Header -->
                <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.072 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        Delete Account
                    </h3>
                    <button @click="show = false" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-4 space-y-4">
                    @if($pendingDeletion)
                        {{-- Show pending deletion status --}}
                        <div class="p-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-700 dark:text-yellow-300">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium">Account Deletion Pending</span>
                            </div>
                            <p class="mb-2">
                                Your account is scheduled to be deleted on
                                <strong>{{ $deletionRequest->scheduled_at->format('F j, Y \a\t g:i A') }}</strong>.
                            </p>
                            <p class="text-xs">
                                Check your email for details and a link to cancel if you've changed your mind.
                            </p>
                        </div>

                        <button
                            wire:click="cancelDeletion"
                            wire:loading.attr="disabled"
                            type="button"
                            class="w-full text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                        >
                            <span wire:loading.remove wire:target="cancelDeletion">Cancel Account Deletion</span>
                            <span wire:loading wire:target="cancelDeletion">Cancelling...</span>
                        </button>
                    @else
                        {{-- Show deletion request form --}}
                        <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-700 dark:text-red-400">
                            <p class="mb-2 font-medium">This action cannot be undone!</p>
                            <p>When you delete your account:</p>
                            <ul class="list-disc list-inside mt-2 space-y-1">
                                <li>All your apps will be permanently deleted</li>
                                <li>All build history and artifacts will be removed</li>
                                <li>Your subscription will be cancelled</li>
                                <li>All personal data will be erased</li>
                            </ul>
                        </div>

                        <div class="p-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-700 dark:text-blue-400">
                            <p>
                                Your account will be scheduled for deletion in <strong>7 days</strong>.
                                You'll receive an email with a link to cancel if you change your mind.
                            </p>
                        </div>

                        <form wire:submit.prevent="requestDeletion" class="space-y-4">
                            <div>
                                <label for="delete-password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Enter your password to confirm
                                </label>
                                <input
                                    wire:model="password"
                                    type="password"
                                    id="delete-password"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500 @error('password') border-red-500 @enderror"
                                    placeholder="Your password"
                                    required
                                >
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex gap-3">
                                <button
                                    type="button"
                                    @click="show = false"
                                    class="flex-1 text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    class="flex-1 text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900 disabled:opacity-50"
                                >
                                    <span wire:loading.remove wire:target="requestDeletion">Delete My Account</span>
                                    <span wire:loading wire:target="requestDeletion">Processing...</span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
