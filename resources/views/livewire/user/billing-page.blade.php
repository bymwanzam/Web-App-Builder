<div>
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M4 5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H4Zm0 6h16v6H4v-6Z" clip-rule="evenodd"/>
                    <path fill-rule="evenodd" d="M5 14a1 1 0 0 1 1-1h2a1 1 0 1 1 0 2H6a1 1 0 0 1-1-1Zm5 0a1 1 0 0 1 1-1h5a1 1 0 1 1 0 2h-5a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Billing</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 hidden sm:block">
                    Manage your subscription and billing
                </p>
            </div>
        </div>
        <button type="button" wire:click="$dispatch('show-plans-modal')" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-width="2" d="M8 7V6a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1M3 18v-7a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Zm8-3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
            </svg>
            <span class="hidden sm:inline">Change Plan</span>
        </button>
    </div>

    <!-- Current Subscription Card -->
    @if($currentSubscription)
        <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Subscription</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Plan Info -->
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Plan</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $currentSubscription->plan->name ?? 'N/A' }}
                    </dd>
                    <dd class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                        ${{ number_format($currentSubscription->amount, 2) }}/month
                    </dd>
                </div>

                <!-- Payment Method -->
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Method</dt>
                    <dd class="mt-1">
                        @if($currentSubscription->payment_method)
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($currentSubscription->payment_method) }}</span>
                            </div>
                        @else
                            <span class="text-sm text-gray-500 dark:text-gray-400">No payment method</span>
                        @endif
                    </dd>
                </div>

                <!-- Status & Next Billing -->
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1">
                        @if($currentSubscription->status === 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                Active
                            </span>
                        @elseif($currentSubscription->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                Pending
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                {{ ucfirst($currentSubscription->status) }}
                            </span>
                        @endif
                    </dd>
                    @if($currentSubscription->renewal_at)
                        <dd class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                            Next billing: {{ $currentSubscription->renewal_at->format('M j, Y') }}
                        </dd>
                    @endif
                </div>
            </div>

            <!-- Bank Transfer Instructions Card (shown for pending Bank Transfer payments) -->
            @if($currentSubscription->status === 'pending' && $currentSubscription->payment_method === 'Bank Transfer')
                @php
                    $transaction = \App\Models\Transaction::where('subscription_id', $currentSubscription->id)
                        ->where('status', 'pending')
                        ->orderBy('transaction_date', 'desc')
                        ->first();
                    $instructions = $transaction && isset($transaction->metadata['bank_transfer_instructions'])
                        ? $transaction->metadata['bank_transfer_instructions']
                        : null;
                    $bankTransferPluginActive = \App\Models\Plugin::active()
                        ->byType('payment_gateway')
                        ->where('slug', 'bank-transfer')
                        ->exists();
                @endphp

                @if(!$bankTransferPluginActive)
                    {{-- Show notice when plugin is inactive --}}
                    <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold text-yellow-900 dark:text-yellow-200 mb-2">Payment Method No Longer Available</h3>
                                <p class="text-sm text-yellow-800 dark:text-yellow-300 mb-3">
                                    The Bank Transfer payment method is no longer available. Your pending subscription payment is still being processed.
                                </p>
                                <p class="text-sm text-yellow-800 dark:text-yellow-300">
                                    If you have questions about your pending payment, please contact our support team with your transaction reference: <strong class="font-mono">{{ $transaction ? $transaction->transaction_id : 'N/A' }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($instructions)
                    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-200 mb-2">
                                    🏦 Complete Your Bank Transfer Payment
                                </h3>
                                <p class="text-sm text-blue-800 dark:text-blue-300 mb-4">
                                    Your subscription is pending payment confirmation. Please complete the bank transfer using the details below:
                                </p>

                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-4 border border-blue-200 dark:border-blue-700">
                                    <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line text-left">{{ $instructions }}</div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-200 dark:border-blue-700">
                                        <div class="text-xs font-medium text-blue-700 dark:text-blue-400 mb-1">💰 Amount to Transfer</div>
                                        <div class="text-lg font-bold text-gray-900 dark:text-white">
                                            ${{ number_format($currentSubscription->amount, 2) }} {{ $transaction->currency }}
                                        </div>
                                    </div>
                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-200 dark:border-blue-700">
                                        <div class="text-xs font-medium text-blue-700 dark:text-blue-400 mb-1">📋 Reference Number</div>
                                        <div class="text-sm font-mono font-semibold text-gray-900 dark:text-white">
                                            {{ $transaction->transaction_id }}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-start gap-2 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div class="text-xs text-yellow-800 dark:text-yellow-300">
                                        <strong>Important:</strong> Please include the reference number "{{ $transaction->transaction_id }}" in your bank transfer. This helps us identify your payment quickly. Once we verify your payment, your subscription will be activated automatically.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    @else
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 mb-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-200">No Active Subscription</h3>
                    <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                        Subscribe to a plan to unlock all features and start building your apps.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Awaiting Bank Payment Section -->
    @if($pendingSubscription)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-2 border-yellow-400 dark:border-yellow-600 rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 dark:bg-yellow-800 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-yellow-900 dark:text-yellow-200 mb-2">
                        🏦 Awaiting Bank Payment
                    </h2>
                    <p class="text-sm text-yellow-800 dark:text-yellow-300 mb-4">
                        Your <span class="font-semibold">{{ $pendingSubscription->plan->name }}</span> plan subscription is pending payment confirmation.
                    </p>

                    <!-- Instructions Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-4 border border-yellow-300 dark:border-yellow-700">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                            Bank Transfer Instructions
                        </h3>
                        @php
                            $transaction = \App\Models\Transaction::where('subscription_id', $pendingSubscription->id)
                                ->where('status', 'pending')
                                ->orderBy('transaction_date', 'desc')
                                ->first();
                            $instructions = $transaction && isset($transaction->metadata['bank_transfer_instructions'])
                                ? $transaction->metadata['bank_transfer_instructions']
                                : 'No instructions available.';
                        @endphp
                        <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line text-left">
                            {{ $instructions }}
                        </div>
                    </div>

                    <!-- Amount & Reference Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-yellow-300 dark:border-yellow-700">
                            <div class="text-xs font-medium text-yellow-700 dark:text-yellow-400 mb-1">💰 Amount to Transfer</div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">
                                ${{ number_format($pendingSubscription->amount, 2) }}
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-yellow-300 dark:border-yellow-700">
                            <div class="text-xs font-medium text-yellow-700 dark:text-yellow-400 mb-1">📋 Reference Number</div>
                            <div class="text-sm font-mono font-semibold text-gray-900 dark:text-white">
                                {{ $transaction->transaction_id ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Billing History -->
    <div class="mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Billing History</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">View and download your transaction history</p>
    </div>

    <div class="relative overflow-x-auto bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
        <div class="flex flex-col items-center justify-between p-4 space-y-3 md:flex-row md:space-y-0 md:space-x-4">
            <div class="w-full md:w-1/2">
                <form class="flex items-center">
                    <label for="invoices-search" class="sr-only">Search</label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="invoices-search" wire:model.live="search" class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search">
                    </div>
                </form>
            </div>
            <div class="flex flex-col items-stretch justify-end flex-shrink-0 w-full space-y-2 md:w-auto md:flex-row md:space-y-0 md:items-center md:space-x-3">
                <div class="flex items-center w-full space-x-3 md:w-auto">
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" id="actionsDropdownButton" class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg md:w-auto focus:outline-hidden hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700" type="button">
                            <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                            </svg>
                            Actions
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 mt-2">
                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                <li>
                                    <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Export CSV</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" id="filterDropdownButton" class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg md:w-auto focus:outline-hidden hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="w-4 h-4 mr-2 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                            </svg>
                            Filter
                            <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute z-10 w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700 mt-2 right-0">
                            <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Status</h6>
                            <ul class="space-y-2 text-sm">
                                <li class="flex items-center">
                                    <input id="completed" type="checkbox" wire:model.live="statusFilter" value="completed" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                    <label for="completed" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Completed</label>
                                </li>
                                <li class="flex items-center">
                                    <input id="pending" type="checkbox" wire:model.live="statusFilter" value="pending" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                    <label for="pending" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Pending</label>
                                </li>
                                <li class="flex items-center">
                                    <input id="failed" type="checkbox" wire:model.live="statusFilter" value="failed" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                    <label for="failed" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Failed</label>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Invoice ID</th>
                    <th scope="col" class="px-6 py-3">Date</th>
                    <th scope="col" class="px-6 py-3">Description</th>
                    <th scope="col" class="px-6 py-3">Amount</th>
                    <th scope="col" class="px-6 py-3">Payment Method</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 font-mono text-xs">{{ $transaction->invoice_number }}</td>
                        <td class="px-6 py-4">{{ $transaction->transaction_date->format('M j, Y') }}</td>
                        <td class="px-6 py-4">
                            @if($transaction->subscription && $transaction->subscription->plan)
                                {{ $transaction->subscription->plan->name }} Plan - Monthly Subscription
                            @else
                                {{ ucfirst($transaction->type) }}
                            @endif
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">${{ number_format($transaction->amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $transaction->payment_method }}</td>
                        <td class="px-6 py-4">
                            @if($transaction->status === 'completed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Completed</span>
                            @elseif($transaction->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Pending</span>
                            @elseif($transaction->status === 'failed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Failed</span>
                            @elseif($transaction->status === 'refunded')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">Refunded</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('user.invoices.view', $transaction) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
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
</div>
