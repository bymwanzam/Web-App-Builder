<div class="bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700" x-data="{ showPassword: false }">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Email Configuration</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure email settings and SMTP server</p>
    </div>
    <form wire:submit="save" class="p-6 space-y-4">
        <!-- Mail From Name -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mail From Name</label>
            <input type="text" wire:model="mail_from_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            @error('mail_from_name') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
        </div>

        <!-- Mail From Address -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mail From Address</label>
            <input type="email" wire:model="mail_from_address" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            @error('mail_from_address') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
        </div>

        <!-- Mail Driver Selection Toggle -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mail Driver</label>
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium {{ $mail_mailer === 'sendmail' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">PHP Mail</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" @click="$wire.set('mail_mailer', $wire.mail_mailer === 'smtp' ? 'sendmail' : 'smtp')" {{ $mail_mailer === 'smtp' ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-blue-600"></div>
                </label>
                <span class="text-sm font-medium {{ $mail_mailer === 'smtp' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">SMTP</span>
            </div>
            @error('mail_mailer') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
        </div>

        <!-- PHP Mail Info Section -->
        <div x-show="$wire.mail_mailer === 'sendmail'" x-transition class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm text-blue-800 dark:text-blue-300">PHP Mail uses your server's built-in mail configuration (sendmail/postfix). No additional setup required in this panel.</p>
                    <p class="mt-2 text-sm text-amber-700 dark:text-amber-400">
                        <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Note: Some hosting providers disable PHP's mail() function. If emails fail to send, switch to SMTP.
                    </p>
                </div>
            </div>
        </div>

        <!-- SMTP Configuration Section -->
        <div x-show="$wire.mail_mailer === 'smtp'" x-transition class="space-y-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">SMTP Server Configuration</h4>

            <!-- Mail Host -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">SMTP Host</label>
                <input type="text" wire:model="mail_host" placeholder="smtp.mailtrap.io" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                @error('mail_host') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
            </div>

            <!-- Mail Port -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">SMTP Port</label>
                <input type="number" wire:model="mail_port" placeholder="2525" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                @error('mail_port') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
            </div>

            <!-- Mail Username -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">SMTP Username</label>
                <input type="text" wire:model="mail_username" placeholder="username" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                @error('mail_username') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
            </div>

            <!-- Mail Password -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">SMTP Password</label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" wire:model="mail_password" placeholder="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pr-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('mail_password') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
            </div>

            <!-- Mail Encryption -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Encryption</label>
                <select wire:model="mail_encryption" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="tls">TLS</option>
                    <option value="ssl">SSL</option>
                    <option value="none">None</option>
                </select>
                @error('mail_encryption') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Admin Notifications Section -->
        <div class="space-y-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Admin Notifications</h4>
            <p class="text-xs text-gray-500 dark:text-gray-400">Configure where admin payment notifications are sent</p>

            <!-- Admin Notification Email -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Admin Notification Email</label>
                <input type="email" wire:model="admin_notification_email" placeholder="admin@example.com" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email address to receive admin notifications. Also used for test emails.</p>
                @error('admin_notification_email') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
            </div>

            <!-- Admin Notification Events -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Notification Events</label>
                <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">Select which events should trigger admin notifications</p>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="admin_notification_events" value="subscription_activated" id="event_subscription_activated" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="event_subscription_activated" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Subscription Activated</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="admin_notification_events" value="payment_completed" id="event_payment_completed" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="event_payment_completed" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Payment Completed</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="admin_notification_events" value="payment_failed" id="event_payment_failed" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="event_payment_failed" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Payment Failed</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="admin_notification_events" value="subscription_cancelled" id="event_subscription_cancelled" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="event_subscription_cancelled" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Subscription Cancelled</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="admin_notification_events" value="subscription_expired" id="event_subscription_expired" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="event_subscription_expired" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Subscription Expired</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="admin_notification_events" value="bank_transfer_pending" id="event_bank_transfer_pending" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="event_bank_transfer_pending" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Bank Transfer Pending</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="admin_notification_events" value="user_registered" id="event_user_registered" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="event_user_registered" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">User Registered</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="admin_notification_events" value="build_completed" id="event_build_completed" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="event_build_completed" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Build Completed</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="admin_notification_events" value="build_failed" id="event_build_failed" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="event_build_failed" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Build Failed</label>
                    </div>
                </div>
                @error('admin_notification_events') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if(config('app.demo'))
                <button type="button" disabled title="Disabled in demo mode" class="text-white bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 cursor-not-allowed opacity-50">
                    {{ __('Save') }}
                </button>
                <button type="button" disabled title="Disabled in demo mode" class="text-gray-900 bg-white border border-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 inline-flex items-center cursor-not-allowed opacity-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ __('Send Test Email') }}
                </button>
            @else
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-hidden dark:focus:ring-blue-800">
                    {{ __('Save') }}
                </button>
                <button type="button" wire:click="sendTestEmail" wire:loading.attr="disabled" class="text-gray-900 bg-white border border-gray-300 focus:outline-hidden hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700 inline-flex items-center">
                    <svg wire:loading.remove wire:target="sendTestEmail" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <svg wire:loading wire:target="sendTestEmail" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="sendTestEmail">{{ __('Send Test Email') }}</span>
                    <span wire:loading wire:target="sendTestEmail">{{ __('Sending...') }}</span>
                </button>
            @endif
        </div>
    </form>
</div>
