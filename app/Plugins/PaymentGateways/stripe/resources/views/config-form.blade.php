{{-- Stripe Plugin Custom Configuration View --}}
{{-- Available variables: $config (array), $pluginName (string), $pluginSlug (string) --}}

{{-- Help Banner --}}
<div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
    <div class="flex">
        <svg class="flex-shrink-0 inline w-4 h-4 mr-3 mt-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
        </svg>
        <div>
            <span class="font-medium">Get Started with Stripe</span>
            <p class="mt-1">Get your API keys from <a href="https://dashboard.stripe.com/apikeys" target="_blank" class="underline hover:no-underline">Stripe Dashboard → Developers → API Keys</a></p>
        </div>
    </div>
</div>

{{-- API Credentials Section --}}
<div class="mb-6">
    <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">API Credentials</h4>

    {{-- Secret Key --}}
    <div class="mb-4">
        <label for="secret_key" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Secret Key
            <span class="text-red-500">*</span>
        </label>
        <input type="password"
               id="secret_key"
               wire:model="config.secret_key"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
               placeholder="sk_live_...">
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Use test key (sk_test_) for testing, live key (sk_live_) for production</p>
        @error('config.secret_key')
            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- Webhook Configuration Section --}}
<div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
    <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Webhook Configuration</h4>

    {{-- Webhook URL Display --}}
    <div class="mb-4" x-data="{
        copied: false,
        webhookUrl: '{{ route('payment-gateways.webhook', ['plugin' => 'stripe']) }}',
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
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Add this URL in Stripe Dashboard → Developers → Webhooks</p>
    </div>

    {{-- Webhook Secret --}}
    <div class="mb-4">
        <label for="webhook_secret" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Webhook Signing Secret
            <span class="text-red-500">*</span>
        </label>
        <input type="password"
               id="webhook_secret"
               wire:model="config.webhook_secret"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
               placeholder="whsec_...">
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Get this after creating webhook endpoint in Stripe Dashboard</p>
        @error('config.webhook_secret')
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
                    <span class="font-medium">Warning:</span> Your site is not using HTTPS. Stripe requires HTTPS for webhooks and production usage.
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Required Events --}}
<div class="mb-4">
    <h4 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Required Webhook Events</h4>
    <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">Select these events when creating your webhook endpoint:</p>
    <ul class="space-y-1 text-xs text-gray-600 dark:text-gray-400 list-disc list-inside ml-2">
        <li><code class="px-1.5 py-0.5 font-mono bg-gray-100 dark:bg-gray-700 rounded">customer.subscription.created</code></li>
        <li><code class="px-1.5 py-0.5 font-mono bg-gray-100 dark:bg-gray-700 rounded">customer.subscription.updated</code></li>
        <li><code class="px-1.5 py-0.5 font-mono bg-gray-100 dark:bg-gray-700 rounded">customer.subscription.deleted</code></li>
        <li><code class="px-1.5 py-0.5 font-mono bg-gray-100 dark:bg-gray-700 rounded">invoice.paid</code></li>
        <li><code class="px-1.5 py-0.5 font-mono bg-gray-100 dark:bg-gray-700 rounded">invoice.payment_failed</code></li>
        <li><code class="px-1.5 py-0.5 font-mono bg-gray-100 dark:bg-gray-700 rounded">charge.refunded</code></li>
    </ul>
</div>

{{-- Supported Features --}}
<div class="mb-4">
    <h4 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Features</h4>
    <ul class="space-y-1 text-xs text-gray-600 dark:text-gray-400 list-disc list-inside ml-2">
        <li>✅ 135+ currencies supported globally</li>
        <li>✅ All billing intervals supported</li>
        <li>✅ Automatic subscription renewal</li>
        <li>✅ Customer management</li>
        <li>✅ Webhook signature verification</li>
        <li>✅ Refund handling</li>
        <li>✅ Strong Customer Authentication (SCA) compliant</li>
    </ul>
</div>
