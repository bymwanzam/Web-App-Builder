{{-- PayPal Plugin Custom Configuration View --}}
{{-- Available variables: $config (array), $pluginName (string), $pluginSlug (string) --}}

{{-- API Credentials Section --}}
<div class="mb-6">
    <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">API Credentials</h4>

    {{-- Client ID --}}
    <div class="mb-4">
        <label for="client_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Client ID
            <span class="text-red-500">*</span>
        </label>
        <input type="text"
               id="client_id"
               wire:model="config.client_id"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
               placeholder="AYSq3RDGsmBLJE-otTkBtM-jBRd1TCQwFf9RGfwddNXWz0uFU9ztymylOhRS">
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Get this from PayPal Developer Dashboard</p>
        @error('config.client_id')
            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Client Secret --}}
    <div class="mb-4">
        <label for="client_secret" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Client Secret
            <span class="text-red-500">*</span>
        </label>
        <input type="password"
               id="client_secret"
               wire:model="config.client_secret"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
               placeholder="••••••••">
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Keep this secret and never share it publicly</p>
        @error('config.client_secret')
            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Sandbox Mode Toggle --}}
    <div class="flex items-center mb-4">
        <input type="checkbox"
               id="sandbox"
               wire:model="config.sandbox"
               value="1"
               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
        <label for="sandbox" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
            Sandbox Mode
        </label>
    </div>
    <p class="ml-6 -mt-3 mb-4 text-xs text-gray-500 dark:text-gray-400">Enable for testing with PayPal Sandbox. Disable for live transactions.</p>
</div>

{{-- Webhook Configuration Section --}}
<div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
    <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Webhook Configuration</h4>

    {{-- Webhook URL Display --}}
    <div class="mb-4" x-data="{
        copied: false,
        webhookUrl: '{{ route('payment-gateways.webhook', ['plugin' => 'paypal']) }}',
        copyToClipboard() {
            const input = $refs.webhookInput;
            input.select();
            input.setSelectionRange(0, 99999);
            try {
                document.execCommand('copy');
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            } catch (err) {
                alert('Failed to copy. Please manually select and copy the URL.');
            }
        }
    }">
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Webhook URL
        </label>
        <div class="flex gap-2">
            <input type="text"
                   readonly
                   x-ref="webhookInput"
                   x-model="webhookUrl"
                   class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400">
            <button type="button"
                    @click="copyToClipboard()"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-hidden dark:focus:ring-blue-800 whitespace-nowrap">
                <span x-show="!copied">Copy</span>
                <span x-show="copied" x-cloak>Copied!</span>
            </button>
        </div>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Add this URL to your PayPal webhook settings</p>
    </div>

    {{-- Webhook ID --}}
    <div class="mb-4">
        <label for="webhook_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Webhook ID
            <span class="text-red-500">*</span>
        </label>
        <input type="text"
               id="webhook_id"
               wire:model="config.webhook_id"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
               placeholder="5YF12345ABCDE">
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Webhook ID from PayPal Dashboard for signature verification (required for security)</p>
        @error('config.webhook_id')
            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- SSL Warning --}}
    @if(!request()->secure())
        <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-400" role="alert">
            <div class="flex">
                <svg class="flex-shrink-0 inline w-4 h-4 mr-3 mt-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <div>
                    <span class="font-medium">Warning:</span> Your site is not using HTTPS. PayPal webhooks require a secure HTTPS connection. Please enable SSL/TLS before going live.
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Required Events --}}
<div class="mb-4">
    <h4 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Required Webhook Events</h4>
    <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">Make sure to enable these events in your PayPal webhook configuration:</p>
    <ul class="space-y-1 text-xs text-gray-600 dark:text-gray-400 list-disc list-inside ml-2">
        <li><code class="px-1.5 py-0.5 font-mono bg-gray-100 dark:bg-gray-700 rounded">BILLING.SUBSCRIPTION.CREATED</code></li>
        <li><code class="px-1.5 py-0.5 font-mono bg-gray-100 dark:bg-gray-700 rounded">BILLING.SUBSCRIPTION.ACTIVATED</code></li>
        <li><code class="px-1.5 py-0.5 font-mono bg-gray-100 dark:bg-gray-700 rounded">BILLING.SUBSCRIPTION.CANCELLED</code></li>
        <li><code class="px-1.5 py-0.5 font-mono bg-gray-100 dark:bg-gray-700 rounded">BILLING.SUBSCRIPTION.EXPIRED</code></li>
        <li><code class="px-1.5 py-0.5 font-mono bg-gray-100 dark:bg-gray-700 rounded">BILLING.SUBSCRIPTION.SUSPENDED</code></li>
        <li><code class="px-1.5 py-0.5 font-mono bg-gray-100 dark:bg-gray-700 rounded">PAYMENT.SALE.COMPLETED</code></li>
    </ul>
</div>
