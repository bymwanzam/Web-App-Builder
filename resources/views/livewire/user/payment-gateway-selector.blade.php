<div class="space-y-4">
    <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Select Payment Method</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose how you'd like to pay for your subscription</p>
    </div>

    @if(empty($gateways))
        <div class="rounded-lg bg-yellow-50 p-4 dark:bg-yellow-900/20">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">No payment gateways available</h3>
                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                        <p>Payment gateways need to be configured by an administrator. Please contact support.</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($gateways as $gateway)
                <button
                    type="button"
                    wire:click="selectGateway('{{ $gateway['slug'] }}')"
                    class="relative flex items-start rounded-lg border-2 p-4 transition-all hover:bg-gray-50 dark:hover:bg-gray-700/50
                        {{ $selectedGateway === $gateway['slug']
                            ? 'border-blue-600 bg-blue-50 dark:border-blue-500 dark:bg-blue-900/20'
                            : 'border-gray-200 bg-white dark:border-gray-600 dark:bg-gray-800' }}"
                >
                    <!-- Selection indicator -->
                    @if($selectedGateway === $gateway['slug'])
                        <div class="absolute right-4 top-4">
                            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif

                    <div class="flex-1 text-left">
                        <!-- Gateway Icon -->
                        <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700">
                            @if(str_contains($gateway['icon'], 'http') || file_exists(public_path($gateway['icon'])))
                                <img src="{{ $gateway['icon'] }}" alt="{{ $gateway['name'] }}" class="h-8 w-8 object-contain">
                            @else
                                <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            @endif
                        </div>

                        <!-- Gateway Info -->
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $gateway['name'] }}
                        </h4>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ $gateway['description'] }}
                        </p>
                    </div>
                </button>
            @endforeach
        </div>
    @endif
</div>
