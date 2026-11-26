{{-- Bank Transfer Plugin Custom Configuration View --}}
{{-- Available variables: $config (array), $pluginName (string), $pluginSlug (string) --}}

{{-- Instructions Section --}}
<div class="mb-6">
    <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
        <div class="flex">
            <svg class="flex-shrink-0 inline w-4 h-4 mr-3 mt-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <div>
                <span class="font-medium">Manual Approval Required</span>
                <p class="mt-1">Bank transfer payments require manual verification. Users will see the instructions below after initiating payment. Admins must approve or decline payments at <a href="{{ route('admin.transactions') }}" wire:navigate class="underline hover:no-underline">Transactions</a>.</p>
            </div>
        </div>
    </div>

    <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Bank Transfer Instructions</h4>

    {{-- Instructions Textarea --}}
    <div class="mb-4">
        <label for="instructions" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Instructions for Users
            <span class="text-red-500">*</span>
        </label>
        <textarea
            id="instructions"
            wire:model="config.instructions"
            rows="10"
            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Enter your bank account details and transfer instructions here...

Example:

Bank Name: ABC Bank
Account Name: Your Company Name
Account Number: 123456789
Swift Code: ABCDEFGH
Reference: [User will see their subscription ID]

Please include your subscription ID in the payment reference and email proof of payment to billing@yourcompany.com"></textarea>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            These instructions will be displayed to users after they select a plan. Include bank account details, reference format, and any verification steps.
        </p>
        @error('config.instructions')
            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- Workflow Information --}}
<div class="p-4 mb-4 text-sm text-gray-800 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-300" role="alert">
    <h5 class="mb-2 font-semibold">How Bank Transfer Payments Work:</h5>
    <ol class="list-decimal list-inside space-y-1 ml-2">
        <li>User selects a plan and chooses Bank Transfer as payment method</li>
        <li>A pending subscription and transaction are created</li>
        <li>User sees the instructions you configured above</li>
        <li>User completes the bank transfer manually</li>
        <li>Admin receives notification of pending payment</li>
        <li>Admin approves payment → subscription activated and credits refilled</li>
        <li>Admin declines payment → subscription and transaction removed</li>
    </ol>
</div>

{{-- Supported Features --}}
<div class="mb-4">
    <h4 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Features</h4>
    <ul class="space-y-1 text-xs text-gray-600 dark:text-gray-400 list-disc list-inside ml-2">
        <li>✅ All currencies supported</li>
        <li>✅ All billing intervals supported (daily, weekly, monthly, yearly)</li>
        <li>✅ Simple setup - no API credentials required</li>
        <li>✅ Manual verification for security</li>
        <li>✅ No transaction fees or third-party dependency</li>
        <li>⚠️ Requires admin approval for each payment</li>
        <li>⚠️ No automatic subscription renewal</li>
    </ul>
</div>
