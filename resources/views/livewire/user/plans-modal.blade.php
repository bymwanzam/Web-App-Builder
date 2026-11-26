<div>
    @teleport('body')
    <!-- Plans Modal -->
    <div x-data="{ show: @entangle('showModal') }"
         x-show="show"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="show = false"
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500/75 dark:bg-gray-900/75"
                 @click="$wire.close()"
                 aria-hidden="true"></div>

            <!-- Center modal -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative z-10 inline-block w-full max-w-4xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">

                <!-- Modal header -->
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        @if($showGatewaySelector)
                            <button type="button"
                                    wire:click="backToPlans"
                                    class="text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                            </button>
                        @endif
                        @if($showInstructions)
                            <button type="button"
                                    wire:click="backToGatewaysFromInstructions"
                                    class="text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                            </button>
                        @endif
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            @if($showInstructions)
                                Bank Transfer Instructions
                            @elseif($showGatewaySelector)
                                Choose Payment Method
                            @else
                                Choose Your Plan
                            @endif
                        </h3>
                    </div>
                    <button type="button"
                            wire:click="close"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div>
                    @if (session()->has('message'))
                        <div class="mb-4 p-4 text-sm text-green-800 bg-green-100 rounded-lg dark:bg-green-900 dark:text-green-300">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="mb-4 p-4 text-sm text-red-800 bg-red-100 rounded-lg dark:bg-red-900 dark:text-red-300">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($showInstructions)
                        <!-- Bank Transfer Instructions -->
                        <div class="space-y-6">
                            <!-- Selected Plan Info -->
                            @if($selectedPlanForPayment)
                                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg dark:bg-gray-700 dark:border-gray-600">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        Selected Plan: <span class="font-semibold">{{ $selectedPlanForPayment->name }}</span>
                                        - ${{ number_format($selectedPlanForPayment->price, 0) }}/month
                                    </p>
                                </div>
                            @endif

                            <!-- Instructions Card -->
                            <div class="p-6 bg-white border-2 border-blue-500 rounded-lg dark:bg-gray-700 dark:border-blue-500">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Bank Transfer Details</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Please use the following information to complete your payment</p>
                                    </div>
                                </div>

                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line text-left">
                                        {!! nl2br(e($bankTransferInstructions)) !!}
                                    </div>
                                </div>
                            </div>

                            <!-- Important Notice -->
                            <div class="flex items-start gap-3 p-4 bg-yellow-50 border border-yellow-200 rounded-lg dark:bg-yellow-900/20 dark:border-yellow-800">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div class="text-sm">
                                    <p class="font-semibold text-yellow-800 dark:text-yellow-400 mb-1">Important</p>
                                    <p class="text-yellow-700 dark:text-yellow-300">
                                        Your subscription will be activated once we confirm your payment.
                                        You can view these instructions anytime on your billing page.
                                    </p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-center pt-4">
                                <button type="button"
                                        wire:click="confirmBankTransfer"
                                        wire:loading.attr="disabled"
                                        wire:target="confirmBankTransfer"
                                        class="px-6 py-2.5 text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-medium dark:bg-blue-500 dark:hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                    <svg wire:loading wire:target="confirmBankTransfer" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="confirmBankTransfer">Initiate Payment</span>
                                    <span wire:loading wire:target="confirmBankTransfer">Processing...</span>
                                </button>
                            </div>
                        </div>
                    @elseif($showGatewaySelector)
                        <!-- Payment Gateway Selector -->
                        <div class="space-y-4">
                            @if($selectedPlanForPayment)
                                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg dark:bg-gray-700 dark:border-gray-600">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        Selected Plan: <span class="font-semibold">{{ $selectedPlanForPayment->name }}</span>
                                        - ${{ number_format($selectedPlanForPayment->price, 0) }}/month
                                    </p>
                                </div>
                            @endif

                            @if(empty($availableGateways))
                                <div class="text-center py-12">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                        No Payment Methods Available
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Please contact support to complete your subscription.
                                    </p>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($availableGateways as $gateway)
                                        <button type="button"
                                                wire:click="selectGateway('{{ $gateway['slug'] }}')"
                                                wire:loading.attr="disabled"
                                                wire:target="selectGateway"
                                                class="flex items-center p-6 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:border-blue-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                            <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-lg flex items-center justify-center p-2 mr-4">
                                                <img src="{{ asset($gateway['icon']) }}" alt="{{ $gateway['name'] }}" class="w-full h-full object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 text-left">
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $gateway['name'] }}</h4>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $gateway['description'] }}</p>
                                            </div>
                                            <div wire:loading wire:target="selectGateway" class="ml-3">
                                                <svg class="animate-spin h-5 w-5 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                            <div wire:loading.remove wire:target="selectGateway" class="ml-3">
                                                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Plans Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($plans as $plan)
                            @php
                                $isCurrent = $this->isCurrentPlan($plan->id);
                                $isPopular = strtolower($plan->name) === 'pro';
                                $features = $plan->all_features;
                            @endphp

                            <div class="flex flex-col p-6 {{ $isPopular ? 'bg-blue-50 border-2 border-blue-600 dark:bg-gray-700 dark:border-blue-500' : 'bg-white border border-gray-200 dark:bg-gray-800 dark:border-gray-700' }} rounded-lg shadow">
                                @if($isPopular)
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $plan->name }}</h3>
                                        <span class="bg-blue-600 text-white text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-blue-500">Popular</span>
                                    </div>
                                @else
                                    <h3 class="mb-4 text-2xl font-semibold text-gray-900 dark:text-white">{{ $plan->name }}</h3>
                                @endif

                                <div class="flex items-baseline text-gray-900 dark:text-white mb-4">
                                    <span class="text-3xl font-extrabold">${{ number_format($plan->price, 0) }}</span>
                                    <span class="text-gray-500 dark:text-gray-400 ms-1">/month</span>
                                </div>

                                <ul class="mb-6 space-y-3 text-sm">
                                    @foreach($features as $feature)
                                        @php
                                            $included = is_array($feature) ? ($feature['included'] ?? true) : true;
                                            $featureName = is_array($feature) ? $feature['name'] : $feature;
                                        @endphp
                                        <li class="flex items-center {{ $included ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500' }}">
                                            @if($included)
                                                <svg class="w-4 h-4 text-green-500 me-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-red-500 me-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                            <span>{{ $featureName }}</span>
                                        </li>
                                    @endforeach
                                </ul>

                                @if($isCurrent)
                                    <button type="button" disabled class="text-gray-900 bg-white border border-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-700 dark:text-white dark:border-gray-600 mt-auto cursor-not-allowed opacity-50">
                                        Current Plan
                                    </button>
                                @elseif($plan->price == 0)
                                    <button type="button"
                                            wire:click="selectPlan({{ $plan->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="selectPlan"
                                            class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 mt-auto disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                        <svg wire:loading wire:target="selectPlan" class="animate-spin h-4 w-4 text-gray-900 dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="selectPlan">Select Free Plan</span>
                                        <span wire:loading wire:target="selectPlan">Processing...</span>
                                    </button>
                                @elseif($isPopular)
                                    <button type="button"
                                            wire:click="selectPlan({{ $plan->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="selectPlan"
                                            class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-500 dark:hover:bg-blue-600 mt-auto disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                        <svg wire:loading wire:target="selectPlan" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="selectPlan">{{ auth()->user()->isAdmin() ? 'Activate ' . $plan->name . ' (Admin)' : 'Upgrade to ' . $plan->name }}</span>
                                        <span wire:loading wire:target="selectPlan">Processing...</span>
                                    </button>
                                @else
                                    <button type="button"
                                            wire:click="selectPlan({{ $plan->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="selectPlan"
                                            class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-500 dark:hover:bg-blue-600 mt-auto disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                        <svg wire:loading wire:target="selectPlan" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="selectPlan">{{ auth()->user()->isAdmin() ? 'Activate ' . $plan->name . ' (Admin)' : 'Upgrade to ' . $plan->name }}</span>
                                        <span wire:loading wire:target="selectPlan">Processing...</span>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
    @endteleport
</div>
