<div class="bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">GDPR & Privacy Settings</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure data protection and privacy compliance</p>
    </div>

    <form wire:submit="save" class="p-6 space-y-6">
        {{-- Feature Toggles --}}
        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Feature Toggles</h4>
            <div class="space-y-4">
                <label class="flex items-center gap-3">
                    <input type="checkbox" wire:model="cookieConsentEnabled" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                    <div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Cookie Consent Banner</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Show cookie consent banner to new visitors</p>
                    </div>
                </label>

                <label class="flex items-center gap-3">
                    <input type="checkbox" wire:model="dataExportEnabled" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                    <div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">User Data Export</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Allow users to request a copy of their data</p>
                    </div>
                </label>

                <label class="flex items-center gap-3">
                    <input type="checkbox" wire:model="accountDeletionEnabled" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                    <div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">User Account Deletion</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Allow users to request account deletion</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Policy Versions --}}
        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Policy Versions</h4>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Updating policy versions will trigger re-consent for users.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Privacy Policy</label>
                    <input type="text" wire:model="privacyPolicyVersion" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('privacyPolicyVersion') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Terms of Service</label>
                    <input type="text" wire:model="termsPolicyVersion" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('termsPolicyVersion') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cookie Policy</label>
                    <input type="text" wire:model="cookiePolicyVersion" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('cookiePolicyVersion') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Data Retention --}}
        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Data Retention Periods (Days)</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Transactions</label>
                    <input type="number" wire:model="retentionTransactions" min="365" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Min 365 days (legal requirement)</p>
                    @error('retentionTransactions') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Inactive Accounts</label>
                    <input type="number" wire:model="retentionInactiveAccounts" min="30" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('retentionInactiveAccounts') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Expired Builds</label>
                    <input type="number" wire:model="retentionExpiredBuilds" min="7" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('retentionExpiredBuilds') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Audit Logs</label>
                    <input type="number" wire:model="retentionAuditLogs" min="30" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('retentionAuditLogs') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Data Exports</label>
                    <input type="number" wire:model="retentionDataExports" min="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('retentionDataExports') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Grace Periods --}}
        <div class="pb-6">
            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Grace Periods & Rate Limits</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Account Deletion Grace Period (Days)</label>
                    <input type="number" wire:model="accountDeletionGraceDays" min="1" max="30" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Time before account is permanently deleted</p>
                    @error('accountDeletionGraceDays') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Data Export Rate Limit (Hours)</label>
                    <input type="number" wire:model="dataExportRateLimitHours" min="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Minimum time between export requests</p>
                    @error('dataExportRateLimitHours') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        @if(config('app.demo'))
            <button type="button" disabled title="Disabled in demo mode" class="text-white bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 cursor-not-allowed opacity-50">
                {{ __('Save') }}
            </button>
        @else
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-hidden dark:focus:ring-blue-800">
                {{ __('Save') }}
            </button>
        @endif
    </form>
</div>
