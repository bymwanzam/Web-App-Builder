<div>
    @if($isProcessing)
        <!-- Processing State -->
        <div class="rounded-lg bg-white p-8 text-center dark:bg-gray-800">
            <!-- Spinner -->
            <div class="mx-auto mb-4 h-16 w-16">
                <svg class="animate-spin h-16 w-16 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <!-- Message -->
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Redirecting to Payment Gateway...</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Please wait while we redirect you to complete your payment.
            </p>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                Do not close this window or press the back button.
            </p>
        </div>
    @elseif($error)
        <!-- Error State -->
        <div class="rounded-lg bg-red-50 p-6 dark:bg-red-900/20">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Payment Initialization Failed</h3>
                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                        <p>{{ $error }}</p>
                    </div>
                    <div class="mt-4">
                        <button
                            type="button"
                            wire:click="initiatePayment"
                            class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 dark:bg-red-500 dark:hover:bg-red-400"
                        >
                            Try Again
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Ready to Initiate -->
        <div class="rounded-lg border-2 border-gray-200 bg-white p-6 dark:border-gray-600 dark:bg-gray-800">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Summary</h3>
            </div>

            @if($plan)
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Plan</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $plan->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Billing Cycle</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">Monthly</dd>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 pt-3 dark:border-gray-600">
                        <dt class="text-base font-semibold text-gray-900 dark:text-white">Total</dt>
                        <dd class="text-base font-bold text-gray-900 dark:text-white">${{ number_format($plan->price, 2) }}</dd>
                    </div>
                </dl>

                <div class="mt-6">
                    <button
                        type="button"
                        wire:click="initiatePayment"
                        wire:loading.attr="disabled"
                        class="w-full rounded-lg bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-xs hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-50 dark:bg-blue-500 dark:hover:bg-blue-400"
                    >
                        <span wire:loading.remove>Proceed to Payment</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>

                <p class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400">
                    You will be redirected to {{ $gateway ?? 'the payment gateway' }} to complete your payment securely.
                </p>
            @endif
        </div>
    @endif
</div>
